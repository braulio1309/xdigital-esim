<?php

namespace App\Http\Controllers\App\Cliente;

use App\Filters\App\Cliente\ClienteFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Models\App\Cliente\Cliente;
use App\Services\App\Cliente\ClienteService;
use App\Services\App\Cliente\EsimService;

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
        $cliente = $this->service->save();

        // TODO: Llamar al servicio de eSIM cuando esté implementado
        // $esimService = app(EsimService::class);
        // $esimService->crearYActivarEsim($cliente->toArray());

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
