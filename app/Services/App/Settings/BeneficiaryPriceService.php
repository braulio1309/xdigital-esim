<?php

namespace App\Services\App\Settings;

use App\Helpers\CountryTariffHelper;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Settings\BeneficiaryCountryPrice;
use App\Models\App\Settings\BeneficiaryFreeEsimCountry;
use App\Models\App\Settings\BeneficiaryPlanPrice;
use Illuminate\Support\Facades\Log;

class BeneficiaryPriceService
{
    /**
     * Resolve the price to charge for a free eSIM activation.
     * Priority:
     *  1. Country-specific price (beneficiary_country_prices)
     *  2. General plan price (beneficiary_plan_prices)
     *  3. null (caller falls back to existing percentage/rate system)
     *
     * @param int    $beneficiarioId
     * @param string $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode e.g. 'US', 'CO'
     * @return float|null
     */
    public function resolvePrice(int $beneficiarioId, string $planCapacity, ?string $countryCode): ?float
    {
        if ($countryCode) {
            $countryPrice = BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
                ->where('plan_capacity', $planCapacity)
                ->where('country_code', strtoupper($countryCode))
                ->where('is_active', true)
                ->first();

            if ($countryPrice) {
                return (float) $countryPrice->price;
            }
        }

        $planPrice = BeneficiaryPlanPrice::where('beneficiario_id', $beneficiarioId)
            ->where('plan_capacity', $planCapacity)
            ->where('is_active', true)
            ->first();

        if ($planPrice) {
            return (float) $planPrice->price;
        }

        return null;
    }

    /**
     * Get formatted plan prices for API response.
     *
     * @param int $beneficiarioId
     * @return array keyed by plan_capacity
     */
    public function getFormattedPlanPrices(int $beneficiarioId): array
    {
        $prices = BeneficiaryPlanPrice::where('beneficiario_id', $beneficiarioId)
            ->where('is_active', true)
            ->get();

        $formatted = [];
        foreach ($prices as $price) {
            $formatted[$price->plan_capacity] = [
                'price' => (float) $price->price,
                'is_active' => $price->is_active,
            ];
        }

        return $formatted;
    }

    /**
     * Get all country price entries for a beneficiary.
     *
     * @param int $beneficiarioId
     * @return array
     */
    public function getCountryPrices(int $beneficiarioId): array
    {
        return BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->orderBy('country_code')
            ->orderBy('plan_capacity')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'country_code' => $item->country_code,
                    'plan_capacity' => $item->plan_capacity,
                    'price' => (float) $item->price,
                    'is_active' => $item->is_active,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Save plan prices for a beneficiary.
     * $planPrices is a key-value array: ['1' => ['price' => 1.00], '3' => [...], ...]
     *
     * @param int   $beneficiarioId
     * @param array $planPrices
     * @return bool
     */
    public function updatePlanPrices(int $beneficiarioId, array $planPrices): bool
    {
        try {
            foreach ($planPrices as $planCapacity => $data) {
                $price = $data['price'] ?? null;

                if ($price === null || $price === '') {
                    // Remove if empty
                    BeneficiaryPlanPrice::where('beneficiario_id', $beneficiarioId)
                        ->where('plan_capacity', (string) $planCapacity)
                        ->delete();
                    continue;
                }

                BeneficiaryPlanPrice::updateOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'plan_capacity' => (string) $planCapacity,
                    ],
                    [
                        'price' => (float) $price,
                        'is_active' => $data['is_active'] ?? true,
                    ]
                );
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating beneficiary plan prices: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save country prices for a beneficiary.
     * $countryPrices is an array of objects: [['country_code'=>'US','plan_capacity'=>'1','price'=>1.00], ...]
     *
     * @param int   $beneficiarioId
     * @param array $countryPrices
     * @return bool
     */
    public function updateCountryPrices(int $beneficiarioId, array $countryPrices): bool
    {
        try {
            // Collect incoming keys to know what to keep
            $incomingKeys = [];

            foreach ($countryPrices as $data) {
                $countryCode = strtoupper((string) ($data['country_code'] ?? ''));
                $planCapacity = (string) ($data['plan_capacity'] ?? '');
                $price = $data['price'] ?? null;

                if (!$countryCode || !$planCapacity || $price === null) {
                    continue;
                }

                BeneficiaryCountryPrice::updateOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'country_code' => $countryCode,
                        'plan_capacity' => $planCapacity,
                    ],
                    [
                        'price' => (float) $price,
                        'is_active' => $data['is_active'] ?? true,
                    ]
                );

                $incomingKeys[] = $countryCode . '|' . $planCapacity;
            }

            // Remove entries that were not sent (deleted by user)
            BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
                ->get()
                ->each(function ($existing) use ($incomingKeys) {
                    $key = $existing->country_code . '|' . $existing->plan_capacity;
                    if (!in_array($key, $incomingKeys)) {
                        $existing->delete();
                    }
                });

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating beneficiary country prices: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all free eSIM countries configuration for a beneficiary.
     * Returns an array keyed by country_code.
     *
     * @param int $beneficiarioId
     * @return array
     */
    public function getFreeEsimCountries(int $beneficiarioId): array
    {
        return BeneficiaryFreeEsimCountry::where('beneficiario_id', $beneficiarioId)
            ->orderBy('country_code')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'country_code' => $item->country_code,
                    'is_active' => (bool) $item->is_active,
                    'price' => $item->price !== null ? (float) $item->price : null,
                    'plan_capacity' => $item->plan_capacity,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Check whether a country is enabled for free eSIM activation for a given beneficiary.
     * Falls back to the global affordable-country check when no per-beneficiary configuration exists.
     *
     * @param int    $beneficiarioId
     * @param string $countryCode
     * @return bool
     */
    public function isCountryEnabledForFreeEsim(int $beneficiarioId, string $countryCode): bool
    {
        $countryCode = strtoupper($countryCode);

        $row = BeneficiaryFreeEsimCountry::where('beneficiario_id', $beneficiarioId)
            ->where('country_code', $countryCode)
            ->first();

        if ($row !== null) {
            return (bool) $row->is_active;
        }

        // No per-beneficiary override → fall back to global rule
        return CountryTariffHelper::isAffordableCountryCode($countryCode);
    }

    /**
     * Get the plan capacity configured for a country's free eSIM for a given beneficiary.
     * Returns null if no override exists (caller should use its own default).
     *
     * @param int    $beneficiarioId
     * @param string $countryCode
     * @return string|null
     */
    public function getFreeEsimPlanCapacityForCountry(int $beneficiarioId, string $countryCode): ?string
    {
        $row = BeneficiaryFreeEsimCountry::where('beneficiario_id', $beneficiarioId)
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', true)
            ->first();

        return $row ? $row->plan_capacity : null;
    }

    /**
     * Get the configured price for a country's free eSIM activation for a given beneficiary.
     * Returns null if no per-beneficiary price is set.
     *
     * @param int    $beneficiarioId
     * @param string $countryCode
     * @return float|null
     */
    public function getFreeEsimPriceForCountry(int $beneficiarioId, string $countryCode): ?float
    {
        $row = BeneficiaryFreeEsimCountry::where('beneficiario_id', $beneficiarioId)
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', true)
            ->first();

        if ($row && $row->price !== null) {
            return (float) $row->price;
        }

        return null;
    }

    /**
     * Save free eSIM countries configuration for a beneficiary.
     * Entries not present in the incoming list are removed.
     *
     * @param int   $beneficiarioId
     * @param array $countries  Array of objects: [['country_code'=>'CO','is_active'=>true,'price'=>0.85,'plan_capacity'=>'1'], ...]
     * @return bool
     */
    public function updateFreeEsimCountries(int $beneficiarioId, array $countries): bool
    {
        try {
            $incomingCodes = [];

            foreach ($countries as $data) {
                $countryCode = strtoupper((string) ($data['country_code'] ?? ''));

                if (strlen($countryCode) !== 2) {
                    continue;
                }

                $price = $data['price'] ?? null;
                if ($price === '' || $price === null) {
                    $price = null;
                } else {
                    $price = (float) $price;
                }

                BeneficiaryFreeEsimCountry::updateOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'country_code'    => $countryCode,
                    ],
                    [
                        'is_active'     => (bool) ($data['is_active'] ?? true),
                        'price'         => $price,
                        'plan_capacity' => (string) ($data['plan_capacity'] ?? '1'),
                    ]
                );

                $incomingCodes[] = $countryCode;
            }

            // Remove entries that were not included in the payload
            BeneficiaryFreeEsimCountry::where('beneficiario_id', $beneficiarioId)
                ->whereNotIn('country_code', $incomingCodes)
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating beneficiary free eSIM countries: ' . $e->getMessage());
            return false;
        }
    }
}
