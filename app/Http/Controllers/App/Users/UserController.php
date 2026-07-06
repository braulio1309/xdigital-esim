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
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(UserService $service, UserFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        $query = $this->service->with('status:id,name,type');

        $user = auth()->user();
        
        if ($user) {
            if ($user->user_type === 'super_partner') {
                // Super partner: solo sus propios sub-usuarios (admin_partner)
                $superPartnerId = $user->superPartner->id ?? 0;
                $query = $query->where('super_partner_id', $superPartnerId);
            } elseif ($user->user_type === 'admin_partner') {
                // Sub-usuario de super partner: muestra compañeros del mismo super partner
                $query = $query->where('super_partner_id', $user->super_partner_id ?? 0);
            } elseif ($user->user_type === 'beneficiario') {
                // Usuario principal de un partner (beneficiario): solo sus sub-usuarios
                $beneficiarioId = $user->beneficiario->id ?? 0;
                $query = $query->where('beneficiario_id', $beneficiarioId);
            } elseif ($user->user_type === 'admin_beneficiario') {
                // Sub-usuario de un partner (beneficiario): muestra compañeros del mismo partner
                $query = $query->where('beneficiario_id', $user->beneficiario_id ?? 0);
            }
        }

        return $query
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
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'user_sub_type' => 'nullable|in:directivo,atencion_cliente',
        ]);

        $normalizedEmail = mb_strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $normalizedEmail]);

        // Get active status
        $status = \App\Models\Core\Status::findByNameAndType('status_active', 'user');
        
        if (!$status) {
            throw new \App\Exceptions\GeneralException('Active status not found in the system');
        }

        $userSubType = $request->input('user_sub_type') ?: 'directivo';
        $attributes = [];
        $roleName = null;
        
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $attributes = [
                    'super_partner_id' => $superPartner->id,
                    'user_type' => 'admin_partner',
                    'user_sub_type' => $userSubType,
                ];
                $roleName = 'Super Partner';
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $attributes = [
                    'beneficiario_id' => $beneficiario->id,
                    'user_type' => 'admin_beneficiario',
                    'user_sub_type' => $userSubType,
                ];
                $roleName = 'App admin';
            }
        } elseif (auth()->check() && in_array(auth()->user()->user_type, ['admin_partner', 'admin_beneficiario'])) {
            // Sub-users created by directivo admin_partner or admin_beneficiario
            $creator = auth()->user();
            if ($creator->user_type === 'admin_partner' && $creator->super_partner_id) {
                $attributes = [
                    'super_partner_id' => $creator->super_partner_id,
                    'user_type' => 'admin_partner',
                    'user_sub_type' => $userSubType,
                ];
                $roleName = 'Super Partner';
            } elseif ($creator->user_type === 'admin_beneficiario' && $creator->beneficiario_id) {
                $attributes = [
                    'beneficiario_id' => $creator->beneficiario_id,
                    'user_type' => 'admin_beneficiario',
                    'user_sub_type' => $userSubType,
                ];
                $roleName = 'App admin';
            }
        } else if (auth()->check() && auth()->user()->user_type === 'admin') {
            $attributes = [
                'user_type' => 'admin',
                'user_sub_type' => $userSubType,
            ];
            $roleName = 'App admin';
        }

        $request->merge(array_merge($attributes, [
            'roles' => $roleName,
        ]));

        $existingUser = User::query()->whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if ($existingUser) {
            if (!$existingUser->hasRole('cliente')) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo ya pertenece a otro usuario.',
                ]);
            }

            $existingUser->fill([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'password' => $request->input('password'),
                'status_id' => $status->id,
                'user_type' => $request->input('user_type', $existingUser->user_type),
                'user_sub_type' => $request->input('user_sub_type', $existingUser->user_sub_type),
                'super_partner_id' => $request->input('super_partner_id', $existingUser->super_partner_id),
                'beneficiario_id' => $request->input('beneficiario_id', $existingUser->beneficiario_id),
            ]);
            $existingUser->save();

            if ($roleName && !$existingUser->hasRole($roleName)) {
                $existingUser->assignRole($roleName);
            }

            return created_responses('user');
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
