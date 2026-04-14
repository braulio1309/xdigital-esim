<?php

namespace App\Http\Controllers\App\SuperPartner;

use App\Http\Controllers\Controller;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\Transaction;
use Illuminate\Database\Eloquent\Builder;
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
        $partners = $superPartner->beneficiarios()
            ->orderBy('nombre')
            ->get();

        $partnerIds = $partners->pluck('id');

        $totalPartners = $partners->count();

        $transactionsQuery = $this->scopeTransactionsForSuperPartner(
            Transaction::query(),
            $partnerIds->all(),
            $superPartner->id
        );

        $totalTransactions = (clone $transactionsQuery)->count();

        $totalClientes = (clone $transactionsQuery)
            ->whereNotNull('cliente_id')
            ->distinct()
            ->count('cliente_id');

        $unpaidTransactions = $this->scopeTransactionsForSuperPartner(
            Transaction::with(['beneficiario', 'cliente.beneficiario', 'superPartner'])->where('is_paid', false),
            $partnerIds->all(),
            $superPartner->id
        )
            ->get();

        $totalFreeEsims = $this->scopeTransactionsForSuperPartner(
            Transaction::where('purchase_amount', 0),
            $partnerIds->all(),
            $superPartner->id
        )
            ->count();

        $totalDebt = round($unpaidTransactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        }), 2);

        $totalUnpaidTransactions = $unpaidTransactions->count();

        $partnerTransactionSummary = Transaction::query()
            ->selectRaw('beneficiario_id, COUNT(*) as total_transactions, COUNT(DISTINCT cliente_id) as total_clientes')
            ->whereIn('beneficiario_id', $partnerIds)
            ->groupBy('beneficiario_id')
            ->get()
            ->keyBy('beneficiario_id');

        $partnerRows = $partners->map(function (Beneficiario $partner) use ($partnerTransactionSummary) {
            $summary = $partnerTransactionSummary->get($partner->id);

            return [
                'id' => $partner->id,
                'nombre' => $partner->nombre,
                'clientes' => (int) ($summary->total_clientes ?? 0),
                'transactions' => (int) ($summary->total_transactions ?? 0),
                'codigo' => $partner->codigo,
            ];
        })->values();

        return [
            'nombre'             => $superPartner->nombre,
            'total_partners'     => $totalPartners,
            'total_clients_with_transactions' => $totalClientes,
            'total_transactions' => $totalTransactions,
            'total_free_esims'   => $totalFreeEsims,
            'total_unpaid_transactions' => $totalUnpaidTransactions,
            'total_debt'         => $totalDebt,
            'related_partners'   => $partnerRows,
        ];
    }

    private function scopeTransactionsForSuperPartner(Builder $query, array $partnerIds, int $superPartnerId, bool $includeDirectSuperPartnerTransactions = true): Builder
    {
        return $query->where(function (Builder $builder) use ($partnerIds, $superPartnerId, $includeDirectSuperPartnerTransactions) {
            if (!empty($partnerIds)) {
                $builder->whereIn('beneficiario_id', $partnerIds);

                if ($includeDirectSuperPartnerTransactions) {
                    $builder->orWhere('super_partner_id', $superPartnerId);
                }

                return;
            }

            if ($includeDirectSuperPartnerTransactions) {
                $builder->where('super_partner_id', $superPartnerId);
            } else {
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
