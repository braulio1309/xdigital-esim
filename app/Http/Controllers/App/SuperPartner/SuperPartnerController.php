<?php

namespace App\Http\Controllers\App\SuperPartner;

use App\Helpers\CountryTariffHelper;
use App\Filters\App\SuperPartner\SuperPartnerFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\SuperPartnerRequest as Request;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Status;
use App\Services\App\Settings\SuperPartnerPlanMarginService;
use App\Services\App\Settings\SuperPartnerPriceService;
use App\Services\App\SuperPartner\SuperPartnerService;
use Illuminate\Support\Str;

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
            ->with('user.status:id,name,class')
            ->filters($this->filter)
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }

    public function inactivate(SuperPartner $super_partner)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para inactivar este super partner.',
            ], 403);
        }

        return $this->updateSuperPartnerStatus($super_partner, 'status_inactive', 'inactivo', 'inactivado');
    }

    public function activate(SuperPartner $super_partner)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado para activar este super partner.',
            ], 403);
        }

        return $this->updateSuperPartnerStatus($super_partner, 'status_active', 'activo', 'activado');
    }

    private function updateSuperPartnerStatus(SuperPartner $super_partner, string $statusName, string $statusLabel, string $actionLabel)
    {

        $super_partner->loadMissing('user.status');

        if (!$super_partner->user) {
            return response()->json([
                'status' => false,
                'message' => 'El super partner no tiene un usuario asociado.',
            ], 422);
        }

        if (optional($super_partner->user->status)->name === $statusName) {
            return response()->json([
                'status' => false,
                'message' => "El super partner ya está {$statusLabel}.",
            ], 422);
        }

        $status = Status::findByNameAndType($statusName, 'user');

        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => "No se encontró el estado {$statusLabel}.",
            ], 422);
        }

        $super_partner->user->markAs($status);

        return response()->json([
            'status' => true,
            'message' => "Super partner {$actionLabel} exitosamente.",
        ]);
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
            'commission_percentage'       => (float) ($super_partner->commission_percentage ?? 0),
            'free_esim_rate'              => (float) $super_partner->free_esim_rate,
            'sale_commission_latam_pct'   => $super_partner->sale_commission_latam_pct !== null ? (float) $super_partner->sale_commission_latam_pct : null,
            'sale_commission_usa_ca_eu_pct' => $super_partner->sale_commission_usa_ca_eu_pct !== null ? (float) $super_partner->sale_commission_usa_ca_eu_pct : null,
            'margins'                     => $marginService->getFormattedMargins($super_partner->id),
            'plan_prices'                 => $priceService->getFormattedPlanPrices($super_partner->id),
            'country_prices'              => $priceService->getCountryPrices($super_partner->id),
            'countries'                   => CountryTariffHelper::getAllCountries(),
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
            'commission_percentage'         => 'nullable|numeric|min:0|max:100',
            'free_esim_rate'                => 'nullable|numeric|min:0|max:999.99',
            'sale_commission_latam_pct'     => 'nullable|numeric|min:0|max:100',
            'sale_commission_usa_ca_eu_pct' => 'nullable|numeric|min:0|max:100',
            'margins'                       => 'sometimes|array',
            'margins.*.margin_percentage'   => 'required_with:margins|numeric|min:0|max:100',
            'margins.*.is_active'           => 'sometimes|boolean',
            'plan_prices'                   => 'sometimes|array',
            'plan_prices.*.price'           => 'nullable|numeric|min:0',
            'plan_prices.*.is_active'       => 'sometimes|boolean',
            'country_prices'                => 'sometimes|array',
            'country_prices.*.country_code' => 'required_with:country_prices|string|size:2',
            'country_prices.*.plan_capacity' => 'required_with:country_prices|string',
            'country_prices.*.price'        => 'required_with:country_prices|numeric|min:0',
        ]);

        if (array_key_exists('commission_percentage', $validated)) {
            $super_partner->commission_percentage = $validated['commission_percentage'];
        }

        if (array_key_exists('free_esim_rate', $validated)) {
            $super_partner->free_esim_rate = $validated['free_esim_rate'];
        }

        if (array_key_exists('sale_commission_latam_pct', $validated)) {
            $super_partner->sale_commission_latam_pct = $validated['sale_commission_latam_pct'];
        }

        if (array_key_exists('sale_commission_usa_ca_eu_pct', $validated)) {
            $super_partner->sale_commission_usa_ca_eu_pct = $validated['sale_commission_usa_ca_eu_pct'];
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
            'message'                       => __('default.updated_response', ['name' => 'Comisiones de Super Partner']),
            'commission_percentage'         => (float) ($super_partner->commission_percentage ?? 0),
            'free_esim_rate'                => (float) $super_partner->free_esim_rate,
            'sale_commission_latam_pct'     => $super_partner->sale_commission_latam_pct !== null ? (float) $super_partner->sale_commission_latam_pct : null,
            'sale_commission_usa_ca_eu_pct' => $super_partner->sale_commission_usa_ca_eu_pct !== null ? (float) $super_partner->sale_commission_usa_ca_eu_pct : null,
            'margins'                       => $marginService->getFormattedMargins($super_partner->id),
            'plan_prices'                   => $priceService->getFormattedPlanPrices($super_partner->id),
            'country_prices'                => $priceService->getCountryPrices($super_partner->id),
            'countries'                     => CountryTariffHelper::getAllCountries(),
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

        $filename = 'comisiones-super-partner-' . Str::slug($super_partner->nombre) . '.csv';
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
