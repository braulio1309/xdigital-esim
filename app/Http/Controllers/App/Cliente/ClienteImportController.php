<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ClienteImportController extends Controller
{
    /**
     * Import clients from an Excel or CSV file.
     *
     * Accepted columns: nombre, apellido, email (extra columns are ignored).
     * - activate_free_esim: boolean, sets can_activate_free_esim on each imported client.
     * - beneficiario_id: optional, assign a partner. Auto-assigned if logged-in user is beneficiario.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'activate_free_esim' => 'nullable|boolean',
            'beneficiario_id' => 'nullable|integer|exists:beneficiarios,id',
        ]);

        // Determine the beneficiario_id to assign
        $beneficiarioId = null;
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $beneficiarioId = $beneficiario->id;
            }
        } elseif ($request->filled('beneficiario_id')) {
            $beneficiarioId = (int) $request->beneficiario_id;
        }

        $activateFreeEsim = filter_var($request->input('activate_free_esim', false), FILTER_VALIDATE_BOOLEAN);

        // Load the file into a collection
        try {
            $rows = Excel::toCollection(new HeadingRowImport, $request->file('file'))->first();
        } catch (\Exception $e) {
            Log::error('ClienteImport: failed to parse file - ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'No se pudo leer el archivo. Asegúrate de que sea un Excel o CSV válido.',
            ], 422);
        }

        if (!$rows || $rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'El archivo no contiene filas de datos.',
            ], 422);
        }

        $status = Status::findByNameAndType('status_active', 'user');

        $imported  = 0;
        $skipped   = 0;
        $errors    = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2; // +2 because row 1 is the header

            // Normalize keys: trim and lowercase
            $data = collect($row)->mapWithKeys(function ($value, $key) {
                return [strtolower(trim($key)) => is_string($value) ? trim($value) : $value];
            });

            $nombre   = $data->get('nombre', $data->get('name', ''));
            $apellido = $data->get('apellido', $data->get('lastname', $data->get('last_name', '')));
            $email    = $data->get('email', $data->get('correo', ''));

            // Skip completely empty rows
            if (empty($nombre) && empty($apellido) && empty($email)) {
                continue;
            }

            // Validate required fields
            if (empty($nombre)) {
                $errors[] = "Fila {$lineNumber}: falta el nombre.";
                $skipped++;
                continue;
            }
            if (empty($apellido)) {
                $errors[] = "Fila {$lineNumber}: falta el apellido.";
                $skipped++;
                continue;
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Fila {$lineNumber}: email inválido o faltante ({$email}).";
                $skipped++;
                continue;
            }

            // Skip duplicates by email
            if (Cliente::where('email', $email)->exists()) {
                $errors[] = "Fila {$lineNumber}: el email {$email} ya está registrado (omitido).";
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($nombre, $apellido, $email, $beneficiarioId, $activateFreeEsim, $status) {
                    // Create User account
                    $password = Str::random(12);

                    $user = User::create([
                        'first_name' => $nombre,
                        'last_name'  => $apellido,
                        'email'      => $email,
                        'password'   => Hash::make($password),
                        'user_type'  => 'cliente',
                        'status_id'  => $status->id,
                    ]);
                    $user->assignRole('Moderator');

                    // Create Cliente record
                    Cliente::create([
                        'nombre'                 => $nombre,
                        'apellido'               => $apellido,
                        'email'                  => $email,
                        'user_id'                => $user->id,
                        'beneficiario_id'        => $beneficiarioId,
                        'can_activate_free_esim' => $activateFreeEsim,
                    ]);
                });

                $imported++;
            } catch (\Exception $e) {
                Log::error("ClienteImport: error on row {$lineNumber} ({$email}) - " . $e->getMessage());
                $errors[] = "Fila {$lineNumber}: error al guardar el cliente ({$email}).";
                $skipped++;
            }
        }

        $message = "Importación finalizada: {$imported} clientes importados";
        if ($skipped > 0) {
            $message .= ", {$skipped} omitidos";
        }
        $message .= '.';

        return response()->json([
            'status'   => true,
            'message'  => $message,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }
}
