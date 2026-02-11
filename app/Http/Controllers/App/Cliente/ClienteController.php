<?php

namespace App\Http\Controllers\App\Cliente;

use App\Filters\App\Cliente\ClienteFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Cliente\Cliente;
use App\Services\App\Cliente\ClienteService;

class ClienteController extends Controller
{
    /**
     * ClienteController constructor.
     * @param ClienteService $service
     * @param ClienteFilter $filter
     */
    public function __construct(ClienteService $service, ClienteFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * Display a listing of clientes.
     * 
     * If authenticated user is a beneficiario, only show their clients.
     * 
     * @return mixed
     */
    public function index()
    {
        $query = $this->service->filters($this->filter)->latest();
        
        // Filter by beneficiario_id if user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            
            if ($beneficiario) {
                $query = $query->where('beneficiario_id', $beneficiario->id);
            }
        }
        
        return $query->with('beneficiario:id,nombre')->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * When creating a client manually from admin panel:
     * - Do NOT activate eSIM automatically
     * - Do NOT create orders or subscriptions
     * - Just create the client record
     * - The can_activate_free_esim flag controls access to free eSIM
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Simply save the client without automatic eSIM activation
        // The flag can_activate_free_esim controls whether they can activate it later
        //Valida si es un beneficiario que lo esta creando, si es asi asigna el beneficiario_id del cliente al beneficiario del usuario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $request->merge(['beneficiario_id' => $beneficiario->id]);
            }
        }
        $cliente = $this->service->save();

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

    /**
     * Toggle the can_activate_free_esim flag for a client.
     * 
     * @param Cliente $cliente
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFreeEsim(Cliente $cliente)
    {
        $cliente->can_activate_free_esim = !$cliente->can_activate_free_esim;
        $cliente->save();
        
        $status = $cliente->can_activate_free_esim ? 'activado' : 'desactivado';
        
        return response()->json([
            'status' => true,
            'message' => "Permiso de eSIM gratuita {$status} exitosamente.",
            'data' => $cliente
        ]);
    }
}