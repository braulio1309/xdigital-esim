<?php

namespace App\Services\App\Settings;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Settings\BeneficiaryCountryPrice;
use App\Models\App\Settings\BeneficiaryPlanPrice;
use Illuminate\Support\Facades\Log;

class BeneficiaryPriceService
{
    /**
     * Resolve the price to charge for a free eSIM activation.
     * Priority:
     *  1. Country-specific price (beneficiary_country_prices) - legacy fixed price
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

            if ($countryPrice && $countryPrice->price !== null) {
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
     * Get a country-specific fixed price for a beneficiary.
     * Returns null when no fixed price is configured for the requested country/plan.
     */
    public function getCountryFixedPrice(int $beneficiarioId, string $planCapacity, ?string $countryCode): ?float
    {
        if (!$countryCode) {
            return null;
        }

        $countryPrice = BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->where('plan_capacity', $planCapacity)
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', true)
            ->first();

        if ($countryPrice && $countryPrice->price !== null) {
            return (float) $countryPrice->price;
        }

        return null;
    }

    /**
     * Get country-specific percentage margin for a beneficiary.
     * Returns the percentage (0-100) if a matching active entry exists, or null.
     *
     * @param int    $beneficiarioId
     * @param string $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode e.g. 'US', 'CO'
     * @return float|null
     */
    public function getCountryPercentage(int $beneficiarioId, string $planCapacity, ?string $countryCode): ?float
    {
        if (!$countryCode) {
            return null;
        }

        $countryPrice = BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->where('plan_capacity', $planCapacity)
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', true)
            ->first();

        if ($countryPrice && $countryPrice->percentage > 0) {
            return (float) $countryPrice->percentage;
        }

        return null;
    }

    /**
     * Get all country codes that have an active percentage > 0 configured for a beneficiary.
     * Used to determine which countries should be treated as "affordable" for free eSIM activation.
     *
     * @param int $beneficiarioId
     * @return string[] Array of uppercase country codes (e.g. ['US', 'CO'])
     */
    public function getCountryCodesWithPercentages(int $beneficiarioId): array
    {
        return BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->where('is_active', true)
            ->where('percentage', '>', 0)
            ->pluck('country_code')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get country codes that have any free eSIM country configuration for a beneficiary.
     * Includes percentage-based entries and fixed 1GB prices.
     *
     * @param int $beneficiarioId
     * @return string[]
     */
    public function getCountryCodesWithFreeEsimPricing(int $beneficiarioId): array
    {
        return BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('percentage', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('plan_capacity', '1')
                            ->whereNotNull('price');
                    });
            })
            ->pluck('country_code')
            ->unique()
            ->values()
            ->toArray();
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
                    'percentage' => (float) $item->percentage,
                    'price' => $item->price !== null ? (float) $item->price : null,
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
     * Save country prices (percentage-based) for a beneficiary.
     * $countryPrices is an array of objects: [['country_code'=>'US','plan_capacity'=>'1','percentage'=>30], ...]
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
                $percentage = $data['percentage'] ?? null;
                $price = $data['price'] ?? null;

                if (!$countryCode || !$planCapacity) {
                    continue;
                }

                $payload = [
                    'is_active' => $data['is_active'] ?? true,
                ];

                if ($planCapacity === '1') {
                    if ($price === null || $price === '') {
                        continue;
                    }

                    $payload['price'] = (float) $price;
                    $payload['percentage'] = 0;
                } else {
                    if ($percentage === null || $percentage === '') {
                        continue;
                    }

                    $payload['percentage'] = (float) $percentage;
                    $payload['price'] = null;
                }

                BeneficiaryCountryPrice::updateOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'country_code' => $countryCode,
                        'plan_capacity' => $planCapacity,
                    ],
                    $payload
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
}
