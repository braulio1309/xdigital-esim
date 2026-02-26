<?php

namespace App\Imports\App;

use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClienteImport implements ToCollection, WithHeadingRow
{
    protected $beneficiarioId;
    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];

    public function __construct($beneficiarioId = null)
    {
        $this->beneficiarioId = $beneficiarioId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nombre   = trim($row['nombre'] ?? '');
            $apellido = trim($row['apellido'] ?? '');
            $email    = trim($row['email'] ?? '');

            if (empty($nombre) || empty($email)) {
                $this->skipped++;
                continue;
            }

            // Skip if email already exists as a cliente
            if (Cliente::where('email', $email)->exists()) {
                $this->skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($nombre, $apellido, $email) {
                    $password = $nombre . '123*';

                    // Skip if user with this email already exists
                    $user = User::where('email', $email)->first();

                    if (!$user) {
                        $status = Status::findByNameAndType('status_active', 'user');
                        $user = User::create([
                            'first_name' => $nombre,
                            'last_name'  => $apellido,
                            'email'      => $email,
                            'password'   => Hash::make($password),
                            'user_type'  => 'cliente',
                            'status_id'  => $status->id,
                        ]);
                        $user->assignRole('Moderator');
                    } elseif ($user->user_type !== 'cliente') {
                        // Skip rows where the email belongs to a non-cliente user
                        throw new \Exception("El email {$email} pertenece a un usuario de otro tipo ({$user->user_type}).");
                    }

                    Cliente::create([
                        'nombre'         => $nombre,
                        'apellido'       => $apellido,
                        'email'          => $email,
                        'user_id'        => $user->id,
                        'beneficiario_id' => $this->beneficiarioId,
                        'can_activate_free_esim' => true,
                    ]);
                });

                $this->imported++;
            } catch (\Exception $e) {
                $this->skipped++;
                $this->errors[] = "Error al importar {$email}: " . $e->getMessage();
            }
        }
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
