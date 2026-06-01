<?php

namespace App\Http\Controllers\App\Settings;

use App\Helpers\CountryTariffHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\BeneficiaryPlanMarginRequest;
use App\Models\App\Beneficiario\Beneficiario;
use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\BeneficiaryPriceService;
use Illuminate\Http\Request;

class BeneficiaryPlanMarginController extends Controller
{
    protected $service;
    protected $priceService;

    public function __construct(BeneficiaryPlanMarginService $service, BeneficiaryPriceService $priceService)
    {
        $this->service = $service;
        $this->priceService = $priceService;
    }

    /**
     * Get all plan margins + plan prices + country prices configuration for a beneficiary
     */
    public function index(Request $request)
    {
        // Super partners cannot manage plan margins/rates
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            return response()->json(['message' => 'Unauthorized. Super partners cannot manage plan margins.'], 403);
        }

        $request->validate([
            'beneficiario_id' => 'required|integer|exists:beneficiarios,id',
        ]);

        $beneficiarioId = $request->input('beneficiario_id');
        $beneficiario = Beneficiario::findOrFail($beneficiarioId);

        return response()->json([
            'margins' => $this->service->getFormattedMargins($beneficiarioId),
            'free_esim_rate' => (float) $beneficiario->free_esim_rate,
            'sale_commission_latam_pct' => $beneficiario->sale_commission_latam_pct !== null ? (float) $beneficiario->sale_commission_latam_pct : null,
            'sale_commission_usa_ca_eu_pct' => $beneficiario->sale_commission_usa_ca_eu_pct !== null ? (float) $beneficiario->sale_commission_usa_ca_eu_pct : null,
            'plan_prices' => $this->priceService->getFormattedPlanPrices($beneficiarioId),
            'country_prices' => $this->priceService->getCountryPrices($beneficiarioId),
            'countries' => CountryTariffHelper::getAllCountries(),
        ]);
    }

    /**
     * Update plan margins, plan prices, and country prices for a beneficiary
     */
    public function update(BeneficiaryPlanMarginRequest $request)
    {
        // Super partners cannot manage plan margins/rates
        if (auth()->check() && auth()->user()->user_type === 'super_partner') {
            return response()->json(['message' => 'Unauthorized. Super partners cannot manage plan margins.'], 403);
        }

        $beneficiarioId = $request->input('beneficiario_id');
        $margins = $request->input('margins', []);
        $freeEsimRate = $request->input('free_esim_rate');
        $planPrices = $request->input('plan_prices', []);
        $countryPrices = $request->input('country_prices', []);
        $saleCommissionLatamPct = $request->has('sale_commission_latam_pct') ? $request->input('sale_commission_latam_pct') : false;
        $saleCommissionUsaCaEuPct = $request->has('sale_commission_usa_ca_eu_pct') ? $request->input('sale_commission_usa_ca_eu_pct') : false;

        $latamValue = $saleCommissionLatamPct === false ? false : ($saleCommissionLatamPct !== null ? (float) $saleCommissionLatamPct : null);
        $usaCaEuValue = $saleCommissionUsaCaEuPct === false ? false : ($saleCommissionUsaCaEuPct !== null ? (float) $saleCommissionUsaCaEuPct : null);

        $success = $this->service->updateMargins(
            $beneficiarioId,
            $margins,
            $freeEsimRate !== null ? (float) $freeEsimRate : null,
            $latamValue,
            $usaCaEuValue
        );

        if ($success) {
            // Update manual plan prices
            if (!empty($planPrices)) {
                $this->priceService->updatePlanPrices($beneficiarioId, $planPrices);
            }

            // Update country-specific prices
            $this->priceService->updateCountryPrices($beneficiarioId, $countryPrices);

            $beneficiario = Beneficiario::findOrFail($beneficiarioId);

            return updated_responses('beneficiary_plan_margins', [
                'margins' => $this->service->getFormattedMargins($beneficiarioId),
                'free_esim_rate' => (float) $beneficiario->free_esim_rate,
                'sale_commission_latam_pct' => $beneficiario->sale_commission_latam_pct !== null ? (float) $beneficiario->sale_commission_latam_pct : null,
                'sale_commission_usa_ca_eu_pct' => $beneficiario->sale_commission_usa_ca_eu_pct !== null ? (float) $beneficiario->sale_commission_usa_ca_eu_pct : null,
                'plan_prices' => $this->priceService->getFormattedPlanPrices($beneficiarioId),
                'country_prices' => $this->priceService->getCountryPrices($beneficiarioId),
                'countries' => CountryTariffHelper::getAllCountries(),
            ]);
        }

        return failed_responses();
    }
}

