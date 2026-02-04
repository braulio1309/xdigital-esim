<?php

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\PlanMarginRequest;
use App\Services\App\Settings\PlanMarginService;
use Illuminate\Http\JsonResponse;

class PlanMarginController extends Controller
{
    protected $service;

    public function __construct(PlanMarginService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of plan margins.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->check() || !auth()->user()->isAppAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access.'
            ], 403);
        }

        return response()->json([
            'data' => $this->service->getFormattedMargins()
        ]);
    }

    /**
     * Update plan margins.
     *
     * @param PlanMarginRequest $request
     * @return JsonResponse
     */
    public function update(PlanMarginRequest $request)
    {
        $margins = $request->input('margins');
        
        // Transform array to have plan_capacity as key
        $transformedMargins = [];
        foreach ($margins as $margin) {
            $transformedMargins[$margin['plan_capacity']] = [
                'margin_percentage' => $margin['margin_percentage'],
                'is_active' => $margin['is_active'] ?? true,
            ];
        }

        $result = $this->service->updateMargins($transformedMargins);

        if ($result) {
            return response()->json([
                'message' => 'Plan margins updated successfully.',
                'data' => $this->service->getFormattedMargins()
            ]);
        }

        return response()->json([
            'message' => 'Failed to update plan margins.'
        ], 500);
    }
}
