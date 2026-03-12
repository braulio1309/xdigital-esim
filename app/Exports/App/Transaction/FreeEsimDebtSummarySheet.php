<?php

namespace App\Exports\App\Transaction;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FreeEsimDebtSummarySheet implements FromArray, WithStyles, WithTitle
{
    const COMMISSION_PER_FREE_ESIM = 0.85; // Default fallback when no beneficiary-specific rate is available

    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Deuda eSIM Gratuitas';
    }

    public function array(): array
    {
        $stats = $this->calculateDebtStats();

        $rows = [
            ['RESUMEN DE DEUDA - eSIM GRATUITAS', ''],
            ['', ''],
            ['Descripción', 'Valor'],
        ];

        // Per-beneficiario breakdown rows
        foreach ($stats['by_beneficiario'] as $item) {
            $rows[] = [
                'Beneficiario: ' . $item['nombre'],
                '',
            ];
            $rows[] = [
                '  eSIMs gratuitas sin pagar',
                $item['unpaid_count'],
            ];
            $rows[] = [
                '  Deuda',
                '$' . number_format($item['debt'], 2),
            ];
            $rows[] = ['', ''];
        }

        $rows[] = ['', ''];
        $rows[] = ['Total eSIMs Gratuitas (en el período)', $stats['total_free']];
        $rows[] = ['eSIMs Gratuitas Sin Pagar', $stats['unpaid_count']];
        $rows[] = ['DEUDA TOTAL', '$' . number_format($stats['total_debt'], 2)];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(25);

        $lastRow = $sheet->getHighestRow();

        // Title row
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'],
            ],
        ]);

        // Column headers row (row 3)
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'],
            ],
        ]);

        // Highlight "DEUDA TOTAL" row (last row)
        $sheet->getStyle("A{$lastRow}:B{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFD700'],
            ],
        ]);

        return [];
    }

    protected function calculateDebtStats(): array
    {
        // Base query: all free eSIMs matching the date/beneficiario/super_partner filters
        $baseQuery = Transaction::where('purchase_amount', 0);

        // Apply beneficiario filter
        if (!empty($this->filters['beneficiario_id'])) {
            $beneficiarioId = $this->filters['beneficiario_id'];
            if ($beneficiarioId === 'none') {
                $baseQuery->whereNull('beneficiario_id');
            } else {
                $baseQuery->where('beneficiario_id', $beneficiarioId);
            }
        }

        // Apply super partner filter (all beneficiarios under the given super partner)
        if (!empty($this->filters['super_partner_id'])) {
            $superPartnerId = $this->filters['super_partner_id'];
            $beneficiarioIds = Beneficiario::where('super_partner_id', $superPartnerId)->pluck('id');

            if ($beneficiarioIds->isEmpty()) {
                $baseQuery->whereRaw('1 = 0');
            } else {
                $baseQuery->whereIn('beneficiario_id', $beneficiarioIds);
            }
        }

        // Apply date filters
        if (!empty($this->filters['start_date'])) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $this->filters['start_date']);
            $baseQuery->where('creation_time', '>=', Carbon::parse($cleanDate)->startOfDay());
        }
        if (!empty($this->filters['end_date'])) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $this->filters['end_date']);
            $baseQuery->where('creation_time', '<=', Carbon::parse($cleanDate)->endOfDay());
        }

        // Alcance por tipo de usuario para exportar solo su propia deuda
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $baseQuery->where('beneficiario_id', $beneficiario->id);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'super_partner') {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::where('user_id', auth()->id())->first();
            if ($superPartner) {
                $partnerIds = $superPartner->beneficiarios()->pluck('id');
                $baseQuery->whereIn('beneficiario_id', $partnerIds);
            }
        }

        $totalFree = (clone $baseQuery)->count();

        // Unpaid transactions with beneficiary relations eagerly loaded
        $unpaidTransactions = (clone $baseQuery)
            ->where('is_paid', false)
            ->with(['beneficiario', 'cliente.beneficiario'])
            ->get();

        $unpaidCount = $unpaidTransactions->count();

        $totalDebt = $unpaidTransactions->sum(function (Transaction $transaction) {
            return $transaction->getCommissionAmount();
        });

        // Breakdown per beneficiario
        $byBeneficiario = $unpaidTransactions
            ->groupBy(function ($t) {
                $beneficiario = $t->resolveBeneficiario();
                return $beneficiario ? $beneficiario->nombre : 'Sin Beneficiario';
            })
            ->map(function ($transactions, $nombre) {
                $count = $transactions->count();

                $debt = $transactions->sum(function (Transaction $transaction) {
                    return $transaction->getCommissionAmount();
                });

                return [
                    'nombre' => $nombre,
                    'unpaid_count' => $count,
                    'debt' => $debt,
                ];
            })
            ->sortBy('nombre')
            ->values()
            ->toArray();

        return [
            'total_free' => $totalFree,
            'unpaid_count' => $unpaidCount,
            'total_debt' => $totalDebt,
            'by_beneficiario' => $byBeneficiario,
        ];
    }
}
