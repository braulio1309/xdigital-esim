<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\SuperPartnerPlanMargin;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Log;

class SuperPartnerPlanMarginService extends BaseService
{
    public function __construct(SuperPartnerPlanMargin $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active plan margins for a super partner.
     *
     * @param int $superPartnerId
     * @return \Illuminate\Support\Collection
     */
    public function getMargins($superPartnerId)
    {
        return SuperPartnerPlanMargin::where('super_partner_id', $superPartnerId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get margin configuration for a specific super partner and plan capacity.
     *
     * @param string $planCapacity
     * @param int    $superPartnerId
     * @return SuperPartnerPlanMargin|null
     */
    public function getMarginForPlan($planCapacity, $superPartnerId)
    {
        $margins = $this->getMargins($superPartnerId);

        return $margins->firstWhere('plan_capacity', (string) $planCapacity);
    }

    /**
     * Update multiple plan margins at once for a super partner.
     * Only capacities 3, 5 y 10 GB son relevantes.
     *
     * @param int   $superPartnerId
     * @param array $data
     * @return bool
     */
    public function updateMargins($superPartnerId, array $data)
    {
        try {
            foreach ($data as $planCapacity => $marginData) {
                $margin = SuperPartnerPlanMargin::firstOrCreate(
                    [
                        'super_partner_id' => $superPartnerId,
                        'plan_capacity' => (string) $planCapacity,
                    ],
                    [
                        'margin_percentage' => 0,
                        'is_active' => true,
                    ]
                );

                $margin->update([
                    'margin_percentage' => $marginData['margin_percentage'] ?? $margin->margin_percentage,
                    'is_active' => $marginData['is_active'] ?? true,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating super partner plan margins: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get formatted margins for API response.
     * Always returns entries for 3, 5 y 10 GB.
     *
     * @param int $superPartnerId
     * @return array
     */
    public function getFormattedMargins($superPartnerId)
    {
        $margins = $this->getMargins($superPartnerId);

        $formatted = [];
        foreach ($margins as $margin) {
            $formatted[$margin->plan_capacity] = [
                'margin_percentage' => $margin->margin_percentage,
                'is_active' => $margin->is_active,
            ];
        }

        $planCapacities = ['3', '5', '10'];
        foreach ($planCapacities as $capacity) {
            if (!isset($formatted[$capacity])) {
                $formatted[$capacity] = [
                    'margin_percentage' => 0.00,
                    'is_active' => true,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Calculate final price with super partner's profit margin on top of admin margin.
     *
     * Fórmula: Precio final = Precio base del admin / (1 - Margen del super partner)
     *
     * @param float  $adminPrice     Price after admin margin is applied
     * @param string $planCapacity   Plan capacity in GB (e.g., "3", "5", "10")
     * @param int    $superPartnerId Super partner ID
     *
     * @return float Final price with super partner margin applied
     */
    public function calculateFinalPrice($adminPrice, $planCapacity, $superPartnerId)
    {
        try {
            $margin = $this->getMarginForPlan($planCapacity, $superPartnerId);

            // If no margin or 0%, return admin price unchanged
            if (!$margin || $margin->margin_percentage == 0) {
                return (float) $adminPrice;
            }

            $marginDecimal = $margin->margin_percentage / 100;

            // Prevent division by zero if margin is 100%
            if ($marginDecimal >= 1) {
                Log::warning("Super partner margin is 100% or higher for super_partner_id {$superPartnerId}, plan {$planCapacity}, returning admin price");

                return (float) $adminPrice;
            }

            $finalPrice = $adminPrice / (1 - $marginDecimal);

            return round($finalPrice, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating super partner final price: ' . $e->getMessage());

            return (float) $adminPrice;
        }
    }
}
