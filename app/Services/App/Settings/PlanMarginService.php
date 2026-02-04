<?php

namespace App\Services\App\Settings;

use App\Models\App\Settings\PlanMargin;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\Cache;
use Exception;

class PlanMarginService extends BaseService
{
    /**
     * Cache key for plan margins
     */
    const CACHE_KEY = 'plan_margins';

    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    public function __construct(PlanMargin $planMargin)
    {
        $this->model = $planMargin;
    }

    /**
     * Get all plan margins
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMargins()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return PlanMargin::where('is_active', true)
                ->orderByRaw("FIELD(plan_capacity, '1', '3', '5', '10', '20', '50')")
                ->get();
        });
    }

    /**
     * Calculate final price with margin
     * 
     * Fórmula: Precio final = Coste / (1 - Margen)
     * 
     * Ejemplo: Si coste = 100 y margen = 30% (0.30)
     * Precio final = 100 / (1 - 0.30) = 100 / 0.70 = 142.86
     * Ganancia = 142.86 - 100 = 42.86 (30% del precio final)
     *
     * @param float $cost Original cost from API
     * @param string $planCapacity Plan capacity (1, 3, 5, 10, 20, 50)
     * @return float Final price with margin applied
     */
    public function calculateFinalPrice($cost, $planCapacity)
    {
        try {
            // Get margin for the specific plan capacity
            $margin = $this->getMargins()
                ->where('plan_capacity', (string)$planCapacity)
                ->first();

            // If no margin is configured, return original cost
            if (!$margin) {
                return $cost;
            }

            $marginDecimal = $margin->getMarginDecimal();

            // Prevent division by zero
            if ($marginDecimal >= 1) {
                return $cost;
            }

            // Apply margin formula: Final Price = Cost / (1 - Margin)
            $finalPrice = $cost / (1 - $marginDecimal);

            return round($finalPrice, 2);
        } catch (Exception $e) {
            // In case of error, return original cost
            \Log::error('Error calculating final price: ' . $e->getMessage());
            return $cost;
        }
    }

    /**
     * Update multiple plan margins
     *
     * @param array $data Array of margins with plan_capacity as key
     * @return bool
     */
    public function updateMargins(array $data)
    {
        try {
            foreach ($data as $planCapacity => $marginData) {
                $margin = PlanMargin::updateOrCreate(
                    ['plan_capacity' => $planCapacity],
                    [
                        'margin_percentage' => $marginData['margin_percentage'],
                        'is_active' => $marginData['is_active'] ?? true,
                    ]
                );
            }

            // Clear cache after update
            Cache::forget(self::CACHE_KEY);

            return true;
        } catch (Exception $e) {
            \Log::error('Error updating plan margins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get margins formatted for frontend
     *
     * @return array
     */
    public function getFormattedMargins()
    {
        $margins = $this->getMargins();
        $formatted = [];

        foreach ($margins as $margin) {
            $formatted[$margin->plan_capacity] = [
                'id' => $margin->id,
                'plan_capacity' => $margin->plan_capacity,
                'margin_percentage' => (float)$margin->margin_percentage,
                'is_active' => $margin->is_active,
            ];
        }

        return $formatted;
    }
}
