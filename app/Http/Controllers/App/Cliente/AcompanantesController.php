<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Mail\App\Cliente\EsimActivationMail;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\EsimFxService;
use App\Helpers\CountryTariffHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AcompanantesController extends Controller
{
    /**
     * Show the companion eSIM registration form.
     * Accessed via a signed URL; no authentication required.
     */
    public function showForm(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'El enlace ha expirado o no es válido.');
        }

        $cliente = Cliente::find($request->query('cliente_id'));
        $voucher = ClienteVoucher::find($request->query('voucher_id'));
        $countryCode = strtoupper($request->query('country_code', ''));

        if (!$cliente || !$voucher || !$countryCode) {
            abort(404, 'Los parámetros del enlace son inválidos.');
        }

        if ((int) $voucher->cliente_id !== (int) $cliente->id) {
            abort(403, 'El enlace no corresponde a este cliente.');
        }

        $allowedCompanions = max(((int) ($voucher->numero_personas ?? 1)) - 1, 0);
        $usedCompanions = Transaction::where('companion_of_cliente_id', $cliente->id)->count();
        $remainingSlots = max($allowedCompanions - $usedCompanions, 0);

        $countryName = collect(CountryTariffHelper::getAllCountries())
            ->firstWhere('code', $countryCode)['name'] ?? $countryCode;

        return view('clientes.acompanantes-form', [
            'cliente' => $cliente,
            'voucher' => $voucher,
            'countryCode' => $countryCode,
            'countryName' => $countryName,
            'allowedCompanions' => $allowedCompanions,
            'remainingSlots' => $remainingSlots,
        ]);
    }

    /**
     * Process companion eSIM activation form submission.
     */
    public function processForm(Request $request, EsimFxService $esimService)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'El enlace ha expirado o no es válido.');
        }

        $cliente = Cliente::find($request->query('cliente_id'));
        $voucher = ClienteVoucher::find($request->query('voucher_id'));
        $countryCode = strtoupper($request->query('country_code', ''));

        if (!$cliente || !$voucher || !$countryCode) {
            abort(404, 'Los parámetros del enlace son inválidos.');
        }

        if ((int) $voucher->cliente_id !== (int) $cliente->id) {
            abort(403, 'El enlace no corresponde a este cliente.');
        }

        $allowedCompanions = max(((int) ($voucher->numero_personas ?? 1)) - 1, 0);
        $usedCompanions = Transaction::where('companion_of_cliente_id', $cliente->id)->count();
        $remainingSlots = max($allowedCompanions - $usedCompanions, 0);

        $validated = $request->validate([
            'companion_emails' => 'required|array|min:1',
            'companion_emails.*' => 'required|email',
        ]);

        $companionEmails = $this->normalizeCompanionEmails(
            (array) $validated['companion_emails'],
            $cliente->email
        );

        if (empty($companionEmails)) {
            return redirect()->back()
                ->with('error', 'No ingresaste correos válidos de acompañantes.')
                ->withInput();
        }

        if (count($companionEmails) > $remainingSlots) {
            return redirect()->back()
                ->with('error', "Solo puedes registrar hasta {$remainingSlots} acompañante(s) adicional(es) con este enlace.")
                ->withInput();
        }

        // Resolve transaction context from primary client
        $beneficiario = $cliente->beneficiario;
        $superPartner = $beneficiario ? $beneficiario->superPartner : null;
        $transactionContext = [
            'beneficiario_id' => $beneficiario ? $beneficiario->id : null,
            'super_partner_id' => $superPartner ? $superPartner->id : null,
        ];

        // Determine free eSIM product capacity from primary client
        $preferredCapacity = $this->resolveFreeEsimCapacityForCliente($cliente);

        try {
            $products = $esimService->getProducts(['countries' => $countryCode]);
            $selectedProduct = is_array($products)
                ? $this->selectFreeEsimProduct($products, $preferredCapacity)
                : null;

            if (!$selectedProduct && isset($products[0])) {
                $selectedProduct = $products[0];
            }

            if (!$selectedProduct) {
                return redirect()->back()
                    ->with('error', 'No encontramos un plan disponible para el país seleccionado. Intenta más tarde o contacta soporte.')
                    ->withInput();
            }

            $successCount = 0;

            foreach ($companionEmails as $index => $companionEmail) {
                $companionCliente = $this->findOrCreateCompanionCliente(
                    $companionEmail,
                    $transactionContext
                );

                try {
                    $esimDataView = $this->activateEsimForCompanion(
                        $companionCliente,
                        $cliente,
                        $selectedProduct,
                        $transactionContext,
                        $countryCode,
                        'COMP-' . $cliente->id . '-' . time() . '-' . $index,
                        $esimService
                    );

                    Mail::to($companionEmail)->send(new EsimActivationMail(
                        $esimDataView,
                        $companionEmail,
                        null
                    ));

                    $successCount++;

                    Log::info('eSIM de acompañante activada y enviada.', [
                        'companion_email' => $companionEmail,
                        'primary_cliente_id' => $cliente->id,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Error activando eSIM de acompañante.', [
                        'companion_email' => $companionEmail,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($successCount === 0) {
                return redirect()->back()
                    ->with('error', 'No fue posible activar ninguna eSIM de acompañante. Intenta más tarde o contacta soporte.')
                    ->withInput();
            }

            $message = $successCount === 1
                ? 'Se activó y envió la eSIM al correo del acompañante.'
                : "Se activaron y enviaron {$successCount} eSIMs a los correos de los acompañantes.";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error en formulario de acompañantes: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar las eSIMs. Por favor, intenta nuevamente.')
                ->withInput();
        }
    }

    private function normalizeCompanionEmails(array $emails, string $primaryEmail): array
    {
        $primaryEmail = mb_strtolower(trim($primaryEmail));

        return array_values(array_unique(array_filter(array_map(function ($email) use ($primaryEmail) {
            $email = mb_strtolower(trim((string) $email));

            if ($email === '' || $email === $primaryEmail) {
                return null;
            }

            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }, $emails))));
    }

    private function resolveFreeEsimCapacityForCliente(Cliente $cliente): int
    {
        $capacity = (int) ($cliente->free_esim_capacity ?? 0);

        if (in_array($capacity, [3, 5, 10], true)) {
            return $capacity;
        }

        return 1;
    }

    private function selectFreeEsimProduct(array $products, int $preferredCapacity): ?array
    {
        $normalizedProducts = collect($products)
            ->filter(function ($product) {
                return isset($product['amount'], $product['amount_unit'])
                    && strtoupper((string) $product['amount_unit']) === 'GB';
            });

        $matchingCapacity = $normalizedProducts
            ->filter(function ($product) use ($preferredCapacity) {
                return (int) $product['amount'] === $preferredCapacity;
            })
            ->sort(function ($first, $second) {
                return [
                    (float) ($first['price'] ?? 0),
                    (int) ($first['duration'] ?? PHP_INT_MAX),
                ] <=> [
                    (float) ($second['price'] ?? 0),
                    (int) ($second['duration'] ?? PHP_INT_MAX),
                ];
            })
            ->values();

        if ($matchingCapacity->isNotEmpty()) {
            return $matchingCapacity->first();
        }

        return $normalizedProducts->first() ?: null;
    }

    private function findOrCreateCompanionCliente(string $email, array $transactionContext): Cliente
    {
        $email = mb_strtolower($email);

        $existing = Cliente::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existing) {
            // Ensure the existing cliente has a linked user account
            if (!$existing->user_id) {
                $user = $this->findOrCreateUserForCompanion($email, $transactionContext);
                $existing->user_id = $user->id;
                $existing->save();
            }

            return $existing;
        }

        $user = $this->findOrCreateUserForCompanion($email, $transactionContext);

        return Cliente::create([
            'email' => $email,
            'nombre' => '',
            'apellido' => '',
            'identificador' => '',
            'user_id' => $user->id,
            'can_activate_free_esim' => false,
            'beneficiario_id' => $transactionContext['beneficiario_id'],
        ]);
    }

    private function findOrCreateUserForCompanion(string $email, array $transactionContext): User
    {
        $existingUser = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($existingUser) {
            if (!$existingUser->roles()->where('name', 'cliente')->exists()) {
                $existingUser->assignRole('cliente');
            }

            if ($existingUser->user_type !== 'cliente') {
                $existingUser->user_type = 'cliente';
                $existingUser->save();
            }

            return $existingUser;
        }

        $status = Status::findByNameAndType('status_active', 'user');

        $user = User::create([
            'first_name'       => '',
            'last_name'        => '',
            'email'            => $email,
            'password'         => Hash::make(Str::random(16)),
            'user_type'        => 'cliente',
            'status_id'        => $status ? $status->id : null,
            'super_partner_id' => null,
        ]);
        $user->assignRole('cliente');

        return $user;
    }

    private function activateEsimForCompanion(
        Cliente $companionCliente,
        Cliente $primaryCliente,
        array $selectedProduct,
        array $transactionContext,
        string $countryCode,
        string $transactionId,
        EsimFxService $esimService
    ): array {
        $apiResponse = $esimService->createOrder($selectedProduct['id'], $transactionId);

        if (empty($apiResponse['id'])) {
            throw new \RuntimeException('No fue posible crear la orden de eSIM para el acompañante.');
        }

        $activate = $esimService->activateOrder($apiResponse['id']);
        $esimPayload = $this->resolveEsimPayload($apiResponse, is_array($activate) ? $activate : []);

        if (!$esimPayload) {
            throw new \RuntimeException('La eSIM del acompañante fue procesada pero no se recibieron los datos de activación.');
        }

        $qrValue = $esimPayload['esim_qr'] ?? null;

        if (!$qrValue) {
            throw new \RuntimeException('No se recibió el QR de activación para el acompañante.');
        }

        $qrImage = QrCode::size(300)->generate($qrValue);
        $parts = explode('$', $qrValue);

        $esimDataView = [
            'qr_svg' => (string) $qrImage,
            'smdp' => $parts[1] ?? 'N/A',
            'code' => $parts[2] ?? 'N/A',
            'iccid' => $esimPayload['iccid'] ?? 'N/A',
            'data_amount' => $selectedProduct['amount'] ?? $selectedProduct['data_amount'] ?? null,
            'duration_days' => $selectedProduct['duration'] ?? $selectedProduct['validity_period'] ?? null,
        ];

        $transactionData = [
            'order_id' => $apiResponse['id'],
            'transaction_id' => $transactionId,
            'status' => $apiResponse['status'] ?? 'completed',
            'iccid' => $esimPayload['iccid'] ?? null,
            'esim_qr' => $esimPayload['esim_qr'] ?? null,
            'creation_time' => now(),
            'cliente_id' => $companionCliente->id,
            'companion_of_cliente_id' => $primaryCliente->id,
            'beneficiario_id' => $transactionContext['beneficiario_id'],
            'super_partner_id' => $transactionContext['super_partner_id'],
            'plan_name' => $selectedProduct['name'] ?? null,
            'data_amount' => $selectedProduct['amount'] ?? $selectedProduct['data_amount'] ?? null,
            'duration_days' => $selectedProduct['duration'] ?? $selectedProduct['validity_period'] ?? null,
            'purchase_amount' => 0,
            'api_price' => isset($selectedProduct['price']) ? (float) $selectedProduct['price'] : null,
            'reference_purchase_amount' => 0,
            'beneficiary_commission_amount' => 0,
            'currency' => 'USD',
            'country_code' => $countryCode,
            'partner_sale_commission_amount' => 0,
            'super_partner_sale_commission_amount' => 0,
        ];

        static $transactionColumns;

        if ($transactionColumns === null) {
            $transactionColumns = array_flip(Schema::getColumnListing('transactions'));
        }

        Transaction::create(array_intersect_key($transactionData, $transactionColumns));

        return $esimDataView;
    }

    private function resolveEsimPayload(array $orderResponse, array $activationResponse = []): ?array
    {
        if (!empty($activationResponse['esim']) && is_array($activationResponse['esim'])) {
            return $activationResponse['esim'];
        }

        if (!empty($orderResponse['esim']) && is_array($orderResponse['esim'])) {
            return $orderResponse['esim'];
        }

        if (!empty($activationResponse['esim_qr']) || !empty($activationResponse['iccid'])) {
            return [
                'esim_qr' => $activationResponse['esim_qr'] ?? null,
                'iccid' => $activationResponse['iccid'] ?? null,
            ];
        }

        if (!empty($orderResponse['esim_qr']) || !empty($orderResponse['iccid'])) {
            return [
                'esim_qr' => $orderResponse['esim_qr'] ?? null,
                'iccid' => $orderResponse['iccid'] ?? null,
            ];
        }

        return null;
    }
}
