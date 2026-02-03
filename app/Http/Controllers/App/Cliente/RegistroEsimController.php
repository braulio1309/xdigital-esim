<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Cliente\ClienteService;
use Illuminate\Http\Request as HttpRequest;
// Importaciones necesarias
use App\Services\EsimFxService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        
        return view('clientes.registro-esim', [
            'beneficiario' => $beneficiario,
            'referralCode' => $referralCode,
            'parametro' => $request->query('parametro', '')
        ]);
    }

    /**
     * Registrar un nuevo cliente desde el formulario público e intentar activar eSIM
     * * @param Request $request
     * @param ClienteService $service
     * @param EsimFxService $esimService  <-- Inyectamos el servicio aquí
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registrarCliente(Request $request, ClienteService $service, EsimFxService $esimService)
    {
        // Extract beneficiario_id from referralCode if provided
        $beneficiarioId = null;
        if ($request->filled('referralCode')) {
            $codigo = $this->extractCodigoFromReferralCode($request->referralCode);
            $beneficiario = Beneficiario::where('codigo', $codigo)->first();

            if ($beneficiario) {
                $beneficiarioId = $beneficiario->id;
            }
        }
        
        // Merge beneficiario_id into request
        if ($beneficiarioId) {
            $request->merge(['beneficiario_id' => $beneficiarioId]);
        }
        
        // 1. Guardar el cliente en BD local
        $cliente = $service->save();
        
        $esimDataView = null; // Variable para guardar datos que enviaremos a la vista

        // 2. Verificar si se seleccionó un plan (product_id)
        if ($request->filled('product_id') || true) {
           try {
                $productId = 'd06b781f-579a-4804-be03-2773b152525a';
                // Generar ID de transacción único
                $transactionId = 'WEB-' . $cliente->id . '-' . time();

                // 3. Llamar al servicio de eSIM
                // NOTA: Asegúrate de que tu EsimFxService tenga la corrección del payload ('product' => ['id'...])
                $apiResponse = $esimService->createOrder($productId, $transactionId);
                $activate = $esimService->activateOrder($apiResponse['id']);

                if (isset($apiResponse['esim'])) {
                    // 4. Guardar datos técnicos en el cliente
                    $qrImage = QrCode::size(300)->generate($apiResponse['esim']['esim_qr']);

                    // Separamos los datos para instalación manual
                    // Formato esperado: LPA:1$smdp.address$activationCode
                    $parts = explode('$', $apiResponse['esim']['esim_qr']);
                    
                    $esimDataView = [
                        'qr_svg' => (string) $qrImage, // Convertimos a string para pasar a la vista
                        'smdp' => $parts[1] ?? 'N/A',
                        'code' => $parts[2] ?? 'N/A',
                        'iccid' => $apiResponse['esim']['iccid'] ?? 'N/A'
                    ];

                    // 4. Guardamos la transacción en la tabla transactions
                    Transaction::create([
                        'order_id' => $apiResponse['id'], // Asumiendo que no hay una orden asociada en este contexto
                        'transaction_id' => $transactionId,
                        'status' => $apiResponse['status'] ?? 'completed',
                        'iccid' => $apiResponse['esim']['iccid'] ?? null,
                        'esim_qr' => $apiResponse['esim']['esim_qr'] ?? null,
                        'creation_time' => now(),
                        'cliente_id' => $cliente->id
                    ]);
                
                }

            } catch (\Exception $e) {
                Log::error("Error al activar eSIM en registro público: " . $e->getMessage());
                // Podrías retornar con un error, pero como el cliente YA se creó, 
                // es mejor avisar que contacte a soporte.
                return redirect()->back()
                    ->with('warning', 'Cliente registrado, pero hubo un error generando la eSIM. Por favor contacte a soporte.');
            }
        }

        // 6. Redirección con datos
        // Usamos 'with' para flashear los datos a la sesión.
        // La vista podrá acceder a ellos con session('esim_success')
       return view('clientes.registro-esim', [
            'parametro' => $request->query('parametro', ''), // Pasamos el parámetro original
            'esim_data' => $esimDataView,                    // Pasamos la data de la eSIM como variable
            'success'   => '¡Registro exitoso! Tu eSIM ha sido generada.' // Mensaje de éxito
        ]);

    }
}