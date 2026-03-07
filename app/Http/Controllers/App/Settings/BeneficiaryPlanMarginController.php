<?php

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\BeneficiaryPlanMarginRequest;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use Illuminate\Http\Request;

class BeneficiaryPlanMarginController extends Controller
{
    protected $service;

    /**
     * BeneficiaryPlanMarginController constructor.
     * 
     * @param BeneficiaryPlanMarginService $service
     */
    public function __construct(BeneficiaryPlanMarginService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all plan margins configuration for a beneficiary
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Super partners cannot manage plan margins/rates
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            return response()->json(['message' => 'Unauthorized. Super partners cannot manage plan margins.'], 403);
        }

        $request->validate([
            'beneficiario_id' => 'required|integer|exists:beneficiarios,id',
        ]);

        $beneficiarioId = $request->input('beneficiario_id');

        return response()->json([
            'margins' => $this->service->getFormattedMargins($beneficiarioId),
        ]);
    }

    /**
     * Update plan margins configuration for a beneficiary
     * 
     * @param BeneficiaryPlanMarginRequest $request
     * @return array
     */
    public function update(BeneficiaryPlanMarginRequest $request)
    {
        // Super partners cannot manage plan margins/rates
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            return response()->json(['message' => 'Unauthorized. Super partners cannot manage plan margins.'], 403);
        }

        $beneficiarioId = $request->input('beneficiario_id');
        $margins = $request->input('margins', []);
        
        $success = $this->service->updateMargins($beneficiarioId, $margins);

        if ($success) {
            return updated_responses('beneficiary_plan_margins', [
                'margins' => $this->service->getFormattedMargins($beneficiarioId),
            ]);
        }

        return failed_responses();
    }
}
