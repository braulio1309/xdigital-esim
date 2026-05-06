<?php

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\BeneficiaryPlanMarginRequest;
use App\Helpers\CountryTariffHelper;
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
            'plan_prices' => $this->priceService->getFormattedPlanPrices($beneficiarioId),
            'country_prices' => $this->priceService->getCountryPrices($beneficiarioId),
            'free_esim_countries' => $this->priceService->getFreeEsimCountries($beneficiarioId),
            'all_countries' => collect(CountryTariffHelper::getAllCountries())->map(function ($c) {
                return [
                    'code' => $c['code'],
                    'name' => $c['name'],
                    'region' => $c['region'] ?? '',
                    'is_affordable' => CountryTariffHelper::isAffordableCountryCode($c['code']),
                ];
            })->sortBy('name')->values()->all(),
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
        $freeEsimCountries = $request->input('free_esim_countries', []);

        $success = $this->service->updateMargins(
            $beneficiarioId,
            $margins,
            $freeEsimRate !== null ? (float) $freeEsimRate : null
        );

        if ($success) {
            // Update manual plan prices
            if (!empty($planPrices)) {
                $this->priceService->updatePlanPrices($beneficiarioId, $planPrices);
            }

            // Update country-specific prices
            $this->priceService->updateCountryPrices($beneficiarioId, $countryPrices);

            // Update free eSIM countries
            $this->priceService->updateFreeEsimCountries($beneficiarioId, $freeEsimCountries);

            $beneficiario = Beneficiario::findOrFail($beneficiarioId);

            return updated_responses('beneficiary_plan_margins', [
                'margins' => $this->service->getFormattedMargins($beneficiarioId),
                'free_esim_rate' => (float) $beneficiario->free_esim_rate,
                'plan_prices' => $this->priceService->getFormattedPlanPrices($beneficiarioId),
                'country_prices' => $this->priceService->getCountryPrices($beneficiarioId),
                'free_esim_countries' => $this->priceService->getFreeEsimCountries($beneficiarioId),
            ]);
        }

        return failed_responses();
    }
}

