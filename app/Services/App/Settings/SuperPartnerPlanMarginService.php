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
     * Update multiple plan margins at once for a super partner.
     * Only capacities 3, 5 y 10 GB son relevantes.
     *
     * @param int $superPartnerId
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
}
