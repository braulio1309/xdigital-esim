<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\BeneficiaryCountryPrice;
use App\Models\App\Settings\BeneficiaryPlanPrice;
use Illuminate\Support\Facades\Log;

class BeneficiaryPriceService
{
    /**
     * Resolve the country-specific margin percentage for a beneficiary.
     * Returns the configured percentage (e.g. 30.00 for 30%) or null if not configured.
     *
     * @param int         $beneficiarioId
     * @param string      $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode    e.g. 'US', 'CO'
     * @return float|null
     */
    public function resolveCountryPercentage(int $beneficiarioId, string $planCapacity, ?string $countryCode): ?float
    {
        if (!$countryCode) {
            return null;
        }

        $countryPrice = BeneficiaryCountryPrice::where('beneficiario_id', $beneficiarioId)
            ->where('plan_capacity', $planCapacity)
            ->where('country_code', strtoupper($countryCode))
            ->where('is_active', true)
            ->first();

        if ($countryPrice) {
            return (float) $countryPrice->percentage;
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
                    'percentage' => (float) $item->percentage,
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
     * Save country prices (percentages) for a beneficiary.
     * $countryPrices is an array of objects: [['country_code'=>'US','plan_capacity'=>'1','percentage'=>30.00], ...]
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

                if (!$countryCode || !$planCapacity || $percentage === null) {
                    continue;
                }

                BeneficiaryCountryPrice::updateOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'country_code' => $countryCode,
                        'plan_capacity' => $planCapacity,
                    ],
                    [
                        'percentage' => (float) $percentage,
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
}

