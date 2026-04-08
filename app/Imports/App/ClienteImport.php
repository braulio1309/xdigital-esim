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
use Illuminate\Support\Str;

class ClienteImport implements ToCollection, WithHeadingRow
{
    protected $beneficiarioId;
    protected $freeEsimCapacity;
    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];

    public function __construct($beneficiarioId = null, $freeEsimCapacity = null)
    {
        $this->beneficiarioId = $beneficiarioId;
        $this->freeEsimCapacity = $freeEsimCapacity;
    }

    protected function resolveUserStatusId(): int
    {
        $status = Status::findByNameAndType('status_active', 'user');

        if (!$status) {
            $status = Status::query()
                ->where('type', 'user')
                ->orderBy('id')
                ->first();
        }

        if (!$status) {
            throw new \RuntimeException('No existe ningún estado configurado para usuarios. Verifica la tabla statuses o ejecuta los seeders de estados.');
        }

        return (int) $status->id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // --- NORMALIZACIÓN DE LLAVES ---
            // Convertimos todas las llaves a "slug" (ej: "Nombre Completo" -> "nombre_completo")
            $row = $row->mapWithKeys(function ($value, $key) {
                return [Str::slug($key, '_') => $value];
            });

            // Extraemos los valores buscando variaciones comunes de nombres de columna
            $nombre   = trim($row['nombre'] ?? $row['name'] ?? $row['first_name'] ?? $row['Nombre'] ??'');
            $apellido = trim($row['apellido'] ?? $row['last_name'] ?? $row['surname'] ?? $row['Apellido'] ?? '');
            $identificador = trim(
                $row['identificador']
                ?? $row['documento']
                ?? $row['numero_documento']
                ?? $row['dni']
                ?? $row['pasaporte']
                ?? $row['passport']
                ?? ''
            );
            $email    = trim($row['email'] ?? $row['correo'] ?? $row['e_mail'] ?? $row['Email'] ?? $row['Correo'] ?? '');

            // --- LÓGICA ORIGINAL ---
            if (empty($nombre) || empty($email) || empty($identificador)) {
                $this->skipped++;
                continue;
            }

            // Skip if email already exists as a cliente
            if (Cliente::where('email', $email)->exists()) {
                $this->skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($nombre, $apellido, $identificador, $email) {
                    $password = $nombre . '123*';

                    // Skip if user with this email already exists
                    $user = User::where('email', $email)->first();

                    if (!$user) {
                        $user = User::create([
                            'first_name' => $nombre,
                            'last_name'  => $apellido,
                            'email'      => $email,
                            'password'   => Hash::make($password),
                            'user_type'  => 'cliente',
                            'status_id'  => $this->resolveUserStatusId(),
                        ]);
                        $user->assignRole('cliente');
                    } elseif ($user->user_type !== 'cliente') {
                        // Skip rows where the email belongs to a non-cliente user
                        throw new \Exception("El email {$email} pertenece a un usuario de otro tipo ({$user->user_type}).");
                    }

                    Cliente::create([
                        'nombre'                 => $nombre,
                        'apellido'               => $apellido,
                        'identificador'          => $identificador,
                        'email'                  => $email,
                        'user_id'                => $user->id,
                        'beneficiario_id'        => $this->beneficiarioId,
                        'can_activate_free_esim' => true,
                        'free_esim_capacity'     => $this->freeEsimCapacity,
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