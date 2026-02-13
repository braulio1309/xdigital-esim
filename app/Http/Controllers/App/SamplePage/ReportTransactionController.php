<?php

namespace App\Http\Controllers\App\SamplePage;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportTransactionController extends Controller
{
    /**
     * Get overview data for transactions
     */
    public function overview(Request $request)
    {
        $beneficiarioId = $request->get('beneficiario_id');

        $query = Transaction::with('cliente.beneficiario');

        if ($beneficiarioId) {
            $query->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });
        }

        // Total transactions this week
        $totalTransactionsThisWeek = (clone $query)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Total revenue all time
        $totalRevenue = (clone $query)->sum('purchase_amount');

        // Free eSIMs activated (transactions with purchase_amount = 0)
        $freeEsims = (clone $query)->where('purchase_amount', 0)->count();

        // Active plans (completed transactions)
        $activePlans = (clone $query)->where('status', 'completed')->count();

        // Transaction trends by week (last 8 weeks)
        $weeks = [];
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();
            
            $weekData = (clone $query)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->selectRaw('COUNT(*) as total, SUM(purchase_amount) as revenue, SUM(CASE WHEN purchase_amount = 0 THEN 1 ELSE 0 END) as free_esims')
                ->first();

            $weeks[] = [
                'start' => $startOfWeek->toISOString(),
                'end' => $endOfWeek->toISOString(),
                'total' => $weekData->total ?? 0,
                'revenue' => round($weekData->revenue ?? 0, 2),
                'free_esims' => $weekData->free_esims ?? 0,
            ];
        }

        // Transaction sources (by beneficiario)
        $transactionSources = Transaction::with('cliente.beneficiario')
            ->when($beneficiarioId, function ($q) use ($beneficiarioId) {
                $q->whereHas('cliente', function ($query) use ($beneficiarioId) {
                    $query->where('beneficiario_id', $beneficiarioId);
                });
            })
            ->get()
            ->groupBy('cliente.beneficiario.nombre')
            ->map(function ($transactions, $beneficiarioName) {
                return [
                    'name' => $beneficiarioName ?: 'Sin Beneficiario',
                    'total_transactions' => $transactions->count(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'total_transactions_this_week' => $totalTransactionsThisWeek,
            'total_revenue' => round($totalRevenue, 2),
            'free_esims' => $freeEsims,
            'active_plans' => $activePlans,
            'transaction_trends' => $weeks,
            'transaction_sources' => $transactionSources,
        ]);
    }

    /**
     * Get basic report data (transactions by plan)
     */
    public function basicReport(Request $request)
    {
        $beneficiarioId = $request->get('beneficiario_id');

        $query = Transaction::with('cliente.beneficiario');

        if ($beneficiarioId) {
            $query->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });
        }

        $reportData = $query
            ->selectRaw('plan_name as name, COUNT(*) as count, SUM(purchase_amount) as value')
            ->groupBy('plan_name')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name ?: 'Sin Plan',
                    'count' => $item->count,
                    'value' => round($item->value, 2),
                ];
            });

        return response()->json([
            'data' => $reportData,
        ]);
    }

    /**
     * Get beneficiary performance overview
     */
    public function beneficiaryOverview(Request $request)
    {
        $beneficiarioId = $request->get('beneficiario_id');

        $query = Beneficiario::with(['clientes.transactions']);

        if ($beneficiarioId) {
            $query->where('id', $beneficiarioId);
        }

        $beneficiarios = $query->get();

        // Total beneficiarios
        $totalBeneficiarios = $beneficiarios->count();

        // Active beneficiarios (have transactions in last 30 days)
        $activeBeneficiarios = $beneficiarios->filter(function ($beneficiario) {
            return $beneficiario->clientes->flatMap->transactions
                ->where('created_at', '>=', now()->subDays(30))
                ->count() > 0;
        })->count();

        // Average transactions per beneficiario
        $avgTransactionsPerBeneficiario = $totalBeneficiarios > 0 
            ? round($beneficiarios->sum(function ($b) {
                return $b->clientes->flatMap->transactions->count();
            }) / $totalBeneficiarios, 1)
            : 0;

        // Transactions by beneficiario (top beneficiarios by transactions)
        $transactionsByBeneficiario = $beneficiarios->map(function ($beneficiario) {
            $transactions = $beneficiario->clientes->flatMap->transactions;
            return [
                'name' => $beneficiario->nombre,
                'value' => $transactions->count(),
            ];
        })->sortByDesc('value')->values()->toArray();

        // Sales by plan for each beneficiario
        $salesByPlan = $beneficiarios->flatMap(function ($beneficiario) {
            return $beneficiario->clientes->flatMap->transactions
                ->groupBy('plan_name')
                ->map(function ($transactions, $planName) {
                    return [
                        'plan' => $planName ?: 'Sin Plan',
                        'count' => $transactions->count(),
                    ];
                });
        })->groupBy('plan')->map(function ($plans, $planName) {
            return [
                'month' => $planName,
                'active_jobs' => $plans->sum('count'),
            ];
        })->values()->toArray();

        return response()->json([
            'total_beneficiarios' => $totalBeneficiarios,
            'active_beneficiarios' => $activeBeneficiarios,
            'avg_transactions_per_beneficiario' => $avgTransactionsPerBeneficiario,
            'transactions_by_beneficiario' => $transactionsByBeneficiario,
            'sales_by_plan' => $salesByPlan,
        ]);
    }

    /**
     * Get list of beneficiarios for the filter
     */
    public function beneficiarios()
    {
        $beneficiarios = Beneficiario::select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($beneficiario) {
                return [
                    'id' => $beneficiario->id,
                    'value' => $beneficiario->nombre,
                ];
            });

        return response()->json($beneficiarios);
    }
}
