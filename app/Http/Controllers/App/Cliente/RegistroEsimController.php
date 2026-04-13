<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\ClienteService;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\App\Settings\SuperPartnerPlanMarginService;
use Illuminate\Http\Request as HttpRequest;
// Importaciones necesarias
use App\Services\EsimFxService;
use App\Services\StripeService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Helpers\CountryTariffHelper;
use App\Mail\App\Cliente\EsimActivationMail;
use Illuminate\Support\Facades\Mail;

class RegistroEsimController extends Controller
{
    private const LEGACY_FREE_ESIM_AMOUNT = 1;

    /**
     * Resolve beneficiary or super partner from a referral code.
     *
     * @param string|null $referralCode
     * @return array{beneficiario:?Beneficiario,superPartner:?SuperPartner,brandPartner:mixed}
     */
    private function resolveBrandingContext($referralCode)
    {
        $beneficiario = null;
        $superPartner = null;

        if ($referralCode) {
            $codigo = $this->extractCodigoFromReferralCode($referralCode);

            if ($codigo) {
                $beneficiario = Beneficiario::where('codigo', $codigo)->first();

                if (!$beneficiario) {
                    $superPartner = SuperPartner::where('codigo', $codigo)->first();
                }
            }
        }

        return [
            'beneficiario' => $beneficiario,
            'superPartner' => $superPartner,
            'brandPartner' => $beneficiario ?: $superPartner,
        ];
    }

    /**
     * Extract codigo from referral code format (nombre-codigo)
     * @param string $referralCode
     * @return string|null
     */
    private function extractCodigoFromReferralCode($referralCode)
    {
        if (!$referralCode) {
            return null;
        }
        
        // Extraer el código del formato: nombre-codigo
        $parts = explode('-', $referralCode);
        // Get last element without modifying array pointer
        return $parts[count($parts) - 1];
    }

    private function syncPartnerContext(array $brandingContext, ?string $codigo = null): void
    {
        $beneficiario = $brandingContext['beneficiario'] ?? null;
        $superPartner = $brandingContext['superPartner'] ?? null;

        if ($beneficiario || $superPartner) {
            session([
                'planes_partner_context' => [
                    'codigo' => $codigo,
                    'beneficiario_id' => $beneficiario ? $beneficiario->id : null,
                    'super_partner_id' => $beneficiario ? $beneficiario->super_partner_id : ($superPartner ? $superPartner->id : null),
                ],
            ]);

            return;
        }

        session()->forget('planes_partner_context');
    }

    private function resolveFreeEsimCapacityForCliente(Cliente $cliente): int
    {
        $capacity = (int) ($cliente->free_esim_capacity ?? 0);

        if (in_array($capacity, [3, 5, 10], true)) {
            return $capacity;
        }

        return self::LEGACY_FREE_ESIM_AMOUNT;
    }

    private function resolveTransactionContext(array $brandingContext, ?Cliente $cliente = null): array
    {
        $beneficiario = $brandingContext['beneficiario'] ?? null;
        $superPartner = $brandingContext['superPartner'] ?? null;

        if (!$beneficiario && $cliente && $cliente->beneficiario) {
            $beneficiario = $cliente->beneficiario;
            $superPartner = $beneficiario->superPartner;
        }

        if (!$superPartner && $beneficiario) {
            $superPartner = $beneficiario->superPartner;
        }

        return [
            'beneficiario_id' => $beneficiario ? $beneficiario->id : null,
            'super_partner_id' => $superPartner ? $superPartner->id : null,
        ];
    }

    private function calculateFreeEsimPricingSnapshot(array $product, ?int $beneficiarioId, ?int $superPartnerId, array $brandingContext = [], ?Cliente $cliente = null): array
    {
        $originalPrice = (float) ($product['price'] ?? 0);
        $planCapacity = (string) ($product['amount'] ?? $product['data_amount'] ?? '0');
        $capacityAsInt = (int) $planCapacity;

        if ($capacityAsInt <= self::LEGACY_FREE_ESIM_AMOUNT) {
            $flatRate = $this->calculateFreeEsimCommissionAmount($brandingContext, $cliente);

            return [
                'charge_amount' => $flatRate,
                'commission_amount' => $flatRate,
            ];
        }

        $adminPrice = app(PlanMarginService::class)->calculateFinalPrice($originalPrice, $planCapacity);
        $priceAfterSuperPartner = $superPartnerId
            ? app(SuperPartnerPlanMarginService::class)->calculateFinalPrice($adminPrice, $planCapacity, $superPartnerId)
            : $adminPrice;

        $finalPrice = $beneficiarioId
            ? app(BeneficiaryPlanMarginService::class)->calculateFinalPrice($priceAfterSuperPartner, $planCapacity, $beneficiarioId)
            : $priceAfterSuperPartner;

        if ($beneficiarioId) {
            return [
                'charge_amount' => round((float) $finalPrice, 2),
                'commission_amount' => round(max(0, (float) $finalPrice - (float) $priceAfterSuperPartner), 2),
            ];
        }

        if ($superPartnerId) {
            return [
                'charge_amount' => round((float) $priceAfterSuperPartner, 2),
                'commission_amount' => round(max(0, (float) $priceAfterSuperPartner - (float) $adminPrice), 2),
            ];
        }

        return [
            'charge_amount' => round((float) $finalPrice, 2),
            'commission_amount' => 0.0,
        ];
    }

    private function calculateFreeEsimCommissionAmount(array $brandingContext, ?Cliente $cliente = null): float
    {
        $beneficiario = $brandingContext['beneficiario'] ?? null;
        $superPartner = $brandingContext['superPartner'] ?? null;

        if (!$beneficiario && $cliente && $cliente->beneficiario) {
            $beneficiario = $cliente->beneficiario;
            $superPartner = $beneficiario->superPartner;
        }

        if (!$superPartner && $beneficiario) {
            $superPartner = $beneficiario->superPartner;
        }

        if ($beneficiario) {
            return $this->resolveLegacyFreeEsimPrice($beneficiario, (float) $beneficiario->free_esim_rate);
        }

        if ($superPartner) {
            return $this->resolveLegacyFreeEsimPrice($superPartner, (float) $superPartner->free_esim_rate);
        }

        return round((float) Beneficiario::DEFAULT_FREE_ESIM_RATE, 2);
    }

    private function resolveLegacyFreeEsimPrice($owner, float $fallback): float
    {
        if (method_exists($owner, 'getAttribute')) {
            $configuredPrice = $owner->getAttribute('free_esim_price');

            if ($configuredPrice !== null && $configuredPrice !== '') {
                return round((float) $configuredPrice, 2);
            }
        }

        return round($fallback, 2);
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

    private function sanitizeTransactionData(array $transactionData): array
    {
        static $transactionColumns;

        if ($transactionColumns === null) {
            $transactionColumns = array_flip(Schema::getColumnListing('transactions'));
        }

        return array_intersect_key($transactionData, $transactionColumns);
    }

    /**
     * Mostrar el formulario de registro de eSIM
     * 
     * Public form - no authentication required.
     * Email validation happens on form submission.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string|null $referralCode
     * @return \Illuminate\View\View
     */
    public function mostrarFormulario(HttpRequest $request, $referralCode = null)
    {
        $brandingContext = $this->resolveBrandingContext($referralCode);
        $this->syncPartnerContext($brandingContext, $this->extractCodigoFromReferralCode($referralCode));
        
        // Get all countries so non-free destinations can redirect to the plans catalog.
        $affordableCountries = CountryTariffHelper::getAllCountries();
        $stripePublicKey = app(StripeService::class)->getPublishableKey();
        
        return view('clientes.registro-esim', [
            'beneficiario' => $brandingContext['beneficiario'],
            'superPartner' => $brandingContext['superPartner'],
            'brandPartner' => $brandingContext['brandPartner'],
            'referralCode' => $referralCode,
            'parametro' => $request->query('parametro', ''),
            'affordableCountries' => $affordableCountries,
            'stripePublicKey' => $stripePublicKey,
        ]);
    }

    /**
     * Registrar un nuevo cliente desde el formulario público e intentar activar eSIM
     * 
     * Flow:
     * 1. Validate form data
     * 2. Check if email already exists:
     *    - If exists and can_activate_free_esim is false -> redirect to planes
     *    - If exists and can_activate_free_esim is true -> activate eSIM and disable flag
     *    - If new email -> register normally
     * 
     * @param HttpRequest $request
     * @param ClienteService $service
     * @param EsimFxService $esimService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function registrarCliente(HttpRequest $request, ClienteService $service, EsimFxService $esimService)
    {
        try {
            $emailDeliveryStatus = null;

            // 1. Validar datos del formulario (email sin unique, lo validamos manualmente)
            $validated = $request->validate([
                'identificador' => 'required|string|max:255',
                'email' => 'required|email',
                'country_code' => 'required|string|max:2',
                'referralCode' => 'nullable|string'
            ]);

            // Buscar referralCode si existe (para usarlo en la vista)
            $brandingContext = $this->resolveBrandingContext($validated['referralCode'] ?? null);
            $this->syncPartnerContext($brandingContext, $this->extractCodigoFromReferralCode($validated['referralCode'] ?? null));
            $beneficiario = $brandingContext['beneficiario'];
            $selectedCountryCode = strtoupper($validated['country_code']);

            if (!CountryTariffHelper::isAffordableCountryCode($selectedCountryCode)) {
                $routeParams = [];

                if (!empty($validated['referralCode'])) {
                    $routeParams['referralCode'] = $validated['referralCode'];
                }

                $routeParams['country'] = $selectedCountryCode;

                return redirect()->route('planes.index', $routeParams)
                    ->with('success', 'Este pais no aplica para eSIM gratis. Te mostramos los planes disponibles para ese destino.')
                    ->withInput();
            }

            // 2. Verificar si el email ya existe
            //

            $existingCliente = Cliente::where('email', $validated['email'])->first();

            if ($existingCliente && !empty($existingCliente->identificador) && $existingCliente->identificador !== $validated['identificador']) {
                return redirect()->back()
                    ->with('error', 'El identificador ingresado no coincide con el registrado para este cliente.')
                    ->withInput();
            }

            // agrega la validacion de que si no encontro el cliente te mande tambien a la vista de planes, esto para evitar que alguien pueda usar un email existente para activar la eSIM gratuita
            if (!$existingCliente || !$existingCliente->can_activate_free_esim) {
                return redirect()->back()
                    ->with('error', 'No tienes permiso para activar una eSIM gratuita. Te mostramos los planes disponibles para el país que seleccionaste.')
                    ->with('show_available_plans', true)
                    ->with('selected_country', $selectedCountryCode)
                    ->withInput();
                
            } 

            $cliente =  $existingCliente;

            if (empty($cliente->identificador)) {
                $cliente->identificador = $validated['identificador'];
                $cliente->save();
            }

            // Variable para almacenar datos de eSIM
            $esimDataView = null;
            // 3. Buscar producto por país
            if ($request->filled('country_code') && $existingCliente) {
                try {
                    $countryCode = $selectedCountryCode;
                    $preferredCapacity = $this->resolveFreeEsimCapacityForCliente($cliente);

                    // Obtener productos del país desde la API
                    Log::info("Buscando productos para país: {$countryCode} con capacidad gratuita {$preferredCapacity}GB");
                    $products = $esimService->getProducts([
                        'countries' => $countryCode
                    ]);

                    $selectedProduct = is_array($products)
                        ? $this->selectFreeEsimProduct($products, $preferredCapacity)
                        : null;

                    if (!$selectedProduct && isset($products[0])) {
                        $selectedProduct = $products[0];
                    }

                    if ($selectedProduct && (int) ($selectedProduct['amount'] ?? 0) !== $preferredCapacity) {
                        Log::warning("No se encontró producto {$preferredCapacity}GB para {$countryCode}, usando {$selectedProduct['amount']}{$selectedProduct['amount_unit']}");
                    }

                    if ($selectedProduct) {
                        $transactionContext = $this->resolveTransactionContext($brandingContext, $cliente);
                        $pricingSnapshot = $this->calculateFreeEsimPricingSnapshot(
                            $selectedProduct,
                            $transactionContext['beneficiario_id'],
                            $transactionContext['super_partner_id'],
                            $brandingContext,
                            $cliente
                        );
                        $productId = $selectedProduct['id'];
                        Log::info("Producto seleccionado: {$productId}");

                        // Generar ID de transacción único
                        $transactionId = 'WEB-' . $cliente->id . '-' . time();

                        // Crear orden en eSIM FX
                        $apiResponse = $esimService->createOrder($productId, $transactionId);

                        if (empty($apiResponse['id'])) {
                            Log::error('La API de eSIM no devolvió un ID de orden válido.', ['response' => $apiResponse]);

                            return redirect()->back()
                                ->with('error', 'No fue posible crear la orden de eSIM. Inténtalo nuevamente en unos minutos.')
                                ->withInput();
                        }
                        
                        // Activar la suscripción
                        $activate = $esimService->activateOrder($apiResponse['id']);

                        $esimPayload = $this->resolveEsimPayload($apiResponse, is_array($activate) ? $activate : []);

                        if ($esimPayload) {
                            // Guardar datos técnicos en la transacción
                            $transactionData = $this->sanitizeTransactionData([
                                'order_id' => $apiResponse['id'],
                                'transaction_id' => $transactionId,
                                'status' => $apiResponse['status'] ?? 'completed',
                                'iccid' => $esimPayload['iccid'] ?? null,
                                'esim_qr' => $esimPayload['esim_qr'] ?? null,
                                'creation_time' => now(),
                                'cliente_id' => $cliente->id,
                                'beneficiario_id' => $transactionContext['beneficiario_id'],
                                'super_partner_id' => $transactionContext['super_partner_id'],
                                'plan_name' => $selectedProduct['name'] ?? null,
                                'data_amount' => $selectedProduct['amount'] ?? $selectedProduct['data_amount'] ?? null,
                                'duration_days' => $selectedProduct['duration'] ?? $selectedProduct['validity_period'] ?? null,
                                'purchase_amount' => 0,
                                'reference_purchase_amount' => $pricingSnapshot['charge_amount'],
                                'beneficiary_commission_amount' => $pricingSnapshot['charge_amount'],
                                'currency' => 'USD',
                            ]);

                            Transaction::create($transactionData);

                            // Sync client-partner association in pivot table
                            if ($transactionContext['beneficiario_id']) {
                                $cliente->partners()->syncWithoutDetaching([$transactionContext['beneficiario_id']]);
                            }

                            // Generar código QR
                            $qrValue = $esimPayload['esim_qr'] ?? null;

                            if (!$qrValue) {
                                Log::warning('No se recibió esim_qr en la activación gratuita.', [
                                    'order_id' => $apiResponse['id'],
                                    'activation_response' => $activate,
                                    'order_response' => $apiResponse,
                                ]);

                                return redirect()->back()
                                    ->with('error', 'La eSIM fue creada pero no se recibieron los datos del QR de activación.')
                                    ->withInput();
                            }

                            $qrImage = QrCode::size(300)->generate($qrValue);

                            // Separar datos para instalación manual
                            // Formato esperado: LPA:1$smdp.address$activationCode
                            $parts = explode('$', $qrValue);
                            
                            // Validar que tenemos las partes necesarias
                            if (count($parts) < 3) {
                                Log::warning("Formato de QR inesperado: " . $qrValue);
                            }

                            // Preparar datos para la vista
                            $esimDataView = [
                                'qr_svg' => (string) $qrImage,
                                'smdp' => $parts[1] ?? 'N/A',
                                'code' => $parts[2] ?? 'N/A',
                                'iccid' => $esimPayload['iccid'] ?? 'N/A',
                                'data_amount' => $selectedProduct['amount'] ?? $selectedProduct['data_amount'] ?? null,
                                'duration_days' => $selectedProduct['duration'] ?? $selectedProduct['validity_period'] ?? null,
                            ];

                            try {
                                $cachedMailSettings = cache()->get('app-delivery-settings');


                                Mail::to($cliente->email)->send(new EsimActivationMail(
                                    $esimDataView,
                                    $cliente->email,
                                    $brandingContext['brandPartner']->nombre ?? null
                                ));

                                $emailDeliveryStatus = [
                                    'sent' => true,
                                    'message' => 'Se ha enviado por correo los datos para activar la eSIM.',
                                ];
                            } catch (\Throwable $mailException) {
                                Log::error('No fue posible enviar el correo de activacion de eSIM.', [
                                    'cliente_id' => $cliente->id,
                                    'email' => $cliente->email,
                                    'message' => $mailException->getMessage(),
                                ]);
                                dd($mailException->getMessage());
                                $emailDeliveryStatus = [
                                    'sent' => false,
                                    'message' => 'La eSIM se activo correctamente, pero no fue posible enviar el correo con los datos de activacion.',
                                ];
                            }

                            // If this client has the can_activate_free_esim flag, deactivate it after successful activation
                            if ($cliente->can_activate_free_esim) {
                                $cliente->can_activate_free_esim = false;
                                $cliente->save();
                            }

                            Log::info("eSIM activada exitosamente para cliente ID: {$cliente->id}");
                        } else {
                            Log::warning("No se recibieron datos de eSIM en la respuesta de la API");

                            return redirect()->back()
                                ->with('error', 'La eSIM fue procesada pero no se recibieron los datos de activación. Inténtalo nuevamente o contacta soporte.')
                                ->withInput();
                        }
                    } else {
                        Log::error("No se encontraron productos disponibles para el país: {$countryCode}");

                        return redirect()->back()
                            ->with('error', 'No encontramos un plan gratuito disponible para el país seleccionado.')
                            ->withInput();
                    }

                } catch (\Exception $e) {
                    Log::error("Error al activar eSIM: " . $e->getMessage());

                    return redirect()->back()
                        ->with('error', 'Ocurrió un error al activar la eSIM. Por favor, inténtalo nuevamente.')
                        ->withInput();
                }
            }

            if (!$esimDataView) {
                return redirect()->back()
                    ->with('error', 'No fue posible completar la activación de la eSIM.')
                    ->withInput();
            }

            // Get affordable countries (tariff <= $0.67)
            $affordableCountries = CountryTariffHelper::getAllCountries();
            $stripePublicKey = app(StripeService::class)->getPublishableKey();
            
            // Retornar la vista con los datos
            return view('clientes.registro-esim', [
                'esim_data' => $esimDataView,
                'esim_email_status' => $emailDeliveryStatus,
                'beneficiario' => $beneficiario,
                'superPartner' => $brandingContext['superPartner'],
                'brandPartner' => $brandingContext['brandPartner'],
                'referralCode' => $request->referralCode,
                'parametro' => $request->query('parametro', ''),
                'affordableCountries' => $affordableCountries,
                'stripePublicKey' => $stripePublicKey,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error("Error en registro de cliente: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar su registro. Por favor, inténtelo nuevamente.')
                ->withInput();
        }
    }
}