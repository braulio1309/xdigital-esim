<?php

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\PlanMarginRequest;
use App\Services\App\Settings\PlanMarginService;
use Illuminate\Http\Request;

class PlanMarginController extends Controller
{
    protected $service;

    /**
     * PlanMarginController constructor.
     * 
     * @param PlanMarginService $service
     */
    public function __construct(PlanMarginService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all plan margins configuration
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'margins' => $this->service->getFormattedMargins(),
        ]);
    }

    /**
     * Update plan margins configuration
     * 
     * @param PlanMarginRequest $request
     * @return array
     */
    public function update(PlanMarginRequest $request)
    {
        $margins = $request->input('margins', []);
        
        $success = $this->service->updateMargins($margins);

        if ($success) {
            return updated_responses('plan_margins', [
                'margins' => $this->service->getFormattedMargins(),
            ]);
        }

        return failed_responses();
    }
}
