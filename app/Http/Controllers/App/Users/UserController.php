<?php

namespace App\Http\Controllers\App\Users;

use App\Exceptions\GeneralException;
use App\Filters\Core\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\App\Beneficiario\Beneficiario;
use App\Notifications\Core\User\UserNotification;
use App\Services\Core\Auth\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(UserService $service, UserFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service->with('status:id,name,type')
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'user_sub_type' => 'nullable|in:directivo,atencion_cliente',
        ]);

        // Get active status
        $status = \App\Models\Core\Status::findByNameAndType('status_active', 'user');
        
        if (!$status) {
            throw new \App\Exceptions\GeneralException('Active status not found in the system');
        }

        $userSubType = $request->input('user_sub_type') ?: 'directivo';
        
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $request->merge([
                    'super_partner_id' => $superPartner->id,
                    'user_type' => 'admin_partner',
                    'user_sub_type' => $userSubType,
                    'roles' => 'Super Partner',
                ]);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $request->merge([
                    'beneficiario_id' => $beneficiario->id,
                    'user_type' => 'admin_beneficiario',
                    'user_sub_type' => $userSubType,
                    'roles' => 'App admin',
                ]);
            }
        } elseif (auth()->check() && in_array(auth()->user()->user_type, ['admin_partner', 'admin_beneficiario'])) {
            // Sub-users created by directivo admin_partner or admin_beneficiario
            $creator = auth()->user();
            if ($creator->user_type === 'admin_partner' && $creator->super_partner_id) {
                $request->merge([
                    'super_partner_id' => $creator->super_partner_id,
                    'user_type' => 'admin_partner',
                    'user_sub_type' => $userSubType,
                    'roles' => 'Super Partner',
                ]);
            } elseif ($creator->user_type === 'admin_beneficiario' && $creator->beneficiario_id) {
                $request->merge([
                    'beneficiario_id' => $creator->beneficiario_id,
                    'user_type' => 'admin_beneficiario',
                    'user_sub_type' => $userSubType,
                    'roles' => 'App admin',
                ]);
            }
        } else if (auth()->check() && auth()->user()->user_type === 'admin') {
            $request->merge([
                'roles' => 'App admin',
                'user_type' => 'admin',
                'user_sub_type' => $userSubType,
            ]);
        }

       $this->service
            ->create($request->all())
            ->when($request->get('roles'), function (UserService $service) use ($request) {
                $service->assignRole($request->get('roles'));
            })->notify('user_created');

        return created_responses('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, $id)
    {
        $user = $this->service->find($id);

        if ($user->status_id != 3 && $user->id != auth()->id()) {

            $this->service->where('id', $id)->update(\request()->only('status_id'));

            notify()
                ->on('user_updated')
                ->with($user)
                ->send(UserNotification::class);

            return updated_responses('user');
        } else {
            throw new GeneralException(trans('default.status_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        if ($user->delete()) {
            return deleted_responses('user');
        }
        return failed_responses();
    }

    public function getUsers()
    {
        return $this->service->with('status:id,name,type')
            ->filters($this->filter)
            ->latest()
            ->get();
    }

    public function updateUserName(Request $request, $id)
    {
        $user = $this->service->find($id);
        $this->service->where('id', $id)->update(\request()->only('first_name', 'last_name'));

        notify()
            ->on('user_updated')
            ->with($user)
            ->send(UserNotification::class);

        return updated_responses('user');
    }
}
