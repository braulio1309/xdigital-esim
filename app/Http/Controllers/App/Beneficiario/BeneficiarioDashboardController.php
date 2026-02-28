<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Http\Controllers\Controller;
use App\Models\App\Settings\BeneficiaryPlanMargin;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BeneficiarioDashboardController extends Controller
{
    /**
     * Plan capacities to display commissions for
     */
    const COMMISSION_PLANS = ['3', '5', '10'];

    /**
     * Free eSIM commission rate in USD
     */
    const FREE_ESIM_RATE = 0.85;

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

        // Get plan commissions for 3, 5, and 10 GB plans
        $plan_commissions = $this->getPlanCommissions($beneficiario->id);

        // Get activated free eSIM debt data (with optional date filter)
        $debt_data = $this->getDebtData($beneficiario->id, $startDate, $endDate);

        // Get dashboard data
        $data = [
            'beneficiario' => $beneficiario,
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => $beneficiario->total_earnings ?? 0,
            'total_sales' => $beneficiario->total_sales ?? 0,
            'free_esim_clients' => $free_esim_clients,
            'plan_commissions' => $plan_commissions,
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

        return response()->json([
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => number_format($beneficiario->total_earnings ?? 0, 2),
            'total_sales' => $beneficiario->total_sales ?? 0,
            'nombre' => $beneficiario->nombre,
            'descripcion' => $beneficiario->descripcion,
            'free_esim_clients' => $free_esim_clients,
            'plan_commissions' => $this->getPlanCommissions($beneficiario->id),
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
        $query = Transaction::where('purchase_amount', 0)
            ->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });

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

        $result = $query->selectRaw(
            'COUNT(*) as total_activated, ' .
            'SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) as total_unpaid, ' .
            'SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) as total_paid'
        )->first();

        $total_activated = (int) ($result->total_activated ?? 0);
        $total_unpaid    = (int) ($result->total_unpaid    ?? 0);
        $total_paid      = (int) ($result->total_paid      ?? 0);

        return [
            'total_activated'  => $total_activated,
            'total_unpaid'     => $total_unpaid,
            'total_paid'       => $total_paid,
            'total_debt'       => round($total_unpaid * self::FREE_ESIM_RATE, 2),
            'rate_per_esim'    => self::FREE_ESIM_RATE,
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

    /**
     * Get commission data for the specified plans
     *
     * @param int $beneficiarioId
     * @return array
     */
    private function getPlanCommissions(int $beneficiarioId): array
    {
        $margins = BeneficiaryPlanMargin::where('beneficiario_id', $beneficiarioId)
            ->whereIn('plan_capacity', self::COMMISSION_PLANS)
            ->where('is_active', true)
            ->get()
            ->keyBy('plan_capacity');

        $commissions = [];
        foreach (self::COMMISSION_PLANS as $capacity) {
            $commissions[$capacity] = isset($margins[$capacity])
                ? (float) $margins[$capacity]->margin_percentage
                : 0.0;
        }

        return $commissions;
    }
}
