<?php

namespace App\Http\Controllers\App\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\ClienteRequest as Request;
use App\Services\App\Cliente\ClienteService;
use Illuminate\Http\Request as HttpRequest;

class RegistroEsimController extends Controller
{
    /**
     * Mostrar el formulario de registro de eSIM
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function mostrarFormulario(HttpRequest $request)
    {
        $parametro = $request->query('parametro', '');
        
        return view('clientes.registro-esim', [
            'parametro' => $parametro
        ]);
    }

    /**
     * Registrar un nuevo cliente desde el formulario público
     * 
     * @param Request $request
     * @param ClienteService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registrarCliente(Request $request, ClienteService $service)
    {
        $cliente = $service->save();

        // TODO: Llamar al servicio de eSIM cuando esté implementado
        // $esimService = app(EsimService::class);
        // $esimService->crearYActivarEsim($cliente->toArray());

        return redirect()
            ->back()
            ->with('success', 'Cliente registrado exitosamente');
    }
}
