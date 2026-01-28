<?php

namespace App\Services\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\AppService;
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
            $attributes = count($options) ? $options : request()->all();
            
            // Create the cliente
            $cliente = parent::save($options);
            
            // Create user if not already associated
            if (!$cliente->user_id && isset($attributes['nombre'])) {
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
        $email = $cliente->email;
        
        // Generate password: nombre + "123"
        $password = $cliente->nombre . '123';
        
        // Get active status
        $status = Status::findByNameAndType('status_active', 'user');
        
        $user = User::create([
            'first_name' => $cliente->nombre,
            'last_name' => $cliente->apellido ?? '',
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => 'cliente',
            'status_id' => $status->id,
        ]);
        $user->assignRole('Moderator');
        
        return $user;
    }

    /**
     * Update Cliente service
     * @param Cliente $cliente
     * @return Cliente
     */
    public function update(Cliente $cliente)
    {
        $cliente->fill(request()->all());

        $this->model = $cliente;

        $cliente->save();

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
