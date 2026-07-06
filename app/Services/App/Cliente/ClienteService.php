<?php

namespace App\Services\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Status;
use App\Services\App\AppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClienteService extends AppService
{
    public function __construct(Cliente $cliente)
    {
        $this->model = $cliente;
    }

    /**
     * Save Cliente and create associated user
     * @param array $options
     * @return Cliente
     */
    public function save($options = [])
    {
        return DB::transaction(function () use ($options) {
            if ($options instanceof Request) {
                $attributes = $options->all();
            } elseif (is_array($options)) {
                $attributes = $options;
            } else {
                $attributes = [];
            }

            if (empty($attributes)) {
                $attributes = request()->all();
            }

            if (isset($attributes['email'])) {
                $attributes['email'] = mb_strtolower(trim((string) $attributes['email']));
            }

            $attributes['nombre'] = trim((string) ($attributes['nombre'] ?? ''));
            $attributes['apellido'] = trim((string) ($attributes['apellido'] ?? ''));
            
            // Create the cliente
            $cliente = parent::save($attributes);
            
            // Create user if not already associated
            if (!$cliente->user_id && !empty($cliente->email)) {
                $user = $this->createUserForCliente($cliente, $attributes);
                $cliente->user_id = $user->id;
                $cliente->save();
            }
            
            return $cliente;
        });
    }

    /**
     * Create a user for the cliente
     * @param Cliente $cliente
     * @param array $attributes
     * @return User
     */
    protected function createUserForCliente(Cliente $cliente, array $attributes)
    {
        // Use the cliente's email
        $email = mb_strtolower(trim((string) $cliente->email));
        $superPartnerId = !empty($attributes['super_partner_id']) ? (int) $attributes['super_partner_id'] : null;
        $clienteRole = $this->ensureClienteRole();

        $existingUser = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existingUser) {
            if ($clienteRole && !$existingUser->roles()->where('name', $clienteRole->name)->exists()) {
                $existingUser->assignRole($clienteRole);
            }

            if ($superPartnerId && !$existingUser->super_partner_id) {
                $existingUser->super_partner_id = $superPartnerId;
                $existingUser->save();
            }

            return $existingUser;
        }
        
        // Use the provided password
        $password = $attributes['password'];
        
        // Get active status
        $status = Status::findByNameAndType('status_active', 'user');
        
        $user = User::create([
            'first_name' => $cliente->nombre,
            'last_name'  => $cliente->apellido ?? '',
            'email'      => $email,
            'password'   => Hash::make($password),
            'user_type'  => 'cliente',
            'status_id'  => $status->id,
            'super_partner_id' => $superPartnerId,
        ]);
        if ($clienteRole) {
            $user->assignRole($clienteRole);
        }
        
        return $user;
    }

    protected function ensureClienteRole(): ?Role
    {
        $role = Role::query()->where('name', 'cliente')->first();

        if ($role) {
            return $role;
        }

        $type = Type::query()->where('alias', 'app')->first();

        if (!$type) {
            return null;
        }

        return Role::create([
            'name' => 'cliente',
            'type_id' => $type->id,
            'is_admin' => false,
            'is_default' => false,
            'created_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Update Cliente service
     * @param Cliente $cliente
     * @return Cliente
     */
    public function update(Cliente $cliente)
    {
        $cliente->fill(request()->only([
            'nombre',
            'apellido',
            'identificador',
            'email',
            'beneficiario_id',
            'can_activate_free_esim',
            'free_esim_capacity',
        ]));

        $this->model = $cliente;

        $cliente->save();

        // Update linked user's password if provided
        if ($cliente->user_id && request()->filled('password')) {
            User::where('id', $cliente->user_id)->update([
                'password' => Hash::make(request('password')),
            ]);
        }

        return $cliente;
    }

    /**
     * Delete Cliente service
     * @param Cliente $cliente
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Cliente $cliente)
    {
        return $cliente->delete();
    }
}
