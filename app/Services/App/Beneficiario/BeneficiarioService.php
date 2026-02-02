<?php

namespace App\Services\App\Beneficiario;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\AppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BeneficiarioService extends AppService
{
    public function __construct(Beneficiario $beneficiario)
    {
        $this->model = $beneficiario;
    }

    /**
     * Save Beneficiario and create associated user
     * @param array $options
     * @return Beneficiario
     */
    public function save($options = [])
    {
        return DB::transaction(function () use ($options) {
            $attributes = count($options) ? $options : request()->all();
            
            // Generate unique codigo if not provided
            if (!isset($attributes['codigo'])) {
                $attributes['codigo'] = $this->generateUniqueCode();
            }
            
            // Create the beneficiario - pass attributes as options to avoid request mutation
            $beneficiario = parent::save($attributes);
            
            // Create user if not already associated
            if (!$beneficiario->user_id && isset($attributes['nombre'])) {
                $user = $this->createUserForBeneficiario($beneficiario, $attributes);
                $beneficiario->user_id = $user->id;
                $beneficiario->save();
            }
            
            return $beneficiario;
        });
    }

    /**
     * Generate a unique 8-character alphanumeric code
     * @return string
     */
    protected function generateUniqueCode()
    {
        do {
            // Generate random 8 character uppercase alphanumeric string using Laravel's Str helper
            $codigo = strtoupper(Str::random(8));
            
            // Check if code already exists
            $exists = Beneficiario::where('codigo', $codigo)->exists();
        } while ($exists);
        
        return $codigo;
    }

    /**
     * Create a user for the beneficiario
     * @param Beneficiario $beneficiario
     * @param array $attributes
     * @return User
     */
    protected function createUserForBeneficiario(Beneficiario $beneficiario, array $attributes)
    {
        // Generate email from request or create from name
        $email = $attributes['email'] ?? strtolower(str_replace(' ', '.', $beneficiario->nombre)) . '@beneficiario.local';
        
        // Generate password: nombre + "123"
        $password = $beneficiario->nombre . '123';
        
        // Get active status
        $status = Status::findByNameAndType('status_active', 'user');
        
        $user = User::create([
            'first_name' => $beneficiario->nombre,
            'last_name' => '',
            'email' => $email,
            'password' => Hash::make($password),
            'user_type' => 'beneficiario',
            'status_id' => $status->id,
        ]);
        $user->assignRole('Moderator');
        
        return $user;
    }

    /**
     * Update Beneficiario service
     * @param Beneficiario $beneficiario
     * @return Beneficiario
     */
    public function update(Beneficiario $beneficiario)
    {
        $beneficiario->fill(request()->all());

        $this->model = $beneficiario;

        $beneficiario->save();

        return $beneficiario;
    }

    /**
     * Delete Beneficiario service
     * @param Beneficiario $beneficiario
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Beneficiario $beneficiario)
    {
        return $beneficiario->delete();
    }
}
