<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Exceptions\GeneralException;
use App\Filters\Common\Auth\UserFilter as AppUserFilter;
use App\Filters\Core\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\UserRequest;
use App\Jobs\User\UserDeleted;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use App\Notifications\Core\User\UserNotification;
use App\Services\Core\Auth\UserService;

/**
 * Class UserController.
 */
class UserController extends Controller
{

    /**
     * UserController constructor.
     *
     * @param UserService $user
     * @param UserFilter $filter
     */
    public function __construct(UserService $user, UserFilter $filter)
    {
        $this->service = $user;
        $this->filter = $filter;
    }


    /**
     *
     * @return mixed
     */
    public function index()
    {
        $query = (new AppUserFilter(
            $this->service
                ->filters($this->filter)
                ->select(['id', 'first_name', 'last_name', 'email', 'created_by', 'status_id', 'created_at', 'super_partner_id', 'beneficiario_id', 'user_sub_type', 'user_type'])
                ->with(
                    'roles:id,name,is_admin,is_default,type_id',
                    'status',
                    'profilePicture',
                    'affiliatedSuperPartner:id,nombre',
                    'affiliatedBeneficiario:id,nombre'
                )
                ->latest()
        ))->filter()->where('user_type', '!=', 'cliente');

        $authUser = auth()->user();

        if ($superPartner = $this->resolveScopedSuperPartner($authUser)) {
            $query = $query->where(function ($builder) use ($superPartner) {
                $builder->where('super_partner_id', $superPartner->id);

                if ($superPartner->user_id) {
                    $builder->orWhere('id', $superPartner->user_id);
                }
            });
        } elseif ($beneficiario = $this->resolveScopedBeneficiario($authUser)) {
            $query = $query->where(function ($builder) use ($beneficiario) {
                $builder->where('beneficiario_id', $beneficiario->id);

                if ($beneficiario->user_id) {
                    $builder->orWhere('id', $beneficiario->user_id);
                }
            });
        } elseif (!$authUser || $authUser->user_type !== 'admin') {
            $query = $query->where('user_type', 'admin');
        }

        return $query->paginate(request()->get('per_page', 10));
    }

    private function resolveScopedSuperPartner(?User $authUser): ?SuperPartner
    {
        if (!$authUser) {
            return null;
        }

        if ($authUser->user_type === 'super_partner') {
            return SuperPartner::where('user_id', $authUser->id)->first()
                ?? ($authUser->super_partner_id ? SuperPartner::find($authUser->super_partner_id) : null);
        }

        if ($authUser->user_type === 'admin_partner' && $authUser->super_partner_id) {
            return SuperPartner::find($authUser->super_partner_id);
        }

        return null;
    }

    private function resolveScopedBeneficiario(?User $authUser): ?Beneficiario
    {
        if (!$authUser) {
            return null;
        }

        if ($authUser->user_type === 'beneficiario') {
            return Beneficiario::where('user_id', $authUser->id)->first()
                ?? ($authUser->beneficiario_id ? Beneficiario::find($authUser->beneficiario_id) : null);
        }

        if ($authUser->user_type === 'admin_beneficiario' && $authUser->beneficiario_id) {
            return Beneficiario::find($authUser->beneficiario_id);
        }

        return null;
    }


    /**
     * @param UserRequest $request
     * @return array
     */
    public function store(UserRequest $request)
    {
        $attributes = [];
        $userSubType = $request->get('user_sub_type');

        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = SuperPartner::where('user_id', auth()->id())->first();

            if ($superPartner) {
                $attributes = [
                    'super_partner_id' => $superPartner->id,
                    'user_type' => 'admin_partner',
                ];

                $request->merge(['roles' => 'Super Partner']);
            }
        } else {
            $attributes = ['user_type' => 'admin'];
            $request->merge(['roles' => $request->get('roles', 'App admin')]);
        }

        if ($userSubType) {
            $attributes['user_sub_type'] = $userSubType;
        }

        $this->service
            ->create($attributes)
            ->when($request->get('roles'), function (UserService $service) use ($request) {
                $service->assignRole($request->get('roles'));
            })->notify('user_created');

        return created_responses('user');
    }


    /**
     * @param User $user
     * @return User
     */
    public function show(User $user)
    {
        return $user->load('roles');
    }


    /**
     * @param UserRequest $request
     * @param User $user
     * @return array
     * @throws GeneralException
     */
    public function update(UserRequest $request, User $user)
    {
        $this->service
            ->setModel($user)
            ->beforeUpdate()
            ->update();

        return updated_responses('user');
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return array
     * @throws GeneralException
     */
    public function destroy(UserRequest $request, User $user)
    {
        $this->service
            ->setModel($user)
            ->beforeDelete()
            ->delete($user);

        notify()
            ->on('user_deleted')
            ->with((object)$user->toArray())
            ->send(UserNotification::class);

        UserDeleted::dispatch((object) $user->toArray())
            ->onConnection('sync');

        return deleted_responses('user');
    }
}
