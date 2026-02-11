<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\ClienteService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\EsimFxService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\CountryTariffHelper;
use Exception;

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

    public function __construct(
        EsimFxService $esimService, 
        StripeService $stripeService,
        ClienteService $clienteService,
        PlanMarginService $planMarginService
    ) {
        $this->esimService = $esimService;
        $this->stripeService = $stripeService;
        $this->clienteService = $clienteService;
        $this->planMarginService = $planMarginService;
    }

    /**
     * Mostrar la vista principal de planes disponibles
     * Ruta: GET /planes-disponibles
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener la clave pública de Stripe para el frontend
        $stripePublicKey = $this->stripeService->getPublishableKey();
        
        // Get all countries with tariff information
        $allCountries = CountryTariffHelper::getAllCountries();
        
        return view('clientes.planes-disponibles', [
            'stripePublicKey' => $stripePublicKey,
            'allCountries' => $allCountries
        ]);
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
                'countries' => $country
            ]);

            // Formatear los productos para el frontend
            $formattedProducts = collect($products)->map(function ($product) {
                // Apply profit margin to price
                $originalPrice = $product['price'];
                $planCapacity = $product['amount']; // Amount is in GB (e.g., 1, 3, 5, 10, 20, 50)
                $finalPrice = $this->planMarginService->calculateFinalPrice($originalPrice, $planCapacity);
                
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'duration' => $product['duration'],
                    'duration_unit' => $product['duration_unit'],
                    'amount' => $product['amount'],
                    'amount_unit' => $product['amount_unit'],
                    'original_price' => $originalPrice,
                    'price' => $finalPrice,
                    'price_unit' => $product['price_unit'],
                    'coverage' => $product['coverage'] ?? [],
                    'is_free' => $originalPrice == 0,
                    'margin_applied' => $finalPrice != $originalPrice,
                ];
            })
            // Filter to only show 3GB, 5GB, and 10GB plans
            ->filter(function ($product) {
                $amount = $product['amount'];
                return in_array($amount, [3, 5, 10]);
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

            // Crear orden en eSIM FX
            $apiResponse = $this->esimService->createOrder(
                $request->product_id,
                $transactionId
            );

            $activateSuscription = $this->esimService->activateOrder(
                $apiResponse['id']
            );

            if (!isset($apiResponse['esim'])) {
                throw new Exception('No se recibieron datos de eSIM desde la API');
            }

            // Generar código QR
            $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

            // Separar datos para instalación manual
            // Formato esperado: LPA:1$smdp.address$activationCode
            $parts = explode('$', $apiResponse['esim']['esim_qr']);

            $esimData = [
                'qr_svg' => (string) $qrImage,
                'smdp' => $parts[1] ?? 'N/A',
                'code' => $parts[2] ?? 'N/A',
                'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A'
            ];

            // Guardar la transacción en la base de datos
            Transaction::create([
                'order_id' => $apiResponse['id'],
                'transaction_id' => $transactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $apiResponse['esim']['iccid'] ?? null,
                'esim_qr' => $apiResponse['esim']['esim_qr'] ?? null,
                'creation_time' => now(),
                'cliente_id' => $cliente->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Pago exitoso! Tu eSIM ha sido activada.',
                'esim_data' => $esimData,
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

            // Crear el Payment Intent con metadata
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $request->amount,
                $request->currency,
                [
                    'product_id' => $request->product_id,
                    'user_id' => $user->id,
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

            // Generar ID de transacción único
            $transactionId = 'FREE-' . $cliente->id . '-' . time() . '-' . uniqid();

            // Crear orden en eSIM FX (para planes gratuitos)
            $apiResponse = $this->esimService->createOrder(
                $request->product_id,
                $transactionId
            );

            if (!isset($apiResponse['esim'])) {
                throw new Exception('No se recibieron datos de eSIM desde la API');
            }

            // Generar código QR
            $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

            // Separar datos para instalación manual
            $parts = explode('$', $apiResponse['esim']['esim_qr']);

            $esimData = [
                'qr_svg' => (string) $qrImage,
                'smdp' => $parts[1] ?? 'N/A',
                'code' => $parts[2] ?? 'N/A',
                'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A'
            ];

            // Guardar la transacción en la base de datos
            Transaction::create([
                'order_id' => $apiResponse['id'],
                'transaction_id' => $transactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $apiResponse['esim']['iccid'] ?? null,
                'esim_qr' => $apiResponse['esim']['esim_qr'] ?? null,
                'creation_time' => now(),
                'cliente_id' => $cliente->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Plan gratuito activado! Tu eSIM ha sido generada.',
                'esim_data' => $esimData,
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
