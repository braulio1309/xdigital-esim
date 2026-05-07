<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\SuperPartnerCountryPrice;
use App\Models\App\Settings\SuperPartnerPlanPrice;
use Illuminate\Support\Facades\Log;

class SuperPartnerPriceService
{
    /**
     * Resolve the country-specific margin percentage for a super partner.
     * Returns the configured percentage (e.g. 30.00 for 30%) or null if not configured.
     *
     * @param int         $superPartnerId
     * @param string      $planCapacity   e.g. '1', '3', '5', '10'
     * @param string|null $countryCode    e.g. 'US', 'CO'
     * @return float|null
     */
    public function resolveCountryPercentage(int $superPartnerId, string $planCapacity, ?string $countryCode): ?float
    {
        if (!$countryCode) {
            return null;
        }

        $countryPrice = SuperPartnerCountryPrice::where('super_partner_id', $superPartnerId)
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
     * Save country prices (percentages) for a super partner.
     * $countryPrices is an array of objects: [['country_code'=>'US','plan_capacity'=>'1','percentage'=>30.00], ...]
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

                if (!$countryCode || !$planCapacity || $percentage === null) {
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

