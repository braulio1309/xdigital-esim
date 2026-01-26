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
     * Paso 2: Crear la Orden (Activar eSIM)
     * * @param string $productId El ID del plan a comprar (e.g. "1GB USA")
     * @param string $transactionId Tu referencia única para este cliente/orden
     * @param array $extraParams Parámetros adicionales (cantidad, etc.)
     */
    public function createOrder($productId, $transactionId, $extraParams = [])
    {
        $endpoint = "{$this->baseUrl}/order/api/v1/create_order";

        // Estructura del body para "Cualquier Plan/Cliente"
        // NOTA: Revisa la pag 17 de tu PDF para confirmar si requieren 'operation_type'
        $payload = array_merge([
            'product' =>[
                'id' => $productId
            ],       // ID del plan seleccionado
            'transaction_id' => $transactionId, // ID único de tu base de datos
            'count' => 1,                     // Por defecto 1 eSIM
            'operation_type' => 'NEW'      // Posiblemente requerido según pag 17
        ], $extraParams);

        $response = Http::withHeaders($this->getHeaders())
                        ->post($endpoint, $payload);

       

       

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