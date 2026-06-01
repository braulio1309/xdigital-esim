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

    /**
     * Pre-process rows to identify duplicate emails and/or vouchers.
     *
     * Returns two arrays:
     *   $canonicalMap  – row_index => canonical_row_index (the first occurrence index)
     *   $groupCountMap – canonical_row_index => total number of rows in that group
     *
     * Two rows belong to the same group when they share the same email OR the same
     * non-empty voucher number.
     *
     * @param  Collection $rows
     * @return array{0: array<int,int>, 1: array<int,int>}
     */
    protected function preGroupRows(Collection $rows): array
    {
        // parent[] implements a simple union-find structure.
        // parent[$i] === $i means $i is a root (canonical).
        $parent        = [];
        $groupCountMap = [];
        // emailToRoot / voucherToRoot hold the root index for the first occurrence
        $emailToRoot   = [];
        $voucherToRoot = [];

        // --- Helper: find root with path compression ---
        $find = function (int $i) use (&$parent, &$find): int {
            if ($parent[$i] !== $i) {
                $parent[$i] = $find($parent[$i]);
            }
            return $parent[$i];
        };

        // --- Helper: union two sets (keep the smaller root) ---
        $union = function (int $a, int $b) use (&$parent, &$groupCountMap, &$find): void {
            $ra = $find($a);
            $rb = $find($b);
            if ($ra === $rb) {
                return;
            }
            // Always attach the larger root to the smaller one (earlier row wins)
            [$keep, $drop] = $ra < $rb ? [$ra, $rb] : [$rb, $ra];
            $parent[$drop]        = $keep;
            $groupCountMap[$keep] = ($groupCountMap[$keep] ?? 0) + ($groupCountMap[$drop] ?? 0);
            unset($groupCountMap[$drop]);
        };

        $indexList = [];

        foreach ($rows as $index => $row) {
            $indexList[] = $index;

            $normalizedRow = $row->mapWithKeys(function ($value, $key) {
                return [Str::slug($key, '_') => $value];
            });

            $email = mb_strtolower(trim((string) $this->firstNonEmptyValue($normalizedRow, [
                'email', 'correo', 'e_mail',
                'correo_del_contratante', 'email_del_contratante', 'mail_del_contratante',
            ], $this->findValueByKeyPatterns(
                $normalizedRow,
                ['email', 'correo', 'mail'],
                ['contrat']
            ) ?: $this->findValueByKeyPatterns($normalizedRow, ['email', 'correo', 'mail']))));

            $voucher = trim((string) $this->firstNonEmptyValue($normalizedRow, [
                'numero_voucher', 'voucher', 'num_voucher', 'n_de_voucher',
            ], $this->findValueByKeyPatterns($normalizedRow, ['voucher'])));

            // Register this row as its own root initially
            $parent[$index]        = $index;
            $groupCountMap[$index] = 1;

            // Merge with existing group if email already seen
            if ($email !== '' && isset($emailToRoot[$email])) {
                $union($index, $emailToRoot[$email]);
            }

            // Merge with existing group if voucher already seen
            if ($voucher !== '' && isset($voucherToRoot[$voucher])) {
                $union($index, $voucherToRoot[$voucher]);
            }

            // Store email/voucher → root for the first time only;
            // after union the root may have changed, so use find()
            if ($email !== '' && !isset($emailToRoot[$email])) {
                $emailToRoot[$email] = $find($index);
            }

            if ($voucher !== '' && !isset($voucherToRoot[$voucher])) {
                $voucherToRoot[$voucher] = $find($index);
            }
        }

        // Build canonicalMap: each row index → its root
        $canonicalMap = [];
        foreach ($indexList as $index) {
            $canonicalMap[$index] = $find($index);
        }

        return [$canonicalMap, $groupCountMap];
    }

    public function collection(Collection $rows)
    {
        [$canonicalMap, $groupCountMap] = $this->preGroupRows($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = (int) $index + 2;

            // --- DEDUPLICACIÓN: sólo procesar la primera fila de cada grupo ---
            $canonical = $canonicalMap[$index] ?? $index;

            if ($canonical !== $index) {
                // Fila duplicada (mismo correo o voucher ya procesado). Se omite.
                $this->skipped++;
                $this->skippedDetails[] = [
                    'row'           => $rowNumber,
                    'nombre'        => '',
                    'apellido'      => '',
                    'identificador' => '',
                    'email'         => '',
                    'reason'        => 'Fila duplicada (correo o voucher ya procesado en fila ' . ($canonical + 2) . ').',
                ];
                continue;
            }

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
            // El número de personas se determina por el número de filas del grupo
            // (cuántas veces aparece el mismo correo o voucher en el archivo).
            // Si el grupo sólo tiene una fila, se usa el valor de la columna PASAJEROS del Excel.
            $groupCount = $groupCountMap[$index] ?? 1;
            $numeroPersonas = $groupCount > 1
                ? $groupCount
                : $this->firstPositiveInteger($row, [
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
