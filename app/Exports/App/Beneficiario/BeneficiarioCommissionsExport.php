<?php

namespace App\Exports\App\Beneficiario;

use App\Services\App\Settings\BeneficiaryPlanMarginService;
use App\Services\App\Settings\PlanMarginService;
use App\Services\EsimFxService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiarioCommissionsExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * Plans to include in export (GB amounts)
     */
    const PLAN_CAPACITIES = [3, 5, 10];

    protected $beneficiarioId;
    protected $beneficiarioName;
    protected $esimService;
    protected $planMarginService;
    protected $beneficiaryPlanMarginService;

    public function __construct(
        int $beneficiarioId,
        string $beneficiarioName,
        EsimFxService $esimService,
        PlanMarginService $planMarginService,
        BeneficiaryPlanMarginService $beneficiaryPlanMarginService
    ) {
        $this->beneficiarioId = $beneficiarioId;
        $this->beneficiarioName = $beneficiarioName;
        $this->esimService = $esimService;
        $this->planMarginService = $planMarginService;
        $this->beneficiaryPlanMarginService = $beneficiaryPlanMarginService;
    }

    public function headings(): array
    {
        return [
            'País',
            'Plan 3GB - Precio',
            'Plan 5GB - Precio',
            'Plan 10GB - Precio',
        ];
    }

    public function array(): array
    {
        try {
            // Fetch all products from the API
            $products = $this->esimService->getProducts([]);
        } catch (\Exception $e) {
            Log::error('BeneficiarioCommissionsExport: error fetching products - ' . $e->getMessage());
            $products = [];
        }

        // Group products by country, keeping only 3, 5, and 10 GB plans
        $byCountry = [];
        foreach ($products as $product) {
            $rawAmount = $product['amount'] ?? null;
            if (!is_numeric($rawAmount)) {
                continue;
            }
            $amount = (int) $rawAmount;
            if (!in_array($amount, self::PLAN_CAPACITIES)) {
                continue;
            }

            // Use country code as the primary key to avoid duplicates
            $countryName = $product['name'] ?? 'Unknown';
            $countryCode = '';

            if (!empty($product['coverage']) && is_array($product['coverage'])) {
                $firstCoverage = $product['coverage'][0] ?? [];
                $countryName = $firstCoverage['name'] ?? $product['name'] ?? 'Unknown';
                $countryCode = $firstCoverage['country_code'] ?? '';
            }

            // Normalize the grouping key: prefer country_code, fall back to lowercase country name
            $key = $countryCode !== '' ? strtoupper($countryCode) : strtolower(trim($countryName));

            if (!isset($byCountry[$key])) {
                $byCountry[$key] = [
                    'name' => $countryName,
                    'plans' => [],
                ];
            }

            // Calculate partner price: first apply admin margin, then partner margin
            $originalPrice = (float) ($product['price'] ?? 0);
            $adminPrice = $this->planMarginService->calculateFinalPrice($originalPrice, (string) $amount);
            $partnerPrice = $this->beneficiaryPlanMarginService->calculateFinalPrice($adminPrice, (string) $amount, $this->beneficiarioId);

            $byCountry[$key]['plans'][$amount] = $partnerPrice;
        }

        // Sort by country name
        usort($byCountry, fn($a, $b) => strcmp($a['name'], $b['name']));

        // Build rows
        $rows = [];
        foreach ($byCountry as $entry) {
            $rows[] = [
                $entry['name'],
                isset($entry['plans'][3])  ? '$' . number_format($entry['plans'][3],  2) : 'N/A',
                isset($entry['plans'][5])  ? '$' . number_format($entry['plans'][5],  2) : 'N/A',
                isset($entry['plans'][10]) ? '$' . number_format($entry['plans'][10], 2) : 'N/A',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
