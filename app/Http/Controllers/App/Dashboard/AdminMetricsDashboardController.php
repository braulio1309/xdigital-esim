<?php

namespace App\Http\Controllers\App\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\App\Dashboard\AdminMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminMetricsDashboardController extends Controller
{
    protected $service;

    public function __construct(AdminMetricsService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the admin metrics dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.admin-metrics');
    }

    /**
     * Get metrics data with optional date filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics(Request $request)
    {
        // Validate date parameters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Default to last 30 days if not provided
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $metrics = $this->service->getMetricsData($startDate, $endDate);

        return response()->json($metrics);
    }
}
