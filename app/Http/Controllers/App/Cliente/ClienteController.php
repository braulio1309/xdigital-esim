<?php

namespace App\Http\Controllers\App\Cliente;

use App\Filters\App\Cliente\ClienteFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Cliente\Cliente;
use App\Services\App\Cliente\ClienteService;
// Importamos el servicio de eSIM
use App\Services\EsimFxService; 
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    protected $esimService;

    /**
     * ClienteController constructor.
     * @param ClienteService $service
     * @param ClienteFilter $filter
     * @param EsimFxService $esimService  <-- Inyección de dependencia
     */
    public function __construct(ClienteService $service, ClienteFilter $filter, EsimFxService $esimService)
    {
        $this->service = $service;
        $this->filter = $filter;
        $this->esimService = $esimService; // Asignación a la propiedad
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Guardamos el cliente en la BD local primero
        $cliente = $this->service->save();

        // 2. Integramos el servicio de eSIM
        // Verificamos si en el request viene el ID del producto (Plan)
        if ($request->has('product_id') || true) {
            try {
                $productId = '04d964be-3747-4b02-90b2-afd2cc4dbf44' ;
                
                // Usamos el ID del cliente recién creado para generar un Transaction ID único
                $transactionId = 'CLI-' . $cliente->id . '-' . time();

                // Llamamos al servicio 'createOrder' que definiste
                // Pasamos 'operation_type' => 'NEW' explícitamente en extraParams
                $esimData = $this->esimService->createOrder($productId, $transactionId, [
                    'operation_type' => 'NEW'
                ]);

                // 3. Actualizamos el cliente con la respuesta de la API
                // La API devuelve normalmente la estructura ['esim' => ['iccid' => '...', 'esim_qr' => '...']]
                if (isset($esimData['esim'])) {
                    // Asumiendo que tu modelo Cliente tiene campos para guardar esto. 
                    // Si no, deberías guardarlo en una tabla relacionada 'esims'.
                    $cliente->iccid = $esimData['esim']['iccid'] ?? null;
                    $cliente->esim_qr = $esimData['esim']['esim_qr'] ?? null; // El string del código QR
                    
                    // Guardamos los cambios en el modelo cliente
                    $cliente->save();
                }

            } catch (\Exception $e) {
                // Si falla la API, logueamos el error pero no fallamos el request HTTP 
                // para asegurar que el cliente se haya creado al menos.
                Log::error("Error activando eSIM para cliente ID {$cliente->id}: " . $e->getMessage());
                
                // Opcional: Podrías retornar un error 500 si la eSIM es estrictamente obligatoria
                // throw $e; 
            }
        }

        return created_responses('cliente');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        $cliente = $this->service->update($cliente);

        return updated_responses('cliente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Cliente $cliente)
    {
        if ($this->service->delete($cliente)) {
            return deleted_responses('cliente');
        }
        return failed_responses();
    }
}