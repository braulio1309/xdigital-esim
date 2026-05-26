<?php

namespace App\Imports\App;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\App\Cliente\FreeEsimInvitationMailService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ClienteImport implements ToCollection, WithHeadingRow
{
    protected $beneficiarioId;
    protected $partnerIds;
    protected $superPartnerId;
    protected $freeEsimCapacity;
    protected $imported = 0;
    protected $skipped = 0;
    protected $errors = [];
    protected $skippedDetails = [];
    protected $clienteAccessMailService;
    protected $freeEsimInvitationMailService;
    protected $superPartner;

    protected function normalizeKey(string $key): string
    {
        return Str::slug($key, '_');
    }

    protected function rowHasAnyToken(string $key, array $tokens): bool
    {
        foreach ($tokens as $token) {
            if (Str::contains($key, $token)) {
                return true;
            }
        }

        return false;
    }

    protected function findValueByKeyPatterns(Collection $row, array $requiredTokens, array $preferredTokens = [], string $default = ''): string
    {
        foreach ($row as $key => $value) {
            $normalizedKey = $this->normalizeKey((string) $key);

            if (!$this->rowHasAnyToken($normalizedKey, $requiredTokens)) {
                continue;
            }

            if (!empty($preferredTokens) && !$this->rowHasAnyToken($normalizedKey, $preferredTokens)) {
                continue;
            }

            $normalizedValue = trim((string) $value);

            if ($normalizedValue !== '') {
                return $normalizedValue;
            }
        }

        return $default;
    }

    protected function findPositiveIntegerByKeyPatterns(Collection $row, array $requiredTokens, array $preferredTokens = [], int $default = 1): int
    {
        foreach ($row as $key => $value) {
            $normalizedKey = $this->normalizeKey((string) $key);

            if (!$this->rowHasAnyToken($normalizedKey, $requiredTokens)) {
                continue;
            }

            if (!empty($preferredTokens) && !$this->rowHasAnyToken($normalizedKey, $preferredTokens)) {
                continue;
            }

            if (is_string($value)) {
                preg_match('/\d+/', $value, $matches);
                $value = $matches[0] ?? 0;
            }

            $normalizedValue = (int) $value;

            if ($normalizedValue > 0) {
                return $normalizedValue;
            }
        }

        return $default;
    }

    protected function firstNonEmptyValue(Collection $row, array $keys, string $default = ''): string
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (!array_key_exists($normalizedKey, $row->all())) {
                continue;
            }

            $value = trim((string) $row[$normalizedKey]);

            if ($value !== '') {
                return $value;
            }
        }

        return $default;
    }

    protected function firstPositiveInteger(Collection $row, array $keys, int $default = 1): int
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);

            if (!array_key_exists($normalizedKey, $row->all())) {
                continue;
            }

            $rawValue = $row[$normalizedKey];

            if (is_string($rawValue)) {
                preg_match('/\d+/', $rawValue, $matches);
                $rawValue = $matches[0] ?? 0;
            }

            $value = (int) $rawValue;

            if ($value > 0) {
                return $value;
            }
        }

        return $default;
    }

    public function __construct($beneficiarioId = null, array $partnerIds = [], $freeEsimCapacity = null, $superPartnerId = null)
    {
        $this->beneficiarioId = $beneficiarioId;
        $this->partnerIds = array_values(array_unique(array_map('intval', array_filter($partnerIds))));
        $this->freeEsimCapacity = $freeEsimCapacity;
        $this->superPartnerId = $superPartnerId ? (int) $superPartnerId : null;
        $this->clienteAccessMailService = app('App\Services\App\Cliente\ClienteAccessMailService');
        $this->freeEsimInvitationMailService = app(FreeEsimInvitationMailService::class);
        $this->superPartner = $this->superPartnerId ? SuperPartner::find($this->superPartnerId) : null;
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

    protected function registerSkippedRow(int $rowNumber, array $payload, string $reason): void
    {
        $this->skipped++;
        $this->skippedDetails[] = [
            'row' => $rowNumber,
            'nombre' => $payload['nombre'] ?? '',
            'apellido' => $payload['apellido'] ?? '',
            'identificador' => $payload['identificador'] ?? '',
            'email' => $payload['email'] ?? '',
            'reason' => $reason,
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = (int) $index + 2;

            // --- NORMALIZACIÓN DE LLAVES ---
            // Convertimos todas las llaves a "slug" (ej: "Nombre Completo" -> "nombre_completo")
            $row = $row->mapWithKeys(function ($value, $key) {
                return [Str::slug($key, '_') => $value];
            });

            // Extraemos los valores buscando variaciones comunes de nombres de columna.
            // Primero intentamos llaves conocidas y luego una búsqueda más flexible por tokens.
            $nombreCompletoContratante = $this->firstNonEmptyValue($row, [
                'nombre_del_contratante',
                'nombre_contratante',
                'contratante',
            ], $this->findValueByKeyPatterns($row, ['contrat'], ['nombre']));
            $nombre = $this->firstNonEmptyValue($row, [
                'nombre',
                'name',
                'first_name',
            ], $nombreCompletoContratante ?: $this->findValueByKeyPatterns($row, ['nombre']));
            $apellido = $this->firstNonEmptyValue($row, [
                'apellido',
                'last_name',
                'surname',
                'apellido_del_contratante',
                'apellido_contratante',
            ], $this->findValueByKeyPatterns($row, ['apellido']));
            $identificador = $this->firstNonEmptyValue($row, [
                'identificador',
                'documento',
                'numero_documento',
                'dni',
                'pasaporte',
                'passport',
                'id_del_contratante',
                'identificacion_del_contratante',
                'dni_del_contratante',
            ], $this->findValueByKeyPatterns(
                $row,
                ['id', 'ident', 'doc', 'dni', 'pasaport', 'cedula'],
                ['contrat']
            ) ?: $this->findValueByKeyPatterns($row, ['id', 'ident', 'doc', 'dni', 'pasaport', 'cedula']));
            $email = mb_strtolower($this->firstNonEmptyValue($row, [
                'email',
                'correo',
                'e_mail',
                'correo_del_contratante',
                'email_del_contratante',
                'mail_del_contratante',
            ], $this->findValueByKeyPatterns(
                $row,
                ['email', 'correo', 'mail'],
                ['contrat']
            ) ?: $this->findValueByKeyPatterns($row, ['email', 'correo', 'mail'])));
            $numeroVoucher = $this->firstNonEmptyValue($row, [
                'numero_voucher',
                'voucher',
                'num_voucher',
                'n_de_voucher',
            ], $this->findValueByKeyPatterns($row, ['voucher']));
            $numeroPersonas = $this->firstPositiveInteger($row, [
                'numero_personas',
                'personas',
                'num_personas',
                'cant_de_pasajeros',
                'cantidad_de_pasajeros',
                'cantidad_pasajeros',
                'pasajeros',
            ], $this->findPositiveIntegerByKeyPatterns(
                $row,
                ['pasaj', 'viajer', 'persona', 'pax'],
                ['cant', 'cantidad', 'num', 'nro', 'no']
            ));
            $rowPayload = [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'identificador' => $identificador,
                'email' => $email,
            ];

            // --- LÓGICA ORIGINAL ---
            $missingFields = [];

            if (empty($identificador)) {
                $missingFields[] = 'identificador';
            }

            if (empty($email)) {
                $missingFields[] = 'email';
            }

            if (!empty($missingFields)) {
                $this->registerSkippedRow(
                    $rowNumber,
                    $rowPayload,
                    'Faltan columnas o valores obligatorios: ' . implode(', ', $missingFields)
                );
                continue;
            }

            try {
                $cliente = DB::transaction(function () use ($nombre, $apellido, $identificador, $email, $numeroVoucher, $numeroPersonas) {
                    $password = $this->clienteAccessMailService->buildPasswordFromIdentifier($identificador);

                    // Reuse the auth user if the email already exists there, but always create the cliente row.
                    $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

                    if (!$user) {
                        $user = User::create([
                            'first_name' => $nombre,
                            'last_name'  => $apellido,
                            'email'      => $email,
                            'password'   => Hash::make($password),
                            'user_type'  => 'cliente',
                            'status_id'  => $this->resolveUserStatusId(),
                            'super_partner_id' => $this->superPartnerId,
                        ]);
                        $user->assignRole('cliente');
                    } elseif ($user->user_type !== 'cliente') {
                        // Skip rows where the email belongs to a non-cliente user
                        throw new \Exception("El email {$email} pertenece a un usuario de otro tipo ({$user->user_type}).");
                    } else {
                        $user->update([
                            'first_name' => $nombre,
                            'last_name' => $apellido,
                            'password' => Hash::make($password),
                            'super_partner_id' => $this->superPartnerId,
                        ]);
                    }

                    $cliente = Cliente::create([
                        'nombre'                 => $nombre,
                        'apellido'               => $apellido,
                        'identificador'          => $identificador,
                        'email'                  => $email,
                        'user_id'                => $user->id,
                        'beneficiario_id'        => $this->beneficiarioId,
                        'can_activate_free_esim' => true,
                        'free_esim_capacity'     => $this->freeEsimCapacity,
                    ]);

                    if (!empty($this->partnerIds)) {
                        $cliente->partners()->syncWithoutDetaching($this->partnerIds);
                    }

                    // Registrar voucher si viene en el Excel
                    if (!empty($numeroVoucher)) {
                        ClienteVoucher::create([
                            'cliente_id'      => $cliente->id,
                            'numero_voucher'  => $numeroVoucher,
                            'numero_personas' => $numeroPersonas,
                        ]);
                    }

                    return $cliente;
                });

                $this->clienteAccessMailService->sendAccessCredentials($cliente);
                try {
                    $beneficiario = $cliente->beneficiario;
                    $this->freeEsimInvitationMailService->send($cliente, $beneficiario, $this->superPartner);
                } catch (\Throwable $mailException) {
                    $this->errors[] = "No se pudo enviar invitación de eSIM gratuita a {$email}: " . $mailException->getMessage();
                }

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Error al importar {$email}: " . $e->getMessage();
                $this->registerSkippedRow($rowNumber, $rowPayload, $e->getMessage());
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

    public function getSkippedDetails(): array
    {
        return $this->skippedDetails;
    }
}
