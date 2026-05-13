<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Exceptions\GeneralException;
use App\Filters\Common\Auth\UserFilter as AppUserFilter;
use App\Filters\Core\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\UserRequest;
use App\Jobs\User\UserDeleted;
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
                ->select(['id', 'first_name', 'last_name', 'email', 'created_by', 'status_id', 'created_at', 'super_partner_id'])
                ->with('roles:id,name,is_admin,is_default,type_id', 'status', 'profilePicture')
                ->latest()
        ))->filter();

        // If the authenticated user is a super_partner, only show their own users
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $query = $query->where('super_partner_id', $superPartner->id)
                                ->where('user_type', 'admin_partner');
            }
        }else{
            $query = $query->where('user_type', 'admin');
        }

        return $query->paginate(request()->get('per_page', 10));
    }


    /**
     * @param UserRequest $request
     * @return array
     */
    public function store(UserRequest $request)
    {
        $attributes = [];

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
