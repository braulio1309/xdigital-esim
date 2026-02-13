<?php

namespace App\Http\Controllers\App\Users;

use App\Exceptions\GeneralException;
use App\Filters\Core\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
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
            'user_type' => 'required|in:admin,beneficiario,cliente',
            'roles' => 'nullable|array',
            'beneficiario_nombre' => 'required_if:user_type,beneficiario',
            'beneficiario_descripcion' => 'required_if:user_type,beneficiario',
            'cliente_nombre' => 'required_if:user_type,cliente',
            'cliente_apellido' => 'required_if:user_type,cliente',
        ]);

        // Get active status
        $status = \App\Models\Core\Status::findByNameAndType('status_active', 'user');

        // Create user
        $user = $this->service->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type' => $request->user_type,
            'status_id' => $status ? $status->id : 1,
        ]);

        // Assign roles if provided
        if ($request->roles) {
            $user->model->roles()->sync($request->roles);
        }

        // Create Beneficiario record if user type is beneficiario
        if ($request->user_type === 'beneficiario') {
            \App\Models\App\Beneficiario\Beneficiario::create([
                'user_id' => $user->model->id,
                'nombre' => $request->beneficiario_nombre,
                'descripcion' => $request->beneficiario_descripcion,
                'codigo' => strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)),
            ]);
        }

        // Create Cliente record if user type is cliente
        if ($request->user_type === 'cliente') {
            \App\Models\App\Cliente\Cliente::create([
                'user_id' => $user->model->id,
                'nombre' => $request->cliente_nombre,
                'apellido' => $request->cliente_apellido,
                'email' => $request->email,
            ]);
        }

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
