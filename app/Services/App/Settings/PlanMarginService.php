<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\PlanMargin;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PlanMarginService extends BaseService
{
    /**
     * Cache key for plan margins
     */
    const CACHE_KEY = 'plan_margins_config';
    
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    public function __construct(PlanMargin $planMargin)
    {
        $this->model = $planMargin;
    }

    /**
     * Calculate final price with profit margin
     * 
     * Fórmula: Precio final = Coste / (1 - Margen)
     * 
     * Ejemplo: Si coste = 100 y margen = 30% (0.30)
     * Precio final = 100 / (1 - 0.30) = 100 / 0.70 = 142.86
     * Ganancia = 142.86 - 100 = 42.86 (30% del precio final)
     * 
     * @param float $cost Original cost from API
     * @param string $planCapacity Plan capacity in GB (e.g., "1", "3", "5", "10", "20", "50")
     * @return float Final price with margin applied
     */
    public function calculateFinalPrice($cost, $planCapacity)
    {
        try {
            // Get margin for this plan capacity
            $margin = $this->getMarginForPlan($planCapacity);
            
            // If no margin found or margin is 0, return original cost
            if (!$margin || $margin->margin_percentage == 0) {
                return (float) $cost;
            }

            // Convert percentage to decimal (e.g., 30% -> 0.30)
            $marginDecimal = $margin->margin_percentage / 100;

            // Calculate final price: cost / (1 - margin)
            // Prevent division by zero if margin is 100%
            if ($marginDecimal >= 1) {
                Log::warning("Plan margin is 100% or higher for plan {$planCapacity}, returning original cost");
                return (float) $cost;
            }

            $finalPrice = $cost / (1 - $marginDecimal);

            return round($finalPrice, 2);
        } catch (\Exception $e) {
            Log::error("Error calculating final price for plan {$planCapacity}: " . $e->getMessage());
            return (float) $cost; // Return original cost on error
        }
    }

    /**
     * Get margin configuration for a specific plan capacity
     * 
     * @param string $planCapacity
     * @return PlanMargin|null
     */
    public function getMarginForPlan($planCapacity)
    {
        $margins = $this->getMargins();
        return $margins->firstWhere('plan_capacity', (string) $planCapacity);
    }

    /**
     * Get all active plan margins (with caching)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getMargins()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return PlanMargin::where('is_active', true)->get();
        });
    }

    /**
     * Update multiple plan margins at once
     * 
     * @param array $data Array of margins with plan_capacity as key
     * @return bool
     */
    public function updateMargins(array $data)
    {
        try {
            foreach ($data as $planCapacity => $marginData) {
                $margin = PlanMargin::firstOrCreate(
                    ['plan_capacity' => $planCapacity],
                    ['margin_percentage' => 0, 'is_active' => true]
                );

                $margin->update([
                    'margin_percentage' => $marginData['margin_percentage'] ?? $margin->margin_percentage,
                    'is_active' => $marginData['is_active'] ?? true,
                ]);
            }

            // Clear cache after update
            Cache::forget(self::CACHE_KEY);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating plan margins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get formatted margins for API response
     * 
     * @return array
     */
    public function getFormattedMargins()
    {
        $margins = $this->getMargins();
        
        $formatted = [];
        foreach ($margins as $margin) {
            $formatted[$margin->plan_capacity] = [
                'margin_percentage' => $margin->margin_percentage,
                'is_active' => $margin->is_active,
            ];
        }

        return $formatted;
    }
}
