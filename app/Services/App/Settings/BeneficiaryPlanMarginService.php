<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\BeneficiaryPlanMargin;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BeneficiaryPlanMarginService extends BaseService
{
    /**
     * Cache key prefix for beneficiary plan margins
     */
    const CACHE_KEY_PREFIX = 'beneficiary_plan_margins_';
    
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    public function __construct(BeneficiaryPlanMargin $beneficiaryPlanMargin)
    {
        $this->model = $beneficiaryPlanMargin;
    }

    /**
     * Calculate final price with beneficiary's profit margin on top of admin margin
     * 
     * Fórmula: Precio final = Precio base del admin / (1 - Margen del beneficiario)
     * 
     * El precio base es el precio que ya incluye el margen del admin.
     * El beneficiario agrega su propio margen sobre ese precio.
     * 
     * @param float $adminPrice Price after admin margin is applied
     * @param string $planCapacity Plan capacity in GB (e.g., "1", "3", "5", "10", "20", "50")
     * @param int $beneficiarioId Beneficiary ID
     * @return float Final price with beneficiary margin applied
     */
    public function calculateFinalPrice($adminPrice, $planCapacity, $beneficiarioId)
    {
        try {
            // Get margin for this beneficiary and plan capacity
            $margin = $this->getMarginForPlan($planCapacity, $beneficiarioId);
            
            // If no margin found or margin is 0, return admin price
            if (!$margin || $margin->margin_percentage == 0) {
                return (float) $adminPrice;
            }

            // Convert percentage to decimal (e.g., 30% -> 0.30)
            $marginDecimal = $margin->margin_percentage / 100;

            // Calculate final price: adminPrice / (1 - margin)
            // Prevent division by zero if margin is 100%
            if ($marginDecimal >= 1) {
                Log::warning("Beneficiary margin is 100% or higher for beneficiary {$beneficiarioId}, plan {$planCapacity}, returning admin price");
                return (float) $adminPrice;
            }

            $finalPrice = $adminPrice / (1 - $marginDecimal);

            return round($finalPrice, 2);
        } catch (\Exception $e) {
            Log::error("Error calculating beneficiary final price for beneficiary {$beneficiarioId}, plan {$planCapacity}: " . $e->getMessage());
            return (float) $adminPrice; // Return admin price on error
        }
    }

    /**
     * Get margin configuration for a specific beneficiary and plan capacity
     * 
     * @param string $planCapacity
     * @param int $beneficiarioId
     * @return BeneficiaryPlanMargin|null
     */
    public function getMarginForPlan($planCapacity, $beneficiarioId)
    {
        $margins = $this->getMargins($beneficiarioId);
        return $margins->firstWhere('plan_capacity', (string) $planCapacity);
    }

    /**
     * Get all active plan margins for a beneficiary (with caching)
     * 
     * @param int $beneficiarioId
     * @return \Illuminate\Support\Collection
     */
    public function getMargins($beneficiarioId)
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $beneficiarioId;
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($beneficiarioId) {
            return BeneficiaryPlanMargin::where('beneficiario_id', $beneficiarioId)
                ->where('is_active', true)
                ->get();
        });
    }

    /**
     * Update multiple plan margins at once for a beneficiary
     * 
     * @param int $beneficiarioId
     * @param array $data Array of margins with plan_capacity as key
     * @return bool
     */
    public function updateMargins($beneficiarioId, array $data)
    {
        try {
            foreach ($data as $planCapacity => $marginData) {
                $margin = BeneficiaryPlanMargin::firstOrCreate(
                    [
                        'beneficiario_id' => $beneficiarioId,
                        'plan_capacity' => $planCapacity
                    ],
                    [
                        'margin_percentage' => 0,
                        'is_active' => true
                    ]
                );

                $margin->update([
                    'margin_percentage' => $marginData['margin_percentage'] ?? $margin->margin_percentage,
                    'is_active' => $marginData['is_active'] ?? true,
                ]);
            }

            // Clear cache after update
            Cache::forget(self::CACHE_KEY_PREFIX . $beneficiarioId);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating beneficiary plan margins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get formatted margins for API response
     * 
     * @param int $beneficiarioId
     * @return array
     */
    public function getFormattedMargins($beneficiarioId)
    {
        $margins = $this->getMargins($beneficiarioId);
        
        $formatted = [];
        foreach ($margins as $margin) {
            $formatted[$margin->plan_capacity] = [
                'margin_percentage' => $margin->margin_percentage,
                'is_active' => $margin->is_active,
            ];
        }

        // If no margins exist, return defaults with 0%
        if (empty($formatted)) {
            $planCapacities = ['1', '3', '5', '10', '20', '50'];
            foreach ($planCapacities as $capacity) {
                $formatted[$capacity] = [
                    'margin_percentage' => 0.00,
                    'is_active' => true,
                ];
            }
        }

        return $formatted;
    }
}
