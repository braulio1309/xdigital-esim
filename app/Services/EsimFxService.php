<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class EsimFxService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientKey;

    public function __construct()
    {
        // Configura estas variables en tu archivo .env
        $this->baseUrl = config('services.esimfx.base_url', 'https://api.esimfx.com'); 
        $this->clientId = config('services.esimfx.client_id', '7f4b881c-85fb-44b2-850c-10b2479a82b5');
        $this->clientKey = config('services.esimfx.client_key', 'b81889d2-8400-41eb-8783-bdf118a1810b');
    }

    /**
     * Paso 1: Obtener Token de Autenticación
     * Usa Cache para no pedir el token en cada petición.
     */
    protected function getAccessToken()
    {
        return Cache::remember('esimfx_token', 3000, function () {
            $response = Http::post("{$this->baseUrl}/account/api/v1/auth", [
                'client_id' => $this->clientId,
                'client_key' => $this->clientKey,
            ]);

            if ($response->failed()) {
                Log::error('Error Auth eSIMfx: ' . $response->body());
                throw new Exception("Fallo en la autenticación con eSIMfx");
            }

            // Según la documentación, el token viene en 'data.access_token'
            return $response->json()['data']['access_token']; 
        });
    }

    /**
     * Método Auxiliar para headers estándar
     */
    protected function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Paso 2: Crear la orden.
     *
     * Soporta:
     * - NEW: crea una eSIM nueva.
     * - TOPUP: recarga una eSIM existente y requiere ICCID.
     *
     * @param string $productId El ID del plan a comprar (e.g. "1GB USA")
     * @param string $transactionId Tu referencia única para este cliente/orden
     * @param array $extraParams Parámetros adicionales enviados a la API.
     * @return array
     * @throws Exception
     */
    public function createOrder($productId, $transactionId, $extraParams = [])
    {
        $endpoint = "{$this->baseUrl}/order/api/v1/create_order";
        $operationType = strtoupper((string) ($extraParams['operation_type'] ?? 'NEW'));

        if ($operationType === 'TOPUP' && empty($extraParams['iccid'])) {
            throw new Exception('ICCID is required for TOPUP orders');
        }

        $payload = array_merge([
            'product' => [
                'id' => $productId
            ],
            'transaction_id' => $transactionId,
            'count' => 1,
            'operation_type' => $operationType,
        ], $extraParams);

        $response = Http::withHeaders($this->getHeaders())
                        ->post($endpoint, $payload);

        if ($response->failed()) {
            Log::error('Error createOrder eSIMfx: ' . $response->body(), [
                'operation_type' => $operationType,
                'product_id' => $productId,
                'transaction_id' => $transactionId,
                'iccid' => $payload['iccid'] ?? null,
            ]);

            throw new Exception('Error creating order in eSIMfx');
        }

        return $response->json()['data'];
    }

    public function activateOrder($orderId)
    {
        $endpoint = "{$this->baseUrl}/order/api/v1/activate_subscription";

        // Estructura del body para "Cualquier Plan/Cliente"
        $payload = array_merge([
            'order_id' => $orderId      // Posiblemente requerido según pag 17
        ]);

        $response = Http::withHeaders($this->getHeaders())
                        ->post($endpoint, $payload);


        return $response->json()['data'];
    }

    /**
     * Get order detail from external API.
     * GET /order/api/v1/get_order
     *
     * @param string $orderId The order identifier
     * @return array
     */
    public function getOrder($orderId)
    {
        $response = Http::withHeaders($this->getHeaders())
                        ->get("{$this->baseUrl}/order/api/v1/get_order", [
                            'order_id' => $orderId,
                        ]);

        if ($response->failed()) {
            Log::error('Error getOrder eSIMfx: ' . $response->body());
            throw new Exception("Error retrieving order detail");
        }

        return $response->json()['data'];
    }
    
    /**
     * Get eSIM status from external API.
     * GET /order/api/v1/get_esim_status
     *
     * @param string $iccid The eSIM identifier
     * @return array
     */
    public function getEsimStatus($iccid)
    {
        $response = Http::withHeaders($this->getHeaders())
                        ->get("{$this->baseUrl}/order/api/v1/get_esim_status", [
                            'iccid' => $iccid,
                        ]);

        if ($response->failed()) {
            Log::error('Error getEsimStatus eSIMfx: ' . $response->body());
            throw new Exception("Error retrieving eSIM status");
        }

        return $response->json()['data'];
    }

    /**
     * Terminate eSIM subscription.
     * POST /order/api/v1/terminate_subscription
     *
     * @param string $orderId The order identifier
     * @return array
     */
    public function terminateSubscription($orderId)
    {
        $response = Http::withHeaders($this->getHeaders())
                        ->post("{$this->baseUrl}/order/api/v1/terminate_subscription", [
                            'order_id' => $orderId,
                        ]);

        if ($response->failed()) {
            Log::error('Error terminateSubscription eSIMfx: ' . $response->body());
            throw new Exception("Error terminating subscription");
        }

        return $response->json()['data'];
    }

    /**
     * (Opcional) Obtener catálogo de productos
     * Útil si quieres listar los planes dinámicamente en tu frontend.
     */
    public function getProducts($filters = [])
    {
         $response = Http::withHeaders($this->getHeaders())
                         ->post("{$this->baseUrl}/product/api/v1/get_products", $filters);
         
         return $response->json()['data']['products'] ?? [];
    }
}