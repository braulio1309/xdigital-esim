<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Http\Controllers\Controller;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BeneficiarioDashboardController extends Controller
{
    /**
     * Show the beneficiario dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is a beneficiario
        if ($user->user_type !== 'beneficiario') {
            abort(403, 'Unauthorized access');
        }
        
        $beneficiario = $user->beneficiario;
        
        if (!$beneficiario) {
            abort(404, 'Beneficiario not found');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get clients with pending free eSIM activation
        $free_esim_clients = $this->getFreeEsimClients($beneficiario);
        $transactions = $this->applyDateFilters(
            $this->beneficiarioTransactionsQuery($beneficiario->id),
            $startDate,
            $endDate
        )->get();

        $sale_commissions = $this->getSaleCommissions($beneficiario);

        // Get activated free eSIM debt data (with optional date filter)
        $debt_data = $this->getDebtData($beneficiario->id, $startDate, $endDate);

        // Get dashboard data
        $data = [
            'beneficiario' => $beneficiario,
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => $this->calculateTotalEarnings($transactions),
            'total_sales' => $transactions->count(),
            'free_esim_clients' => $free_esim_clients,
            'sale_commissions' => $sale_commissions,
            'debt_data' => $debt_data,
            'filter_start_date' => $startDate,
            'filter_end_date' => $endDate,
        ];
        
        return view('dashboard.beneficiario', $data);
    }

    /**
     * Get beneficiario dashboard data as JSON (for API/AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $user = $request->user();
        
        if ($user->user_type !== 'beneficiario') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $beneficiario = $user->beneficiario;
        
        if (!$beneficiario) {
            return response()->json(['error' => 'Beneficiario not found'], 404);
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $free_esim_clients = $this->getFreeEsimClients($beneficiario);
        $debt_data = $this->getDebtData($beneficiario->id, $startDate, $endDate);
        $transactions = $this->applyDateFilters(
            $this->beneficiarioTransactionsQuery($beneficiario->id),
            $startDate,
            $endDate
        )->get();

        return response()->json([
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => number_format($this->calculateTotalEarnings($transactions), 2),
            'total_sales' => $transactions->count(),
            'nombre' => $beneficiario->nombre,
            'descripcion' => $beneficiario->descripcion,
            'free_esim_clients' => $free_esim_clients,
            'sale_commissions' => $this->getSaleCommissions($beneficiario),
            'debt_data' => $debt_data,
        ]);
    }

    /**
     * Get activated free eSIM debt data for a beneficiario, optionally filtered by date.
     *
     * @param int $beneficiarioId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    private function getDebtData(int $beneficiarioId, ?string $startDate, ?string $endDate): array
    {
        $query = $this->applyDateFilters(
            Transaction::where('purchase_amount', 0)
                ->where('beneficiario_id', $beneficiarioId),
            $startDate,
            $endDate
        );

        $unpaidTransactions = (clone $query)
            ->where(function ($builder) {
                $builder->where('is_paid', 0)
                    ->orWhereNull('is_paid');
            })
            ->get();

        $total_activated = (clone $query)->count();
        $total_unpaid = $unpaidTransactions->count();
        $total_paid = (clone $query)
            ->where('is_paid', 1)
            ->count();

        $totalDebt = $unpaidTransactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        });

        return [
            'total_activated'  => $total_activated,
            'total_unpaid'     => $total_unpaid,
            'total_paid'       => $total_paid,
            'total_debt'       => round($totalDebt, 2),
        ];
    }

    /**
     * Get clients with pending free eSIM activation for a beneficiario
     *
     * @param \App\Models\App\Beneficiario\Beneficiario $beneficiario
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFreeEsimClients($beneficiario)
    {
        return $beneficiario->clientes()
            ->where('can_activate_free_esim', true)
            ->get(['id', 'nombre', 'apellido', 'email']);
    }

    private function beneficiarioTransactionsQuery(int $beneficiarioId): Builder
    {
        return Transaction::with(['beneficiario', 'cliente.beneficiario', 'superPartner'])
            ->where(function (Builder $builder) use ($beneficiarioId) {
                $builder->where('beneficiario_id', $beneficiarioId)
                    ->orWhere(function (Builder $fallbackBuilder) use ($beneficiarioId) {
                        $fallbackBuilder->whereNull('beneficiario_id')
                            ->whereHas('cliente', function (Builder $clienteBuilder) use ($beneficiarioId) {
                                $clienteBuilder->where('beneficiario_id', $beneficiarioId);
                            });
                    });
            });
    }

    private function applyDateFilters(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            try {
                $query->where('creation_time', '>=', Carbon::parse($startDate)->startOfDay());
            } catch (\Exception $e) {
                // Ignore invalid date; no filter applied
            }
        }

        if ($endDate) {
            try {
                $query->where('creation_time', '<=', Carbon::parse($endDate)->endOfDay());
            } catch (\Exception $e) {
                // Ignore invalid date; no filter applied
            }
        }

        return $query;
    }

    private function calculateTotalEarnings($transactions): float
    {
        return round($transactions->sum(function (Transaction $transaction) {
            return (float) ($transaction->partner_sale_commission_amount ?? 0);
        }), 2);
    }

    private function getSaleCommissions($beneficiario): array
    {
        return [
            'usa_ca_eu' => $beneficiario->sale_commission_usa_ca_eu_pct !== null
                ? (float) $beneficiario->sale_commission_usa_ca_eu_pct
                : 0.0,
            'latam' => $beneficiario->sale_commission_latam_pct !== null
                ? (float) $beneficiario->sale_commission_latam_pct
                : 0.0,
        ];
    }
}
