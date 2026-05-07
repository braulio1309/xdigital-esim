<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\SuperPartnerCountryPrice;
use App\Models\App\Settings\SuperPartnerPlanPrice;
use Illuminate\Support\Facades\Log;

class SuperPartnerPriceService
{
    /**
     * Resolve the price to charge for a free eSIM activation.
     * Priority:
     *  1. Country-specific price (super_partner_country_prices) - legacy fixed price
     *  2. General plan price (super_partner_plan_prices)
     *  3. null (caller falls back to existing percentage/rate system)
     *
     * @param int    $superPartnerId
     * @param string $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode e.g. 'US', 'CO'
     * @return float|null
     */
    public function resolvePrice(int $superPartnerId, string $planCapacity, ?string $countryCode): ?float
    {
        if ($countryCode) {
            $countryPrice = SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
                ->where('plan_capacity', $planCapacity)
                ->where('country_code', strtoupper($countryCode))
                ->where('is_active', true)
                ->first();

            if ($countryPrice && $countryPrice->price !== null) {
                return (float) $countryPrice->price;
            }
        }

        $planPrice = SuperPartnerPlanPrice::where('super_partner_id', $superPartnerId)
            ->where('plan_capacity', $planCapacity)
            ->where('is_active', true)
            ->first();

        if ($planPrice) {
            return (float) $planPrice->price;
        }

        return null;
    }

    /**
     * Get country-specific percentage margin for a super partner.
     * Returns the percentage (0-100) if a matching active entry exists, or null.
     *
     * @param int    $superPartnerId
     * @param string $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode e.g. 'US', 'CO'
     * @return float|null
     */
    public function getCountryPercentage(int $superPartnerId, string $planCapacity, ?string $countryCode): ?float
    {
        if (!$countryCode) {
            return null;
        }

        $countryPrice = SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
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
     * Get all country codes that have an active percentage > 0 configured for a super partner.
     * Used to determine which countries should be treated as "affordable" for free eSIM activation.
     *
     * @param int $superPartnerId
     * @return string[] Array of uppercase country codes (e.g. ['US', 'CO'])
     */
    public function getCountryCodesWithPercentages(int $superPartnerId): array
    {
        return SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
            ->where('is_active', true)
            ->where('percentage', '>', 0)
            ->pluck('country_code')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get formatted plan prices for API response.
     *
     * @param int $superPartnerId
     * @return array keyed by plan_capacity
     */
    public function getFormattedPlanPrices(int $superPartnerId): array
    {
        $prices = SuperPartnerPlanPrice::where('super_partner_id', $superPartnerId)
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
     * Get all country price entries for a super partner.
     *
     * @param int $superPartnerId
     * @return array
     */
    public function getCountryPrices(int $superPartnerId): array
    {
        return SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
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
     * Save plan prices for a super partner.
     *
     * @param int   $superPartnerId
     * @param array $planPrices
     * @return bool
     */
    public function updatePlanPrices(int $superPartnerId, array $planPrices): bool
    {
        try {
            foreach ($planPrices as $planCapacity => $data) {
                $price = $data['price'] ?? null;

                if ($price === null || $price === '') {
                    SuperPartnerPlanPrice::where('super_partner_id', $superPartnerId)
                        ->where('plan_capacity', (string) $planCapacity)
                        ->delete();
                    continue;
                }

                SuperPartnerPlanPrice::updateOrCreate(
                    [
                        'super_partner_id' => $superPartnerId,
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
            Log::error('Error updating super partner plan prices: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save country prices (percentage-based) for a super partner.
     * $countryPrices is an array of objects: [['country_code'=>'US','plan_capacity'=>'1','percentage'=>30], ...]
     *
     * @param int   $superPartnerId
     * @param array $countryPrices
     * @return bool
     */
    public function updateCountryPrices(int $superPartnerId, array $countryPrices): bool
    {
        try {
            $incomingKeys = [];

            foreach ($countryPrices as $data) {
                $countryCode = strtoupper((string) ($data['country_code'] ?? ''));
                $planCapacity = (string) ($data['plan_capacity'] ?? '');
                $percentage = $data['percentage'] ?? null;

                if (!$countryCode || !$planCapacity || $percentage === null || $percentage === '') {
                    continue;
                }

                SuperPartnerCountryPrice::updateOrCreate(
                    [
                        'super_partner_id' => $superPartnerId,
                        'country_code' => $countryCode,
                        'plan_capacity' => $planCapacity,
                    ],
                    [
                        'percentage' => (float) $percentage,
                        'price' => null,
                        'is_active' => $data['is_active'] ?? true,
                    ]
                );

                $incomingKeys[] = $countryCode . '|' . $planCapacity;
            }

            // Remove entries that were not sent (deleted by user)
            SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
                ->get()
                ->each(function ($existing) use ($incomingKeys) {
                    $key = $existing->country_code . '|' . $existing->plan_capacity;
                    if (!in_array($key, $incomingKeys)) {
                        $existing->delete();
                    }
                });

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating super partner country prices: ' . $e->getMessage());
            return false;
        }
    }
}
