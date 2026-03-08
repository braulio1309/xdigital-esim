<?php

namespace App\Services\App\Dashboard;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use App\Services\App\AppService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AdminMetricsService extends AppService
{
    /**
     * Get metrics data for the admin dashboard
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getMetricsData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        return [
            'widgets' => $this->getMainWidgets($start, $end),
            'topBeneficiarios' => $this->getTopBeneficiarios($start, $end),
            'clientsTrend' => $this->getClientsTrend($start, $end),
            'transactionsByStatus' => $this->getTransactionsByStatus($start, $end),
            'beneficiariosActivity' => $this->getBeneficiariosActivity($start, $end),
        ];
    }

    /**
     * Helper: obtener IDs de beneficiarios del super partner autenticado (si aplica).
     *
     * @return array [superPartner|null, array partnerIds]
     */
    protected function getSuperPartnerAndPartnerIds(): array
    {
        if (!auth()->check() || auth()->user()->user_type !== 'super_partner') {
            return [null, []];
        }

        $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
        if (!$superPartner) {
            return [null, []];
        }

        $partnerIds = $superPartner->beneficiarios()->pluck('id')->toArray();
        return [$superPartner, $partnerIds];
    }

    /**
     * Get main metrics widgets
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function getMainWidgets($start, $end)
    {
        [$superPartner, $partnerIds] = $this->getSuperPartnerAndPartnerIds();

        // Base queries
        $clientesQuery = Cliente::whereBetween('created_at', [$start, $end]);
        $beneficiariosQuery = Beneficiario::whereBetween('created_at', [$start, $end]);
        $transactionsQuery = Transaction::whereBetween('created_at', [$start, $end]);
        $completedTransactionsQuery = Transaction::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        // Si es super_partner, limitar a su red de beneficiarios
        if ($superPartner && !empty($partnerIds)) {
            $clientesQuery->where(function ($q) use ($partnerIds) {
                $q->whereIn('beneficiario_id', $partnerIds)
                  ->orWhereHas('partners', function ($partnerQuery) use ($partnerIds) {
                      $partnerQuery->whereIn('beneficiario_id', $partnerIds);
                  });
            });

            $beneficiariosQuery->whereIn('id', $partnerIds);

            $transactionsQuery->whereIn('beneficiario_id', $partnerIds);
            $completedTransactionsQuery->whereIn('beneficiario_id', $partnerIds);
        }

        // Totales con el alcance aplicado
        $totalClientes = $clientesQuery->count();
        $totalBeneficiarios = $beneficiariosQuery->count();
        $totalTransactions = $transactionsQuery->count();

        // Total revenue from completed transactions (placeholder: $100 por transacción completada)
        $totalRevenue = $completedTransactionsQuery->count() * 100;

        return [
            [
                'label' => 'Total Clientes',
                'number' => $totalClientes,
                'icon' => 'users'
            ],
            [
                'label' => 'Total Beneficiarios',
                'number' => $totalBeneficiarios,
                'icon' => 'user-check'
            ],
            [
                'label' => 'Total Transacciones',
                'number' => $totalTransactions,
                'icon' => 'shopping-cart'
            ],
            [
                'label' => 'Ingresos Totales',
                'number' => '$' . number_format($totalRevenue, 0),
                'icon' => 'dollar-sign'
            ]
        ];
    }

    /**
     * Get top 5 beneficiarios with most clients
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function getTopBeneficiarios($start, $end)
    {
        [$superPartner, $partnerIds] = $this->getSuperPartnerAndPartnerIds();

        $topBeneficiariosQuery = Beneficiario::withCount(['clientes' => function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }])
            ->with(['clientes' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])
            ->having('clientes_count', '>', 0);

        if ($superPartner && !empty($partnerIds)) {
            $topBeneficiariosQuery->whereIn('id', $partnerIds);
        }

        $topBeneficiarios = $topBeneficiariosQuery
            ->orderByDesc('clientes_count')
            ->limit(5)
            ->get();

        $rows = $topBeneficiarios->map(function ($beneficiario) {
            // total_earnings is a database column from beneficiarios table
            $comisiones = $beneficiario->total_earnings ?? 0;
            
            return [
                'nombre' => $beneficiario->nombre,
                'codigo' => $beneficiario->codigo,
                'clientes_count' => $beneficiario->clientes_count,
                'comisiones' => '$' . number_format($comisiones, 2)
            ];
        })->toArray();

        return [
            'columns' => [
                ['key' => 'nombre', 'label' => 'Nombre'],
                ['key' => 'codigo', 'label' => 'Código'],
                ['key' => 'clientes_count', 'label' => 'Clientes'],
                ['key' => 'comisiones', 'label' => 'Comisiones']
            ],
            'rows' => $rows
        ];
    }

    /**
     * Get clients trend over time
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function getClientsTrend($start, $end)
    {
        $days = $start->diffInDays($end);
        $groupBy = 'date';
        $dateFormat = 'M j';

        if ($days > 90) {
            // Group by month for ranges > 90 days
            $groupBy = 'month';
            $dateFormat = 'M Y';
        } elseif ($days > 30) {
            // Group by week for ranges > 30 days
            $groupBy = 'week';
            $dateFormat = 'M j';
        }

        [$superPartner, $partnerIds] = $this->getSuperPartnerAndPartnerIds();

        $clientsQuery = Cliente::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end]);

        if ($superPartner && !empty($partnerIds)) {
            $clientsQuery->where(function ($q) use ($partnerIds) {
                $q->whereIn('beneficiario_id', $partnerIds)
                  ->orWhereHas('partners', function ($partnerQuery) use ($partnerIds) {
                      $partnerQuery->whereIn('beneficiario_id', $partnerIds);
                  });
            });
        }

        $clientsData = $clientsQuery
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        if ($groupBy === 'date') {
            // Daily grouping
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $count = $clientsData->where('date', $dateStr)->first()->count ?? 0;
                $labels[] = $date->format($dateFormat);
                $data[] = $count;
            }
        } else {
            // For simplicity, just use the raw data
            foreach ($clientsData as $item) {
                $labels[] = Carbon::parse($item->date)->format($dateFormat);
                $data[] = $item->count;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nuevos Clientes',
                    'data' => $data,
                    'borderColor' => '#4466F2',
                    'backgroundColor' => 'rgba(68, 102, 242, 0.1)',
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get transactions grouped by status
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function getTransactionsByStatus($start, $end)
    {
        [$superPartner, $partnerIds] = $this->getSuperPartnerAndPartnerIds();

        $transactionsQuery = Transaction::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end]);

        if ($superPartner && !empty($partnerIds)) {
            $transactionsQuery->whereIn('beneficiario_id', $partnerIds);
        }

        $transactions = $transactionsQuery
            ->groupBy('status')
            ->get();

        $statusColors = [
            'completed' => '#28a745',
            'pending' => '#ffc107',
            'failed' => '#dc3545',
            'cancelled' => '#6c757d',
        ];

        $labels = [];
        $data = [];
        $backgroundColor = [];

        foreach ($transactions as $transaction) {
            $status = $transaction->status;
            $labels[] = ucfirst($status);
            $data[] = $transaction->count;
            $backgroundColor[] = $statusColors[$status] ?? '#6c757d';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor
                ]
            ]
        ];
    }

    /**
     * Get beneficiarios activity (active vs inactive)
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function getBeneficiariosActivity($start, $end)
    {
        [$superPartner, $partnerIds] = $this->getSuperPartnerAndPartnerIds();

        $beneficiariosQuery = Beneficiario::whereBetween('created_at', [$start, $end])
            ->withCount('clientes');

        if ($superPartner && !empty($partnerIds)) {
            $beneficiariosQuery->whereIn('id', $partnerIds);
        }

        $allBeneficiarios = $beneficiariosQuery->get();

        $activos = $allBeneficiarios->where('clientes_count', '>', 0)->count();
        $inactivos = $allBeneficiarios->where('clientes_count', '=', 0)->count();

        return [
            'labels' => ['Activos', 'Inactivos'],
            'datasets' => [
                [
                    'label' => 'Beneficiarios',
                    'data' => [$activos, $inactivos],
                    'backgroundColor' => ['#4466F2', '#e0e0e0']
                ]
            ]
        ];
    }
}
