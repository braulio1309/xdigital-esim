<?php

namespace App\Http\Controllers\App\SuperPartner;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use Illuminate\Http\Request;

class SuperPartnerDashboardController extends Controller
{
    /**
     * Show the super partner dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'super_partner') {
            abort(403, 'Unauthorized access');
        }

        $superPartner = $user->superPartner;

        if (!$superPartner) {
            abort(404, 'Super Partner not found');
        }

        $data = $this->getDashboardData($superPartner, $request);

        return view('dashboard.super_partner', array_merge(['superPartner' => $superPartner], $data));
    }

    /**
     * Return super partner dashboard data as JSON.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'super_partner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $superPartner = $user->superPartner;

        if (!$superPartner) {
            return response()->json(['error' => 'Super Partner not found'], 404);
        }

        return response()->json($this->getDashboardData($superPartner, $request));
    }

    /**
     * Build the metrics array for the super partner dashboard.
     *
     * @param \App\Models\App\SuperPartner\SuperPartner $superPartner
     * @param Request $request
     * @return array
     */
    private function getDashboardData($superPartner, Request $request): array
    {
        $partnerIds = $superPartner->beneficiarios()->pluck('id');

        $totalPartners = $partnerIds->count();

        $totalClientes = Cliente::whereIn('beneficiario_id', $partnerIds)->count();

        $totalTransactions = Transaction::where(function ($builder) use ($partnerIds, $superPartner) {
            $builder->whereIn('beneficiario_id', $partnerIds)
                ->orWhere('super_partner_id', $superPartner->id);
        })->count();

        $unpaidTransactions = Transaction::with(['beneficiario', 'cliente.beneficiario', 'superPartner'])
            ->where('is_paid', false)
            ->where(function ($builder) use ($partnerIds, $superPartner) {
                $builder->whereIn('beneficiario_id', $partnerIds)
                    ->orWhere('super_partner_id', $superPartner->id);
            })
            ->get();

        $totalFreeEsims = Transaction::where('purchase_amount', 0)
            ->where(function ($builder) use ($partnerIds, $superPartner) {
                $builder->whereIn('beneficiario_id', $partnerIds)
                    ->orWhere('super_partner_id', $superPartner->id);
            })
            ->count();

        $totalDebt = round($unpaidTransactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        }), 2);

        $totalUnpaidTransactions = $unpaidTransactions->count();

        return [
            'nombre'             => $superPartner->nombre,
            'total_partners'     => $totalPartners,
            'total_clientes'     => $totalClientes,
            'total_transactions' => $totalTransactions,
            'total_free_esims'   => $totalFreeEsims,
            'total_unpaid_transactions' => $totalUnpaidTransactions,
            'total_debt'         => $totalDebt,
        ];
    }
}
