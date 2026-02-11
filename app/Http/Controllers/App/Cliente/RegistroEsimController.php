<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\ClienteService;
use Illuminate\Http\Request as HttpRequest;
// Importaciones necesarias
use App\Services\EsimFxService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\CountryTariffHelper;

class RegistroEsimController extends Controller
{
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
        $beneficiario = null;
        
        if ($referralCode) {
            $codigo = $this->extractCodigoFromReferralCode($referralCode);
            $beneficiario = Beneficiario::where('codigo', $codigo)->first();
        }
        
        // Get affordable countries (tariff <= $0.67)
        $affordableCountries = CountryTariffHelper::getAffordableCountries();
        
        return view('clientes.registro-esim', [
            'beneficiario' => $beneficiario,
            'referralCode' => $referralCode,
            'parametro' => $request->query('parametro', ''),
            'affordableCountries' => $affordableCountries
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
            // 1. Validar datos del formulario (email sin unique, lo validamos manualmente)
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|email',
                'country_code' => 'required|string|max:2',
                'referralCode' => 'nullable|string'
            ]);

            // Buscar referralCode si existe (para usarlo en la vista)
            $beneficiario = null;
            if (!empty($validated['referralCode'])) {
                $codigo = $this->extractCodigoFromReferralCode($validated['referralCode']);
                $beneficiario = Beneficiario::where('codigo', $codigo)->first();
            }

            // 2. Verificar si el email ya existe
            $existingCliente = Cliente::where('email', $validated['email'])->first();
            
            if ($existingCliente) {
                // El cliente ya existe, verificar el flag
                if (!$existingCliente->can_activate_free_esim) {
                    // No tiene permiso para activar eSIM gratuita
                    return redirect()->route('planes.index')
                        ->with('error', 'No tienes permiso para activar una eSIM gratuita. Por favor, contacta al administrador.');
                }
                
                // Tiene permiso, usar el cliente existente
                $cliente = $existingCliente;
            } else {
                // Email nuevo, registrar cliente normalmente
                if ($beneficiario) {
                    $request->merge(['beneficiario_id' => $beneficiario->id]);
                }

                // Guardar cliente (el servicio lee de request()->all())
                $cliente = $service->save();
            }

            // Variable para almacenar datos de eSIM
            $esimDataView = null;

            // 3. Buscar producto por país
            if ($request->filled('country_code')) {
                try {
                    $countryCode = strtoupper($validated['country_code']);

                    // Obtener productos del país desde la API
                    Log::info("Buscando productos para país: {$countryCode}");
                    $products = $esimService->getProducts([
                        'country' => $countryCode
                    ]);

                    // Filtrar el producto de 1GB con 7 días de duración
                    $selectedProduct = null;
                    
                    if (is_array($products) && !empty($products)) {
                        foreach ($products as $product) {
                            // Buscar producto con 1GB y 7 días
                            if (isset($product['data_amount']) && 
                                isset($product['data_unit']) && 
                                isset($product['validity_period']) &&
                                isset($product['validity_unit'])) {
                                
                                // Verificar si es 1GB
                                $isOneGb = ($product['data_amount'] === 1 && 
                                           strtoupper($product['data_unit']) === 'GB');
                                
                                // Verificar si es 7 días
                                $isSevenDays = ($product['validity_period'] === 7 && 
                                               strtoupper($product['validity_unit']) === 'DAYS');
                                
                                if ($isOneGb && $isSevenDays) {
                                    $selectedProduct = $product;
                                    break;
                                }
                            }
                        }
                    }

                    // Si no se encontró producto de 1GB/7días, usar el primero disponible
                    if (!$selectedProduct && isset($products[0])) {
                        $selectedProduct = $products[0];
                        Log::warning("No se encontró producto 1GB/7días para {$countryCode}, usando primer producto disponible");
                    }

                    if ($selectedProduct) {
                        $productId = $selectedProduct['id'];
                        Log::info("Producto seleccionado: {$productId}");

                        // Generar ID de transacción único
                        $transactionId = 'WEB-' . $cliente->id . '-' . time();

                        // Crear orden en eSIM FX
                        $apiResponse = $esimService->createOrder($productId, $transactionId);
                        
                        // Activar la suscripción
                        $activate = $esimService->activateOrder($apiResponse['id']);

                        if (isset($apiResponse['esim'])) {
                            // Guardar datos técnicos en la transacción
                            Transaction::create([
                                'order_id' => $apiResponse['id'],
                                'transaction_id' => $transactionId,
                                'status' => $apiResponse['status'] ?? 'completed',
                                'iccid' => $apiResponse['esim']['iccid'] ?? null,
                                'esim_qr' => $apiResponse['esim']['esim_qr'] ?? null,
                                'creation_time' => now(),
                                'cliente_id' => $cliente->id
                            ]);

                            // Generar código QR
                            $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

                            // Separar datos para instalación manual
                            // Formato esperado: LPA:1$smdp.address$activationCode
                            $parts = explode('$', $apiResponse['esim']['esim_qr']);
                            
                            // Validar que tenemos las partes necesarias
                            if (count($parts) < 3) {
                                Log::warning("Formato de QR inesperado: " . $apiResponse['esim']['esim_qr']);
                            }

                            // Preparar datos para la vista
                            $esimDataView = [
                                'qr_svg' => (string) $qrImage,
                                'smdp' => $parts[1] ?? 'N/A',
                                'code' => $parts[2] ?? 'N/A',
                                'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A'
                            ];

                            // If this client has the can_activate_free_esim flag, deactivate it after successful activation
                            if ($cliente->can_activate_free_esim) {
                                $cliente->can_activate_free_esim = false;
                                $cliente->save();
                            }

                            Log::info("eSIM activada exitosamente para cliente ID: {$cliente->id}");
                        } else {
                            Log::warning("No se recibieron datos de eSIM en la respuesta de la API");
                        }
                    } else {
                        Log::error("No se encontraron productos disponibles para el país: {$countryCode}");
                    }

                } catch (\Exception $e) {
                    Log::error("Error al activar eSIM: " . $e->getMessage());
                    // No lanzamos la excepción para que el registro se complete
                }
            }

            // Get affordable countries (tariff <= $0.67)
            $affordableCountries = CountryTariffHelper::getAffordableCountries();
            
            // Retornar la vista con los datos
            return view('clientes.registro-esim', [
                'esim_data' => $esimDataView,
                'beneficiario' => $beneficiario,
                'referralCode' => $request->referralCode,
                'parametro' => $request->query('parametro', ''),
                'affordableCountries' => $affordableCountries
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