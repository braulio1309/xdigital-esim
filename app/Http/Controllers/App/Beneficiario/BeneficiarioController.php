<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Filters\App\Beneficiario\BeneficiarioFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\BeneficiarioRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Services\App\Beneficiario\BeneficiarioService;

class BeneficiarioController extends Controller
{
    /**
     * BeneficiarioController constructor.
     * @param BeneficiarioService $service
     * @param BeneficiarioFilter $filter
     */
    public function __construct(BeneficiarioService $service, BeneficiarioFilter $filter)
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
        $beneficiario = $this->service->save();

        return created_responses('beneficiario');
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
    public function update(Request $request, Beneficiario $beneficiario)
    {
        $beneficiario = $this->service->update($beneficiario);

        return updated_responses('beneficiario');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Beneficiario $beneficiario)
    {
        if ($this->service->delete($beneficiario)) {
            return deleted_responses('beneficiario');
        }
        return failed_responses();
    }
}
