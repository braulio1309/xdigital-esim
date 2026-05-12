<?php

namespace App\Http\Controllers\App\Cliente;

use App\Helpers\CountryTariffHelper;
use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\ClienteService;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\BeneficiaryPriceService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\App\Settings\SuperPartnerPlanMarginService;
use App\Services\App\Settings\SuperPartnerPriceService;
use App\Services\EsimFxService;
use App\Services\StripeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Controlador para gestionar la vista de Planes Disponibles
 * Permite a los usuarios ver planes eSIM por país y comprarlos con Stripe
 */
class PlanesDisponiblesController extends Controller
{
    protected $esimService;
    protected $stripeService;
    protected $clienteService;
    protected $planMarginService;
    protected $beneficiaryPlanMarginService;
    protected $superPartnerPlanMarginService;
    protected $beneficiaryPriceService;
    protected $superPartnerPriceService;

    public function __construct(
        EsimFxService $esimService,
        StripeService $stripeService,
        ClienteService $clienteService,
        PlanMarginService $planMarginService,
        BeneficiaryPlanMarginService $beneficiaryPlanMarginService,
        SuperPartnerPlanMarginService $superPartnerPlanMarginService,
        BeneficiaryPriceService $beneficiaryPriceService,
        SuperPartnerPriceService $superPartnerPriceService
    ) {
        $this->esimService = $esimService;
        $this->stripeService = $stripeService;
        $this->clienteService = $clienteService;
        $this->planMarginService = $planMarginService;
        $this->beneficiaryPlanMarginService = $beneficiaryPlanMarginService;
        $this->superPartnerPlanMarginService = $superPartnerPlanMarginService;
        $this->beneficiaryPriceService = $beneficiaryPriceService;
        $this->superPartnerPriceService = $superPartnerPriceService;
    }

    /**
     * Resolve beneficiary or super partner from a referral code.
     *
     * @param string|null $referralCode
     * @return array{codigo:?string,beneficiario:?Beneficiario,superPartner:?SuperPartner,brandPartner:mixed,partnerContext:array<string,mixed>}
     */
    protected function resolveBrandingContext($referralCode = null): array
    {
        $beneficiario = null;
        $superPartner = null;
        $codigo = null;

        if ($referralCode) {
            $parts = explode('-', $referralCode);
            $codigo = $parts[count($parts) - 1] ?? null;

            if ($codigo) {
                $beneficiario = Beneficiario::where('codigo', $codigo)->first();

                if (!$beneficiario) {
                    $superPartner = SuperPartner::where('codigo', $codigo)->first();
                }
            }
        }

        return [
            'codigo' => $codigo,
            'beneficiario' => $beneficiario,
            'superPartner' => $superPartner,
            'brandPartner' => $beneficiario ?: $superPartner,
            'partnerContext' => [
                'codigo' => $codigo,
                'beneficiario_id' => $beneficiario ? $beneficiario->id : null,
                'super_partner_id' => $beneficiario ? $beneficiario->super_partner_id : ($superPartner ? $superPartner->id : null),
            ],
        ];
    }

    /**
     * Mostrar la vista principal de planes disponibles
     * Ruta: GET /planes-disponibles
     * 
     * @return \Illuminate\View\View
     */
    /**
     * Mostrar la vista principal de planes disponibles
     * Ruta: GET /planes-disponibles/{referralCode?}
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $referralCode = null)
    {
        $brandingContext = $this->resolveBrandingContext($referralCode);
        $partnerContext = $brandingContext['partnerContext'];
        $initialCountry = strtoupper((string) $request->query('country', ''));
        $rechargeContext = $this->syncRechargeContext($request);

        if (strlen($initialCountry) !== 2) {
            $initialCountry = '';
        }

        // Persistir contexto de partner para siguientes llamadas (planes, pagos, etc.)
        if ($partnerContext['beneficiario_id'] || $partnerContext['super_partner_id']) {
            session(['planes_partner_context' => $partnerContext]);
        } else {
            session()->forget('planes_partner_context');
        }

        // Obtener la clave pública de Stripe para el frontend
        $stripePublicKey = $this->stripeService->getPublishableKey();

        // Get all countries with tariff information
        $allCountries = CountryTariffHelper::getAllCountries();

        return view('clientes.planes-disponibles', [
            'stripePublicKey' => $stripePublicKey,
            'allCountries' => $allCountries,
            'initialCountry' => $initialCountry,
            'rechargeContext' => $rechargeContext,
            'partnerContext' => $partnerContext,
            'beneficiario' => $brandingContext['beneficiario'],
            'superPartner' => $brandingContext['superPartner'],
            'brandPartner' => $brandingContext['brandPartner'],
            'referralCode' => $referralCode,
        ]);
    }

    /**
     * Resolver IDs de beneficiario y super partner tomando en cuenta
     * el cliente autenticado y el contexto de partner en sesión.
     *
     * @return array [beneficiarioId|null, superPartnerId|null]
     */
    protected function resolveBeneficiarioAndSuperPartnerIds(): array
    {
        $beneficiarioId = null;
        $superPartnerId = null;

        $user = Auth::user();
        $cliente = $user ? $user->cliente : null;

        if ($cliente && $cliente->beneficiario) {
            $beneficiarioId = $cliente->beneficiario->id;
            $superPartnerId = $cliente->beneficiario->super_partner_id;
        }

        $partnerContext = session('planes_partner_context');

        if (is_array($partnerContext)) {
            if (!$beneficiarioId && !empty($partnerContext['beneficiario_id'])) {
                $beneficiarioId = $partnerContext['beneficiario_id'];
            }

            if (!$superPartnerId && !empty($partnerContext['super_partner_id'])) {
                $superPartnerId = $partnerContext['super_partner_id'];
            }
        }

        return [$beneficiarioId, $superPartnerId];
    }

    protected function calculateFreeEsimCommissionAmount(?int $beneficiarioId, ?int $superPartnerId): float
    {
        if ($beneficiarioId) {
            $beneficiario = Beneficiario::find($beneficiarioId);

            if ($beneficiario) {
                return $this->resolveLegacyFreeEsimPrice($beneficiario, (float) $beneficiario->free_esim_rate);
            }
        }

        if ($superPartnerId) {
            $superPartner = SuperPartner::find($superPartnerId);

            if ($superPartner) {
                return $this->resolveLegacyFreeEsimPrice($superPartner, (float) $superPartner->free_esim_rate);
            }
        }

        return round((float) Beneficiario::DEFAULT_FREE_ESIM_RATE, 2);
    }

    protected function resolveLegacyFreeEsimPrice($owner, float $fallback): float
    {
        if (method_exists($owner, 'getAttribute')) {
            $configuredPrice = $owner->getAttribute('free_esim_price');

            if ($configuredPrice !== null && $configuredPrice !== '') {
                return round((float) $configuredPrice, 2);
            }
        }

        return round($fallback, 2);
    }

    protected function calculateFreeEsimPricingSnapshot(float $originalPrice, $planCapacity, ?int $beneficiarioId, ?int $superPartnerId, ?string $countryCode = null): array
    {
        $capacityAsInt = (int) $planCapacity;
        $normalizedCapacity = (string) $planCapacity;

        if ($capacityAsInt <= 1) {
            if ($beneficiarioId && $countryCode) {
                $countryFixedPrice = app(BeneficiaryPriceService::class)->getCountryFixedPrice($beneficiarioId, $normalizedCapacity, $countryCode);
                if ($countryFixedPrice !== null) {
                    return [
                        'charge_amount' => round($countryFixedPrice, 2),
                        'commission_amount' => round($countryFixedPrice, 2),
                    ];
                }

                $countryPct = app(BeneficiaryPriceService::class)->getCountryPercentage($beneficiarioId, $normalizedCapacity, $countryCode);
                if ($countryPct !== null) {
                    $finalPrice = $originalPrice / (1 - $countryPct / 100);

                    return [
                        'charge_amount' => round((float) $finalPrice, 2),
                        'commission_amount' => round(max(0, (float) $finalPrice - (float) $originalPrice), 2),
                    ];
                }
            }

            if ($superPartnerId && $countryCode) {
                $countryFixedPrice = app(SuperPartnerPriceService::class)->getCountryFixedPrice($superPartnerId, $normalizedCapacity, $countryCode);
                if ($countryFixedPrice !== null) {
                    return [
                        'charge_amount' => round($countryFixedPrice, 2),
                        'commission_amount' => round($countryFixedPrice, 2),
                    ];
                }

                $countryPct = app(SuperPartnerPriceService::class)->getCountryPercentage($superPartnerId, $normalizedCapacity, $countryCode);
                if ($countryPct !== null) {
                    $finalPrice = $originalPrice / (1 - $countryPct / 100);

                    return [
                        'charge_amount' => round((float) $finalPrice, 2),
                        'commission_amount' => round(max(0, (float) $finalPrice - (float) $originalPrice), 2),
                    ];
                }
            }

            $flatRate = $this->calculateFreeEsimCommissionAmount($beneficiarioId, $superPartnerId);

            return [
                'charge_amount' => $flatRate,
                'commission_amount' => $flatRate,
            ];
        }

        $adminPrice = $this->planMarginService->calculateFinalPrice($originalPrice, $normalizedCapacity);

        // Check for country-specific percentage (overrides admin and partner margins)
        $countryPercentageApplied = false;
        $finalPrice = $adminPrice;

        if ($beneficiarioId && $countryCode) {
            $countryPct = $this->beneficiaryPriceService->getCountryPercentage($beneficiarioId, $normalizedCapacity, $countryCode);
            if ($countryPct !== null) {
                $finalPrice = $originalPrice / (1 - $countryPct / 100);
                $countryPercentageApplied = true;
            }
        }

        if (!$countryPercentageApplied && $superPartnerId && $countryCode) {
            $countryPct = $this->superPartnerPriceService->getCountryPercentage($superPartnerId, $normalizedCapacity, $countryCode);
            if ($countryPct !== null) {
                $finalPrice = $originalPrice / (1 - $countryPct / 100);
                $countryPercentageApplied = true;
            }
        }

        if (!$countryPercentageApplied) {
            $priceAfterSuperPartner = $superPartnerId
                ? $this->superPartnerPlanMarginService->calculateFinalPrice($adminPrice, $normalizedCapacity, $superPartnerId)
                : $adminPrice;
            $finalPrice = $beneficiarioId
                ? $this->beneficiaryPlanMarginService->calculateFinalPrice($priceAfterSuperPartner, $normalizedCapacity, $beneficiarioId)
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

        return [
            'charge_amount' => round((float) $finalPrice, 2),
            'commission_amount' => round(max(0, (float) $finalPrice - (float) $originalPrice), 2),
        ];
    }

    protected function calculatePricingSnapshot(float $originalPrice, $planCapacity, ?int $beneficiarioId, ?int $superPartnerId, ?string $countryCode = null): array
    {
        $normalizedCapacity = (string) $planCapacity;

        if ((int) $normalizedCapacity <= 1) {
            return $this->calculateFreeEsimPricingSnapshot(
                $originalPrice,
                $normalizedCapacity,
                $beneficiarioId,
                $superPartnerId,
                $countryCode
            );
        }

        $adminPrice = $this->planMarginService->calculateFinalPrice($originalPrice, $normalizedCapacity);

        if ($beneficiarioId && $countryCode) {
            $countryPct = $this->beneficiaryPriceService->getCountryPercentage($beneficiarioId, $normalizedCapacity, $countryCode);
            if ($countryPct !== null) {
                $finalPrice = $originalPrice / (1 - $countryPct / 100);

                return [
                    'charge_amount' => round((float) $finalPrice, 2),
                    'commission_amount' => round(max(0, (float) $finalPrice - (float) $originalPrice), 2),
                ];
            }
        }

        if ($superPartnerId && $countryCode) {
            $countryPct = $this->superPartnerPriceService->getCountryPercentage($superPartnerId, $normalizedCapacity, $countryCode);
            if ($countryPct !== null) {
                $finalPrice = $originalPrice / (1 - $countryPct / 100);

                return [
                    'charge_amount' => round((float) $finalPrice, 2),
                    'commission_amount' => round(max(0, (float) $finalPrice - (float) $originalPrice), 2),
                ];
            }
        }

        if ($beneficiarioId) {
            $manualPrice = $this->beneficiaryPriceService->resolvePrice($beneficiarioId, $normalizedCapacity, null);
            if ($manualPrice !== null) {
                return [
                    'charge_amount' => round((float) $manualPrice, 2),
                    'commission_amount' => round((float) $manualPrice, 2),
                ];
            }
        } elseif ($superPartnerId) {
            $manualPrice = $this->superPartnerPriceService->resolvePrice($superPartnerId, $normalizedCapacity, null);
            if ($manualPrice !== null) {
                return [
                    'charge_amount' => round((float) $manualPrice, 2),
                    'commission_amount' => round((float) $manualPrice, 2),
                ];
            }
        }

        $priceAfterSuperPartner = $superPartnerId
            ? $this->superPartnerPlanMarginService->calculateFinalPrice($adminPrice, $normalizedCapacity, $superPartnerId)
            : $adminPrice;

        $finalPrice = $beneficiarioId
            ? $this->beneficiaryPlanMarginService->calculateFinalPrice($priceAfterSuperPartner, $normalizedCapacity, $beneficiarioId)
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

    protected function sanitizeTransactionData(array $transactionData): array
    {
        static $transactionColumns;

        if ($transactionColumns === null) {
            $transactionColumns = array_flip(Schema::getColumnListing('transactions'));
        }

        return array_intersect_key($transactionData, $transactionColumns);
    }

    protected function emptyRechargeContext(): array
    {
        return [
            'is_recharge' => false,
            'iccid' => null,
            'transaction_id' => null,
        ];
    }

    protected function resolveRechargeContextForIccid(?string $iccid): array
    {
        $normalizedIccid = trim((string) $iccid);

        if ($normalizedIccid === '' || !Auth::check()) {
            return $this->emptyRechargeContext();
        }

        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            return $this->emptyRechargeContext();
        }

        $transaction = Transaction::query()
            ->where('cliente_id', $cliente->id)
            ->where('iccid', $normalizedIccid)
            ->whereNotNull('order_id')
            ->latest('creation_time')
            ->first();

        if (!$transaction) {
            return $this->emptyRechargeContext();
        }

        return [
            'is_recharge' => true,
            'iccid' => $transaction->iccid,
            'transaction_id' => $transaction->transaction_id,
        ];
    }

    protected function syncRechargeContext(Request $request): array
    {
        $requestedIccid = trim((string) $request->query('recharge_iccid', ''));

        if ($requestedIccid === '') {
            session()->forget('planes_recharge_context');

            return $this->emptyRechargeContext();
        }

        $rechargeContext = $this->resolveRechargeContextForIccid($requestedIccid);

        if ($rechargeContext['is_recharge']) {
            session(['planes_recharge_context' => $rechargeContext]);
            return $rechargeContext;
        }

        session()->forget('planes_recharge_context');

        return $this->emptyRechargeContext();
    }

    protected function getRechargeContext(): array
    {
        $rechargeContext = session('planes_recharge_context');

        if (!is_array($rechargeContext) || empty($rechargeContext['is_recharge']) || empty($rechargeContext['iccid'])) {
            return $this->emptyRechargeContext();
        }

        return $this->resolveRechargeContextForIccid($rechargeContext['iccid']);
    }

    /**
     * Obtener planes disponibles por país
     * Ruta: POST /planes/get-by-country
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlanes(Request $request)
    {
        try {
            $request->validate([
                'country' => 'required|string|max:2',
            ]);

            $country = strtoupper($request->country);

            // Obtener productos desde la API de eSIM FX
            $products = $this->esimService->getProducts([
                'countries' => $country,
            ]);

            // Resolver beneficiario y super partner (por cliente autenticado o referral)
            [$beneficiarioId, $superPartnerId] = $this->resolveBeneficiarioAndSuperPartnerIds();

            // Formatear los productos para el frontend
            $formattedProducts = collect($products)->map(function ($product) use ($beneficiarioId, $superPartnerId, $country) {
                // Apply admin profit margin to price
                $originalPrice = $product['price'];
                $planCapacity = $product['amount']; // Amount is in GB (e.g., 1, 3, 5, 10, 20, 50)

                // First, apply admin margin
                $priceWithAdminMargin = $this->planMarginService->calculateFinalPrice($originalPrice, $planCapacity);

                // Check if a country-specific percentage is assigned for the beneficiary or super partner
                $countryPercentageApplied = false;
                $finalPrice = $priceWithAdminMargin;

                if ($beneficiarioId) {
                    $countryPct = $this->beneficiaryPriceService->getCountryPercentage($beneficiarioId, (string) $planCapacity, $country);
                    if ($countryPct !== null) {
                        $finalPrice = $originalPrice / (1 - $countryPct / 100);
                        $countryPercentageApplied = true;
                    }
                }

                if (!$countryPercentageApplied && $superPartnerId) {
                    $countryPct = $this->superPartnerPriceService->getCountryPercentage($superPartnerId, (string) $planCapacity, $country);
                    if ($countryPct !== null) {
                        $finalPrice = $originalPrice / (1 - $countryPct / 100);
                        $countryPercentageApplied = true;
                    }
                }

                // If no country percentage, apply general partner margins (existing behavior)
                $superPartnerMarginApplied = false;
                $beneficiaryMarginApplied = false;

                if (!$countryPercentageApplied) {
                    $priceAfterSuperPartner = $priceWithAdminMargin;

                    if ($superPartnerId) {
                        $priceAfterSuperPartner = $this->superPartnerPlanMarginService->calculateFinalPrice(
                            $priceWithAdminMargin,
                            $planCapacity,
                            $superPartnerId
                        );
                        $superPartnerMarginApplied = ($priceAfterSuperPartner != $priceWithAdminMargin);
                    }

                    $finalPrice = $priceAfterSuperPartner;

                    if ($beneficiarioId) {
                        $finalPrice = $this->beneficiaryPlanMarginService->calculateFinalPrice(
                            $priceAfterSuperPartner,
                            $planCapacity,
                            $beneficiarioId
                        );
                        $beneficiaryMarginApplied = ($finalPrice != $priceAfterSuperPartner);
                    }
                }
                
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'duration' => $product['duration'],
                    'duration_unit' => $product['duration_unit'],
                    'amount' => $product['amount'],
                    'amount_unit' => $product['amount_unit'],
                    'original_price' => round((float) $originalPrice, 2),
                    'price' => round((float) $finalPrice, 2),
                    'price_unit' => $product['price_unit'],
                    'coverage' => $product['coverage'] ?? [],
                    'is_free' => $originalPrice == 0,
                    'margin_applied' => $finalPrice != $originalPrice,
                    'super_partner_margin_applied' => $superPartnerMarginApplied,
                    'beneficiary_margin_applied' => $beneficiaryMarginApplied,
                ];
            })
            // Filter to only show 3GB, 5GB, and 10GB plans
            ->filter(function ($product) {
                $amount = $product['amount'];
                return in_array($amount, [3, 5, 10]);
            })
            ->sort(function ($first, $second) {
                return [
                    (float) $first['price'],
                    (float) $first['amount'],
                    (float) $first['duration'],
                ] <=> [
                    (float) $second['price'],
                    (float) $second['amount'],
                    (float) $second['duration'],
                ];
            })
            ->values(); // Reset array keys

            return response()->json([
                'success' => true,
                'products' => $formattedProducts,
            ]);
        } catch (Exception $e) {
            Log::error('Error obteniendo planes por país: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los planes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Procesar el pago con Stripe y activar eSIM
     * Ruta: POST /planes/procesar-pago
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function procesarPago(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string',
                'payment_intent_id' => 'required|string',
                'plan_name' => 'nullable|string',
                'data_amount' => 'nullable|numeric',
                'duration' => 'nullable|integer',
                'purchase_amount' => 'nullable|numeric',
                'currency' => 'nullable|string|max:3',
                'country' => 'nullable|string|max:2',
                'original_price' => 'nullable|numeric|min:0',
            ]);

            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para completar la compra.',
                ], 401);
            }

            // Obtener el cliente asociado al usuario autenticado
            $user = Auth::user();
            $cliente = $user->cliente;

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un cliente asociado a este usuario.',
                ], 404);
            }

            $rechargeContext = $this->getRechargeContext();
            $isRecharge = $rechargeContext['is_recharge'] && !empty($rechargeContext['iccid']);

            // Verificar el estado del pago en Stripe
            $paymentStatus = $this->stripeService->getPaymentStatus($request->payment_intent_id);

            if ($paymentStatus['status'] !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'El pago no se ha completado correctamente.',
                    'payment_status' => $paymentStatus['status'],
                ], 400);
            }

            // Generar ID de transacción único
            $transactionId = 'STRIPE-' . $cliente->id . '-' . time() . '-' . uniqid();

            $orderPayload = $isRecharge
                ? [
                    'operation_type' => 'TOPUP',
                    'iccid' => $rechargeContext['iccid'],
                ]
                : [];

            // Crear orden en eSIM FX
            $apiResponse = $this->esimService->createOrder(
                $request->product_id,
                $transactionId,
                $orderPayload
            );

            if (empty($apiResponse['id'])) {
                throw new Exception('No se recibió un ID de orden válido desde la API');
            }

            $activateSuscription = $this->esimService->activateOrder(
                $apiResponse['id']
            );

            if ($isRecharge) {
                $esimData = [
                    'is_topup' => true,
                    'iccid' => $rechargeContext['iccid'],
                    'data_amount' => $request->data_amount,
                    'duration_days' => $request->duration,
                ];
            } else {
                if (!isset($apiResponse['esim'])) {
                    throw new Exception('No se recibieron datos de eSIM desde la API');
                }

                // Generar código QR
                $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

                // Separar datos para instalación manual
                // Formato esperado: LPA:1$smdp.address$activationCode
                $parts = explode('$', $apiResponse['esim']['esim_qr']);

                $esimData = [
                    'is_topup' => false,
                    'qr_svg' => (string) $qrImage,
                    'smdp' => $parts[1] ?? 'N/A',
                    'code' => $parts[2] ?? 'N/A',
                    'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A',
                    'data_amount' => $request->data_amount,
                    'duration_days' => $request->duration,
                ];
            }

            // Resolver beneficiario asociado para atribuir comisiones
            [$beneficiarioId, $superPartnerId] = $this->resolveBeneficiarioAndSuperPartnerIds();
            $countryCode = $request->filled('country') ? strtoupper((string) $request->input('country')) : null;
            $originalPrice = $request->filled('original_price')
                ? (float) $request->input('original_price')
                : (float) $request->input('purchase_amount', 0);
            $pricingSnapshot = $this->calculatePricingSnapshot(
                $originalPrice,
                (string) $request->input('data_amount', '0'),
                $beneficiarioId,
                $superPartnerId,
                $countryCode
            );

            // Guardar la transacción en la base de datos
            $transactionData = [
                'order_id' => $apiResponse['id'],
                'transaction_id' => $transactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $isRecharge ? $rechargeContext['iccid'] : ($apiResponse['esim']['iccid'] ?? null),
                'esim_qr' => $isRecharge ? null : ($apiResponse['esim']['esim_qr'] ?? null),
                'creation_time' => now(),
                'cliente_id' => $cliente->id,
                'plan_name' => $request->plan_name,
                'data_amount' => $request->data_amount,
                'duration_days' => $request->duration,
                'purchase_amount' => round((float) $pricingSnapshot['charge_amount'], 2),
                'api_price' => $request->filled('original_price') ? (float) $request->original_price : null,
                'reference_purchase_amount' => round((float) $pricingSnapshot['charge_amount'], 2),
                'beneficiary_commission_amount' => round((float) $pricingSnapshot['commission_amount'], 2),
                'currency' => $request->currency ?? 'USD',
                'country_code' => $countryCode,
            ];

            $saleCommissions = Transaction::calculateSaleCommissions(
                round((float) $pricingSnapshot['charge_amount'], 2),
                $countryCode,
                $beneficiarioId,
                $superPartnerId
            );
            $transactionData['partner_sale_commission_amount'] = $saleCommissions['partner_sale_commission_amount'];
            $transactionData['super_partner_sale_commission_amount'] = $saleCommissions['super_partner_sale_commission_amount'];

            if ($beneficiarioId) {
                $transactionData['beneficiario_id'] = $beneficiarioId;
            }

            if ($superPartnerId) {
                $transactionData['super_partner_id'] = $superPartnerId;
            }

            $transactionData = $this->sanitizeTransactionData($transactionData);

            Transaction::create($transactionData);

            return response()->json([
                'success' => true,
                'message' => $isRecharge
                    ? '¡Pago exitoso! Los datos fueron recargados en tu eSIM actual.'
                    : '¡Pago exitoso! Tu eSIM ha sido activada.',
                'esim_data' => $esimData,
                'is_recharge' => $isRecharge,
            ]);
        } catch (Exception $e) {
            Log::error('Error procesando pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear un Payment Intent para un producto específico
     * Ruta: POST /planes/create-payment-intent
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
            ]);

            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para continuar.',
                ], 401);
            }

            $user = Auth::user();
            $rechargeContext = $this->getRechargeContext();

            // Crear el Payment Intent con metadata
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $request->amount,
                $request->currency,
                [
                    'product_id' => $request->product_id,
                    'user_id' => $user->id,
                    'recharge_iccid' => $rechargeContext['iccid'] ?? '',
                ]
            );

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent['client_secret'],
                'payment_intent_id' => $paymentIntent['payment_intent_id'],
            ]);
        } catch (Exception $e) {
            Log::error('Error creando Payment Intent: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activar un plan gratuito
     * Ruta: POST /planes/activar-gratis
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activarGratis(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string',
                'plan_name' => 'nullable|string',
                'data_amount' => 'nullable|numeric',
                'duration' => 'nullable|integer',
                'original_price' => 'nullable|numeric|min:0',
                'reference_purchase_amount' => 'nullable|numeric|min:0',
                'country' => 'nullable|string|max:2',
            ]);

            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para continuar.',
                ], 401);
            }

            // Obtener el cliente asociado al usuario autenticado
            $user = Auth::user();
            $cliente = $user->cliente;

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un cliente asociado a este usuario.',
                ], 404);
            }

            $rechargeContext = $this->getRechargeContext();
            $isRecharge = $rechargeContext['is_recharge'] && !empty($rechargeContext['iccid']);

            // Generar ID de transacción único
            $transactionId = 'FREE-' . $cliente->id . '-' . time() . '-' . uniqid();

            $orderPayload = $isRecharge
                ? [
                    'operation_type' => 'TOPUP',
                    'iccid' => $rechargeContext['iccid'],
                ]
                : [];

            // Crear orden en eSIM FX (para planes gratuitos)
            $apiResponse = $this->esimService->createOrder(
                $request->product_id,
                $transactionId,
                $orderPayload
            );

            if (empty($apiResponse['id'])) {
                throw new Exception('No se recibió un ID de orden válido desde la API');
            }

            $activateSuscription = $this->esimService->activateOrder(
                $apiResponse['id']
            );

            if ($isRecharge) {
                $esimData = [
                    'is_topup' => true,
                    'iccid' => $rechargeContext['iccid'],
                    'data_amount' => $request->data_amount,
                    'duration_days' => $request->duration,
                ];
            } else {
                if (!isset($apiResponse['esim'])) {
                    throw new Exception('No se recibieron datos de eSIM desde la API');
                }

                // Generar código QR
                $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

                // Separar datos para instalación manual
                $parts = explode('$', $apiResponse['esim']['esim_qr']);

                $esimData = [
                    'is_topup' => false,
                    'qr_svg' => (string) $qrImage,
                    'smdp' => $parts[1] ?? 'N/A',
                    'code' => $parts[2] ?? 'N/A',
                    'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A',
                    'data_amount' => $request->data_amount,
                    'duration_days' => $request->duration,
                ];
            }

            // Resolver beneficiario asociado para atribuir comisiones
            [$beneficiarioId, $superPartnerId] = $this->resolveBeneficiarioAndSuperPartnerIds();
            $countryCode = $request->filled('country') ? strtoupper($request->input('country')) : null;
            $pricingSnapshot = $this->calculateFreeEsimPricingSnapshot(
                (float) $request->input('original_price', 0),
                $request->data_amount,
                $beneficiarioId,
                $superPartnerId,
                $countryCode
            );

            // Guardar la transacción en la base de datos
            $transactionData = [
                'order_id' => $apiResponse['id'],
                'transaction_id' => $transactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $isRecharge ? $rechargeContext['iccid'] : ($apiResponse['esim']['iccid'] ?? null),
                'esim_qr' => $isRecharge ? null : ($apiResponse['esim']['esim_qr'] ?? null),
                'creation_time' => now(),
                'cliente_id' => $cliente->id,
                'plan_name' => $request->plan_name,
                'data_amount' => $request->data_amount,
                'duration_days' => $request->duration,
                'purchase_amount' => 0,
                'api_price' => $request->filled('original_price') ? (float) $request->input('original_price') : null,
                'reference_purchase_amount' => $pricingSnapshot['charge_amount'],
                'beneficiary_commission_amount' => $pricingSnapshot['charge_amount'],
                'currency' => 'USD',
                'country_code' => $countryCode,
                'partner_sale_commission_amount' => 0,
                'super_partner_sale_commission_amount' => 0,
            ];

            if ($beneficiarioId) {
                $transactionData['beneficiario_id'] = $beneficiarioId;
            }

            if ($superPartnerId) {
                $transactionData['super_partner_id'] = $superPartnerId;
            }

            $transactionData = $this->sanitizeTransactionData($transactionData);

            Transaction::create($transactionData);

            return response()->json([
                'success' => true,
                'message' => $isRecharge
                    ? '¡Recarga gratuita aplicada! Los datos fueron agregados a tu eSIM actual.'
                    : '¡Plan gratuito activado! Tu eSIM ha sido generada.',
                'esim_data' => $esimData,
                'is_recharge' => $isRecharge,
            ]);
        } catch (Exception $e) {
            Log::error('Error activando plan gratuito: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al activar el plan gratuito: ' . $e->getMessage(),
            ], 500);
        }
    }
}
