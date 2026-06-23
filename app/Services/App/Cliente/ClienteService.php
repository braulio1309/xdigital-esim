<?php

namespace App\Services\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\AppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        $existingUser = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existingUser) {
            $this->ensureExistingUserCanRegisterAsCliente($existingUser);

            if (!$existingUser->roles()->where('name', 'cliente')->exists()) {
                $existingUser->assignRole('cliente');
            }

            if ($existingUser->user_type !== 'cliente') {
                $existingUser->user_type = 'cliente';
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
        $user->assignRole('cliente');
        
        return $user;
    }

    protected function ensureExistingUserCanRegisterAsCliente(User $existingUser): void
    {
        if (in_array($existingUser->user_type, ['beneficiario', 'admin_beneficiario', 'super_partner', 'admin_partner'], true)) {
            throw ValidationException::withMessages([
                'email' => 'Este correo ya pertenece a una cuenta de partner o super partner y no puede registrarse como cliente.',
            ]);
        }
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
