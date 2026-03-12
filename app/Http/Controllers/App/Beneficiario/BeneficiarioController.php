<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Exports\App\Beneficiario\BeneficiarioCommissionsExport;
use App\Filters\App\Beneficiario\BeneficiarioFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\BeneficiarioRequest as Request;
use App\Models\App\Beneficiario\Beneficiario;
use App\Services\App\Beneficiario\BeneficiarioService;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\EsimFxService;
use Maatwebsite\Excel\Facades\Excel;

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
        $query = $this->service->filters($this->filter)->latest();

        // Filter by super_partner_id if user is a super_partner
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $query = $query->where('super_partner_id', $superPartner->id);
            }
        }

        $beneficiarios = $query->paginate(request()->get('per_page', 10));
        
        // Add unpaid transactions count and total owed for each beneficiary
        $beneficiarios->getCollection()->transform(function ($beneficiario) {
            $unpaidCount = \App\Models\App\Transaction\Transaction::where('purchase_amount', 0)
                ->where('is_paid', false)
                ->where('beneficiario_id', $beneficiario->id)
                ->count();
            
            $beneficiario->unpaid_transactions_count = $unpaidCount;
            $beneficiario->total_owed = round($unpaidCount * (float) $beneficiario->free_esim_rate, 2);
            
            return $beneficiario;
        });
        
        return $beneficiarios;
    }

    /**
     * Download Excel file with commissions for a specific beneficiary.
     *
     * @param Beneficiario $beneficiario
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCommissions(
        Beneficiario $beneficiario,
        EsimFxService $esimService,
        PlanMarginService $planMarginService,
        BeneficiaryPlanMarginService $beneficiaryPlanMarginService
    ) {
        $export = new BeneficiarioCommissionsExport(
            $beneficiario->id,
            $beneficiario->nombre,
            $esimService,
            $planMarginService,
            $beneficiaryPlanMarginService
        );

        $filename = 'comisiones-' . \Str::slug($beneficiario->nombre) . '.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // If a super_partner is creating a beneficiario, set super_partner_id
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $request->merge(['super_partner_id' => $superPartner->id]);
            }
        }

        $beneficiario = $this->service->save($request->all());

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
        return $this->service->find($id)->load('user:id,email,last_name');
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
