<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use App\Services\EsimFxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;

class PlanesDisponiblesController extends Controller
{
    protected $esimService;

    public function __construct(EsimFxService $esimService)
    {
        $this->esimService = $esimService;
    }

    /**
     * Mostrar la vista principal de planes disponibles
     */
    public function index()
    {
        return view('planes-disponibles');
    }

    /**
     * Obtener planes por país (AJAX)
     */
    public function getPlanes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'country' => 'required|in:ES,US'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'País inválido'
                ], 400);
            }

            $planes = $this->esimService->getProductsByCountry($request->country);

            return response()->json([
                'success' => true,
                'data' => $planes
            ]);

        } catch (Exception $e) {
            Log::error('Error obteniendo planes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los planes'
            ], 500);
        }
    }

    /**
     * Verificar autenticación del usuario
     */
    public function verificarAuth()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? Auth::user() : null
        ]);
    }

    /**
     * Autenticar usuario (login/registro)
     */
    public function autenticar(Request $request)
    {
        try {
            $tipo = $request->input('tipo'); // 'login' o 'registro'

            if ($tipo === 'login') {
                return $this->login($request);
            } elseif ($tipo === 'registro') {
                return $this->registro($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tipo de acción inválido'
            ], 400);

        } catch (Exception $e) {
            Log::error('Error en autenticación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la autenticación'
            ], 500);
        }
    }

    /**
     * Login de usuario
     */
    private function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    /**
     * Registro de usuario y cliente
     */
    private function registro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear usuario
        $user = \App\Models\Core\Auth\User::create([
            'first_name' => $request->nombre,
            'last_name' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status_id' => 1 // Activo
        ]);

        // Crear cliente asociado
        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'user_id' => $user->id
        ]);

        // Login automático
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso',
            'user' => $user
        ]);
    }

    /**
     * Procesar pago con Stripe y crear orden eSIM
     */
    public function procesarPago(Request $request)
    {
        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para continuar'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|string',
                'product_name' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'payment_method_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validar payment_method_id solo si el monto es mayor a 0
            if ($request->amount > 0 && empty($request->payment_method_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago requerido para planes de pago'
                ], 422);
            }

            $user = Auth::user();
            
            // Buscar o crear cliente
            $cliente = Cliente::where('user_id', $user->id)->first();
            if (!$cliente) {
                $cliente = Cliente::create([
                    'nombre' => $user->first_name,
                    'apellido' => $user->last_name,
                    'email' => $user->email,
                    'user_id' => $user->id
                ]);
            }

            $paymentIntentId = null;

            // Procesar pago con Stripe si el monto es mayor a 0
            if ($request->amount > 0) {
                Stripe::setApiKey(config('services.stripe.secret'));

                // Convertir a centavos usando round para evitar problemas de precisión de punto flotante
                $amountInCents = round($request->amount * 100);

                $paymentIntent = PaymentIntent::create([
                    'amount' => $amountInCents,
                    'currency' => 'usd',
                    'payment_method' => $request->payment_method_id,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'description' => 'Plan eSIM: ' . $request->product_name,
                    'metadata' => [
                        'user_id' => $user->id,
                        'cliente_id' => $cliente->id,
                        'product_id' => $request->product_id
                    ]
                ]);

                if ($paymentIntent->status === 'requires_action' && $paymentIntent->next_action->type === 'use_stripe_sdk') {
                    return response()->json([
                        'requires_action' => true,
                        'payment_intent_client_secret' => $paymentIntent->client_secret
                    ]);
                }

                if ($paymentIntent->status !== 'succeeded') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al procesar el pago'
                    ], 400);
                }

                $paymentIntentId = $paymentIntent->id;
            }

            // Crear orden en eSIM API
            $transactionId = 'WEB-' . $cliente->id . '-' . time();
            $apiResponse = $this->esimService->createOrder($request->product_id, $transactionId);

            if (!isset($apiResponse['esim'])) {
                throw new Exception('Error al crear la orden eSIM');
            }

            // Generar QR Code
            $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

            // Parsear datos de instalación manual
            $parts = explode('$', $apiResponse['esim']['esim_qr']);

            // Guardar transacción
            $transaction = Transaction::create([
                'order_id' => $apiResponse['id'] ?? null,
                'transaction_id' => $transactionId,
                'status' => $apiResponse['status'] ?? 'completed',
                'iccid' => $apiResponse['esim']['iccid'] ?? null,
                'esim_qr' => $apiResponse['esim']['esim_qr'] ?? null,
                'creation_time' => now(),
                'cliente_id' => $cliente->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Compra exitosa',
                'data' => [
                    'qr_svg' => (string) $qrImage,
                    'smdp' => $parts[1] ?? 'N/A',
                    'code' => $parts[2] ?? 'N/A',
                    'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A',
                    'payment_intent_id' => $paymentIntentId,
                    'transaction_id' => $transactionId
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error procesando pago: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la compra: ' . $e->getMessage()
            ], 500);
        }
    }
}
