<?php


namespace App\Services\Core\Auth;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\FileHandler;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Helpers;
use App\Hooks\User\AfterLogin;
use App\Hooks\User\BeforeLogin;
use App\Mail\Core\User\LoginVerificationCodeMail;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Auth\Traits\HasUserActions;
use App\Services\Core\BaseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService extends BaseService
{
    use FileHandler, Helpers, HasWhen, HasUserActions;

    public $role;

    protected $twoFactorSessionKey = 'core.auth.user.two_factor_login';

    public function __construct(User $user, Role $role)
    {
        $this->model = $user;
        $this->role = $role;
    }

    public function create($attributes = [])
    {
        $status = Status::findByNameAndType('status_active', 'user')->id;
        $attributes = array_merge(['status_id' => $status], $attributes);

        $payload = $this->getFillAble(array_merge(request()->only(
            'first_name',
            'last_name',
            'email',
            'password',
            'user_type',
            'user_sub_type'
        ), $attributes));

        $email = mb_strtolower(trim((string) data_get($payload, 'email')));
        $payload['email'] = $email;

        $existingUser = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existingUser) {
            if (!$existingUser->hasRole('cliente')) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo ya pertenece a otro usuario.',
                ]);
            }

            $existingUser->fill($payload);
            $existingUser->save();

            $this->setModel($existingUser);

            return $this;
        }

        parent::save($payload);

        return $this;
    }


    public function update()
    {
        $this->model->fill($this->getFillAble(request()->only('first_name', 'last_name', 'status_id')));

        throw_if(
            $this->model->isDirty('status_id') && ($this->model->isAppAdmin() || $this->model->id == auth()->id()),
            new GeneralException(trans('default.action_not_allowed'))
        );

        $this->when($this->model->isDirty(), function (UserService $service) {
            $service->notify('user_updated');
        });

        $this->model->save();

        $this->when(request()->get('roles'), function (UserService $service) {
            $service->assignRole(request()->get('roles'));
        });

        return $this->model;
    }

    public function assignRole($roles)
    {
        $this->model->assignRole($roles);

        return $this;
    }

    public function delete(User $user)
    {
        if ($user->isAppAdmin() && !$user->isInvited())
            throw new GeneralException(trans('default.action_not_allowed'));

        if ($user->id == auth()->id())
            throw new GeneralException(trans('default.cant_delete_own_account'));

        return $user->forceDelete();
    }


    public function attachRole()
    {
        if ((($this->model->isAppAdmin() && !auth()->user()->isAppAdmin()) || $this->model->id == auth()->id()) && !$this->model->isInvited())
            throw new GeneralException(trans('default.action_not_allowed'));

        $roles = $this->checkMakeArray(request('roles'));
        $this->model->roles()->sync(array_unique($roles));
        return $this->model;
    }

    public function detachRole()
    {
        if (($this->model->isAppAdmin() && !auth()->user()->isAppAdmin()) || $this->model->id == auth()->id())
            throw new GeneralException(trans('default.action_not_allowed'));

        $roles = $this->checkMakeArray(request('roles'));
        $this->model->roles()->detach($roles);
        $this->model->load('roles');
        return $this->model;
    }

    public function changeSetting($user = null)
    {
        $this->setModel($user ?? auth()->user());

        $this->attachSettings(
            request()->only('gender', 'date_of_birth', 'address', 'contact')
        );

        return true;
    }

    public function attachSettings($settings)
    {
        $settings_models = [];
        foreach ($settings as $key => $setting) {
            $setting_model = $this->model
                ->settings()
                ->firstOrNew([
                    'name' => $key,
                    'context' => 'user'
                ]);

            $setting_model->fill([
                'value' => $key == 'date_of_birth' ? Carbon::parse($setting)->format('Y-m-d') : $setting,
                'public' => 1
            ]);

            array_push($settings_models, $setting_model);
        }

        return $this->model
            ->settings()
            ->saveMany($settings_models);
    }

    public function storeThumbnail($user = null)
    {
        $user = $user ?? auth()->user();

        if (optional($user->profilePicture)->path){
            $this->deleteImage(optional($user->profilePicture)->path);
        }

        $file_path = $this->uploadImage(
            request()->file('profile_picture'),
            config('file.profile_picture.folder'),
            config('file.profile_picture.height')
        );

        $user->profilePicture()->updateOrCreate([
            'type' => 'profile_picture'
        ], [
            'path' => $file_path
        ]);

        return $user->load('profilePicture');

    }


    public function login()
    {
        /** @var User $user */
        $user = $this->model::findByEmail(request()->get('email'));

        BeforeLogin::new(true)
            ->setModel($user)
            ->handle();

        if (!$user->roles->count())
            throw new AuthenticationException(trans('default.no_roles_found'));

        if (Hash::check(request()->get('password'), optional($user)->password)) {
            return $user;
        }

        throw new AuthenticationException(
            trans('default.incorrect_user_password', [
                'password' => trans('default.password'),
                'email' => trans('default.email')
            ])
        );
    }

    public function completeLogin(User $user, $remember = false)
    {
        auth()->login($user, $remember);

        AfterLogin::new(true)
            ->setModel($user)
            ->handle();

        return $user;
    }

    public function generateTwoFactorCode()
    {
        return Str::upper(Str::random(4));
    }

    public function createTwoFactorChallenge(User $user, $remember = false)
    {
        $code = $this->generateTwoFactorCode();

        session()->put($this->twoFactorSessionKey, [
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'remember' => $remember,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
        ]);

        return $code;
    }

    public function sendTwoFactorCode(User $user, $code)
    {
        Mail::to($user->email)->send(new LoginVerificationCodeMail($user, $code));
    }

    public function verifyTwoFactorCode($code)
    {
        $challenge = $this->getTwoFactorChallenge();

        throw_if(
            !$challenge,
            new AuthenticationException(trans('default.two_factor_code_expired'))
        );

        throw_if(
            now()->timestamp > data_get($challenge, 'expires_at'),
            new AuthenticationException(trans('default.two_factor_code_expired'))
        );

        /** @var User $user */
        $user = $this->model::find(data_get($challenge, 'user_id'));

        throw_if(
            !$user,
            new AuthenticationException(trans('default.two_factor_code_expired'))
        );

        if (!Hash::check(Str::upper(trim($code)), data_get($challenge, 'code_hash'))) {
            $attempts = ((int) data_get($challenge, 'attempts', 0)) + 1;

            if ($attempts >= 5) {
                $this->clearTwoFactorChallenge();
            } else {
                $challenge['attempts'] = $attempts;
                session()->put($this->twoFactorSessionKey, $challenge);
            }

            throw new AuthenticationException(trans('default.invalid_two_factor_code'));
        }

        $this->clearTwoFactorChallenge();
        $this->completeLogin($user, (bool) data_get($challenge, 'remember', false));
        session()->regenerate();

        return $user;
    }

    public function getTwoFactorChallenge()
    {
        return session()->get($this->twoFactorSessionKey);
    }

    public function clearTwoFactorChallenge()
    {
        session()->forget($this->twoFactorSessionKey);

        return true;
    }

    public function getFormattedSettings()
    {
        return resolve(SettingRepository::class)
            ->getFormattedSettings('user', User::class, auth()->id());
    }

    public function findAndCacheUser($id)
    {
        return cache()->remember('user-'.$id, 86400, function () use ($id) {
            return $this->select('id', 'first_name', 'last_name')->with('profilePicture')
                ->find($id);
        });
    }

}
