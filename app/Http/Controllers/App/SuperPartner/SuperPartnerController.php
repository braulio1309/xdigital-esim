<?php

namespace App\Http\Controllers\App\SuperPartner;

use App\Filters\App\SuperPartner\SuperPartnerFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\SuperPartnerRequest as Request;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use App\Services\App\Settings\SuperPartnerPlanMarginService;
use App\Services\App\Settings\SuperPartnerPriceService;
use App\Services\App\SuperPartner\SuperPartnerService;

class SuperPartnerController extends Controller
{
    public function __construct(SuperPartnerService $service, SuperPartnerFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    /**
     * Display a listing of super partners.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    /**
     * Store a newly created super partner.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->service->save();

        return created_responses('super_partner');
    }

    /**
     * Display the specified super partner.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->find($id)->load('user:id,email,last_name');
    }

    /**
     * Update the specified super partner.
     *
     * @param Request $request
     * @param SuperPartner $super_partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SuperPartner $super_partner)
    {
        $this->service->update($super_partner);

        return updated_responses('super_partner');
    }

    /**
     * Remove the specified super partner.
     *
     * @param SuperPartner $super_partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(SuperPartner $super_partner)
    {
        if ($this->service->delete($super_partner)) {
            return deleted_responses('super_partner');
        }
        return failed_responses();
    }

    /**
     * Get commission configuration for a super partner (general commission and free eSIM rate).
     *
     * @param  SuperPartner $super_partner
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommissions(SuperPartner $super_partner, SuperPartnerPlanMarginService $marginService, SuperPartnerPriceService $priceService)
    {
        return response()->json([
            'commission_percentage' => (float) ($super_partner->commission_percentage ?? 0),
            'free_esim_rate' => (float) $super_partner->free_esim_rate,
            'margins' => $marginService->getFormattedMargins($super_partner->id),
            'plan_prices' => $priceService->getFormattedPlanPrices($super_partner->id),
            'country_prices' => $priceService->getCountryPrices($super_partner->id),
        ]);
    }

    /**
     * Update commission configuration for a super partner.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  SuperPartner $super_partner
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCommissions(\Illuminate\Http\Request $request, SuperPartner $super_partner, SuperPartnerPlanMarginService $marginService, SuperPartnerPriceService $priceService)
    {
        $validated = $request->validate([
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'free_esim_rate' => 'nullable|numeric|min:0|max:999.99',
            'margins' => 'sometimes|array',
            'margins.*.margin_percentage' => 'required_with:margins|numeric|min:0|max:100',
            'margins.*.is_active' => 'sometimes|boolean',
            'plan_prices' => 'sometimes|array',
            'plan_prices.*.price' => 'nullable|numeric|min:0',
            'plan_prices.*.is_active' => 'sometimes|boolean',
            'country_prices' => 'sometimes|array',
            'country_prices.*.country_code' => 'required_with:country_prices|string|size:2',
            'country_prices.*.plan_capacity' => 'required_with:country_prices|string',
            'country_prices.*.percentage' => 'nullable|numeric|min:0|max:100',
            'country_prices.*.price' => 'nullable|numeric|min:0',
        ]);

        if (array_key_exists('commission_percentage', $validated)) {
            $super_partner->commission_percentage = $validated['commission_percentage'];
        }

        if (array_key_exists('free_esim_rate', $validated)) {
            $super_partner->free_esim_rate = $validated['free_esim_rate'];
        }

        $super_partner->save();

        if (array_key_exists('margins', $validated)) {
            $marginService->updateMargins($super_partner->id, $validated['margins']);
        }

        if (array_key_exists('plan_prices', $validated)) {
            $priceService->updatePlanPrices($super_partner->id, $validated['plan_prices']);
        }

        $priceService->updateCountryPrices($super_partner->id, $validated['country_prices'] ?? []);

        return response()->json([
            'message' => __('default.updated_response', ['name' => 'Comisiones de Super Partner']),
            'commission_percentage' => (float) ($super_partner->commission_percentage ?? 0),
            'free_esim_rate' => (float) $super_partner->free_esim_rate,
            'margins' => $marginService->getFormattedMargins($super_partner->id),
            'plan_prices' => $priceService->getFormattedPlanPrices($super_partner->id),
            'country_prices' => $priceService->getCountryPrices($super_partner->id),
        ]);
    }

    /**
     * Export commissions summary for a super partner.
     *
     * @param SuperPartner $super_partner
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCommissions(SuperPartner $super_partner, SuperPartnerPlanMarginService $marginService)
    {
        $partnerIds = $super_partner->beneficiarios()->pluck('id');

        $transactions = Transaction::with('beneficiario')
            ->whereIn('beneficiario_id', $partnerIds)
            ->get();

        $margins = $marginService->getFormattedMargins($super_partner->id);

        $rows = $transactions->map(function (Transaction $t) use ($super_partner, $margins) {
            $commission = 0.0;

            if ($t->isFreeEsim()) {
                $commission = (float) $super_partner->free_esim_rate;
            } else {
                $purchaseAmount = (float) $t->purchase_amount;
                $capacity = (string) $t->data_amount;

                if (isset($margins[$capacity]) && $margins[$capacity]['margin_percentage'] > 0) {
                    $commission = $purchaseAmount * ($margins[$capacity]['margin_percentage'] / 100);
                } elseif ($super_partner->commission_percentage) {
                    $commission = $purchaseAmount * ($super_partner->commission_percentage / 100);
                }
            }

            return [
                $t->transaction_id,
                $t->plan_name,
                $t->purchase_amount,
                $t->beneficiario ? $t->beneficiario->nombre : 'N/A',
                round($commission, 2),
                $t->created_at ? $t->created_at->format('Y-m-d') : '',
            ];
        })->toArray();

        $headings = ['Transaction ID', 'Plan', 'Monto', 'Partner', 'Comisión', 'Fecha'];

        $filename = 'comisiones-super-partner-' . \Str::slug($super_partner->nombre) . '.csv';
        $filepath = storage_path('app/' . $filename);

        $fp = fopen($filepath, 'w');
        fputcsv($fp, $headings);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }
}
