<?php

namespace App\Http\Controllers\Core\Auth;

use App\Exceptions\GeneralException;
use App\Helpers\App\Traits\ReCaptchaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\UserRegistrationRequest;
use App\Mail\Core\User\UserVerificationMail;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Core\Auth\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserRegistrationController extends Controller
{
    use ReCaptchaHelper;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        if (config('settings.application.registration') == 'on') {
            $recaptcha = $this->getReCaptcha();
            return view('auth.register', $recaptcha);
        }

        throw new GeneralException(trans('default.action_not_allowed'). '. '. trans('default.enable_registration_warning'));

    }

    public function register(UserRegistrationRequest $request)
    {
        DB::transaction(function () use ($request){
            $remember_token = base64_encode($request->email.'-remember-token');
            $this->service
                ->create([
                    'status_id' => resolve(StatusRepository::class)->userInactive(),
                    'remember_token' => $remember_token,
                ])
                ->assignRole('Moderator');

            try {
                Mail::to($request->email)
                    ->send(new UserVerificationMail($this->service->getModel(), $remember_token));
            }catch (\Exception $exception){
                throw $exception;
            }
        });

        return response()->json([
            'status' => true,
            'message' => trans('default.a_verification_mail_sent_to_the_user')
        ]);
    }

    public function verify()
    {
        $user = User::query()
            ->where('email', request('email'))
            ->where('remember_token', request('token'))
            ->first();

        throw_if(!$user,
            new GeneralException(trans('default.action_not_allowed'))
        );
        $user->update([
            'status_id' => resolve(StatusRepository::class)->userActive(),
            'remember_token' => null
        ]);

        auth()->login($user);


        return view('auth.verified');
    }
}
