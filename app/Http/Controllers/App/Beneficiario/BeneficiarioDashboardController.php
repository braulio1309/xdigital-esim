<?php

namespace App\Http\Controllers\App\Beneficiario;

use App\Http\Controllers\Controller;
use App\Models\App\Settings\BeneficiaryPlanMargin;
use Illuminate\Http\Request;

class BeneficiarioDashboardController extends Controller
{
    /**
     * Plan capacities to display commissions for
     */
    const COMMISSION_PLANS = ['3', '5', '10'];

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

        // Get clients with pending free eSIM activation
        $free_esim_clients = $this->getFreeEsimClients($beneficiario);

        // Get plan commissions for 3, 5, and 10 GB plans
        $plan_commissions = $this->getPlanCommissions($beneficiario->id);

        // Get dashboard data
        $data = [
            'beneficiario' => $beneficiario,
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => $beneficiario->total_earnings ?? 0,
            'total_sales' => $beneficiario->total_sales ?? 0,
            'free_esim_clients' => $free_esim_clients,
            'plan_commissions' => $plan_commissions,
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

        $free_esim_clients = $this->getFreeEsimClients($beneficiario);

        return response()->json([
            'commission_percentage' => $beneficiario->commission_percentage ?? 0,
            'total_earnings' => number_format($beneficiario->total_earnings ?? 0, 2),
            'total_sales' => $beneficiario->total_sales ?? 0,
            'nombre' => $beneficiario->nombre,
            'descripcion' => $beneficiario->descripcion,
            'free_esim_clients' => $free_esim_clients,
            'plan_commissions' => $this->getPlanCommissions($beneficiario->id),
        ]);
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
