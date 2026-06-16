<?php

namespace App\Exports\App\Transaction;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FreeEsimDebtSummarySheet implements FromArray, WithStyles, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Estado de Cuenta';
    }

    public function array(): array
    {
        $stats = $this->calculateAccountStats();

        $period = $this->buildPeriodLabel();
        $partnerLabel = $this->buildPartnerLabel();

        $rows = [];

        // ── Header ──────────────────────────────────────────────────────────
        $rows[] = ['ESTADO DE CUENTA', ''];
        $rows[] = ['Período: ' . $period, ''];
        if ($partnerLabel) {
            $rows[] = [$partnerLabel, ''];
        }
        $rows[] = ['', ''];

        // ── Column headers ───────────────────────────────────────────────────
        $rows[] = ['Descripción', 'Importe (USD)'];

        // ── Free eSIMs section ───────────────────────────────────────────────
        $rows[] = ['── eSIMs Gratuitas Otorgadas ──', ''];
        $rows[] = ['Cantidad de eSIMs gratuitas', $stats['free_count']];
        $rows[] = ['Cargo promedio por eSIM gratuita', '$' . number_format($stats['free_avg_rate'], 4)];
        $rows[] = ['Subtotal eSIMs gratuitas (nos deben)', '$' . number_format($stats['free_total'], 2)];
        $rows[] = ['', ''];

        // ── Paid eSIMs section ───────────────────────────────────────────────
        $rows[] = ['── eSIMs de Planes de Pago ──', ''];
        $rows[] = ['Cantidad de eSIMs de pago', $stats['paid_count']];
        $rows[] = ['Comisión total generada por ventas', '$' . number_format($stats['paid_commission_total'], 2)];
        $rows[] = ['Subtotal comisiones a pagar (les debemos)', '$' . number_format($stats['paid_commission_total'], 2)];
        $rows[] = ['', ''];

        // ── Summary section ──────────────────────────────────────────────────
        $rows[] = ['── Resumen ──', ''];
        $rows[] = ['Total de transacciones en el período', $stats['total_transactions']];
        $rows[] = ['', ''];

        // ── Balance ──────────────────────────────────────────────────────────
        $rows[] = ['── Balance Final ──', ''];
        $rows[] = ['Cargo eSIMs gratuitas (nos deben)', '$' . number_format($stats['free_total'], 2)];
        $rows[] = ['Comisiones por ventas (les debemos)', '$' . number_format($stats['paid_commission_total'], 2)];

        $balance = $stats['free_total'] - $stats['paid_commission_total'];
        if ($balance > 0) {
            $rows[] = ['SALDO A NUESTRO FAVOR (nos deben pagar)', '$' . number_format($balance, 2)];
        } elseif ($balance < 0) {
            $rows[] = ['SALDO A PAGAR (les debemos pagar)', '$' . number_format(abs($balance), 2)];
        } else {
            $rows[] = ['SALDO EQUILIBRADO (sin pagos pendientes)', '$0.00'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(52);
        $sheet->getColumnDimension('B')->setWidth(28);

        $lastRow = $sheet->getHighestRow();

        // Title row (row 1)
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Period / Partner header rows
        foreach ([2, 3, 4] as $row) {
            if ($sheet->getCell("A{$row}")->getValue() !== '') {
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '1F4E79']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCE6F1']],
                ]);
            }
        }

        // Detect actual column-header row (first row with "Descripción")
        $headerRow = null;
        for ($r = 1; $r <= min($lastRow, 8); $r++) {
            if ($sheet->getCell("A{$r}")->getValue() === 'Descripción') {
                $headerRow = $r;
                break;
            }
        }
        if ($headerRow) {
            $sheet->getStyle("A{$headerRow}:B{$headerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E75B6']],
            ]);
        }

        // Section headers (rows containing "──")
        for ($r = 1; $r <= $lastRow; $r++) {
            $cellValue = (string) $sheet->getCell("A{$r}")->getValue();
            if (str_contains($cellValue, '──')) {
                $sheet->mergeCells("A{$r}:B{$r}");
                $sheet->getStyle("A{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1F4E79']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
                ]);
            }
        }

        // Balance total row (last row with actual content)
        $sheet->getStyle("A{$lastRow}:B{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1F4E79']],
            ],
        ]);

        // Light zebra for data rows
        for ($r = 1; $r <= $lastRow; $r++) {
            $cellA = (string) $sheet->getCell("A{$r}")->getValue();
            if ($cellA === '' || str_contains($cellA, '──')) {
                continue;
            }
            $sheet->getStyle("A{$r}:B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    protected function buildPeriodLabel(): string
    {
        $start = !empty($this->filters['start_date'])
            ? Carbon::parse(preg_replace('/\s*\(.*?\)/', '', $this->filters['start_date']))->format('d/m/Y')
            : null;
        $end = !empty($this->filters['end_date'])
            ? Carbon::parse(preg_replace('/\s*\(.*?\)/', '', $this->filters['end_date']))->format('d/m/Y')
            : null;

        if ($start && $end) {
            return "{$start} al {$end}";
        }
        if ($start) {
            return "Desde {$start}";
        }
        if ($end) {
            return "Hasta {$end}";
        }

        return 'Todo el historial';
    }

    protected function buildPartnerLabel(): string
    {
        $parts = [];

        if (!empty($this->filters['super_partner_id'])) {
            $sp = SuperPartner::find($this->filters['super_partner_id']);
            if ($sp) {
                $parts[] = 'Super Partner: ' . $sp->nombre;
            }
        }

        if (!empty($this->filters['beneficiario_id']) && $this->filters['beneficiario_id'] !== 'none') {
            $b = Beneficiario::find($this->filters['beneficiario_id']);
            if ($b) {
                $parts[] = 'Partner: ' . $b->nombre;
            }
        }

        return implode(' | ', $parts);
    }

    protected function buildBaseQuery()
    {
        $query = Transaction::query();

        if (!empty($this->filters['beneficiario_id'])) {
            $id = $this->filters['beneficiario_id'];
            if ($id === 'none') {
                $query->whereNull('beneficiario_id');
            } else {
                $query->where('beneficiario_id', $id);
            }
        }

        if (!empty($this->filters['super_partner_id'])) {
            $query->where('super_partner_id', $this->filters['super_partner_id']);
        }

        if (!empty($this->filters['start_date'])) {
            $clean = preg_replace('/\s*\(.*?\)/', '', $this->filters['start_date']);
            $query->where('creation_time', '>=', Carbon::parse($clean)->startOfDay());
        }

        if (!empty($this->filters['end_date'])) {
            $clean = preg_replace('/\s*\(.*?\)/', '', $this->filters['end_date']);
            $query->where('creation_time', '<=', Carbon::parse($clean)->endOfDay());
        }

        // Scope by authenticated user role
        if (auth()->check()) {
            $userType = auth()->user()->user_type;

            if (in_array($userType, ['beneficiario', 'admin_beneficiario'], true)) {
                $beneficiario = $userType === 'beneficiario'
                    ? Beneficiario::where('user_id', auth()->id())->first()
                    : Beneficiario::find(auth()->user()->beneficiario_id);
                if ($beneficiario) {
                    $query->where('beneficiario_id', $beneficiario->id);
                }
            } elseif (in_array($userType, ['super_partner', 'admin_partner'], true)) {
                $superPartner = $userType === 'super_partner'
                    ? SuperPartner::where('user_id', auth()->id())->first()
                    : SuperPartner::find(auth()->user()->super_partner_id);
                if ($superPartner) {
                    $partnerIds = $superPartner->beneficiarios()->pluck('id');
                    $query->where(function ($builder) use ($partnerIds, $superPartner) {
                        $builder->whereIn('beneficiario_id', $partnerIds)
                            ->orWhere('super_partner_id', $superPartner->id);
                    });
                }
            }
        }

        return $query;
    }

    protected function calculateAccountStats(): array
    {
        $baseQuery = $this->buildBaseQuery();

        $allTransactions = (clone $baseQuery)
            ->with(['beneficiario', 'superPartner', 'cliente.beneficiario'])
            ->get();

        $totalTransactions = $allTransactions->count();

        $freeTransactions = $allTransactions->filter(fn (Transaction $t) => $t->isFreeEsim());
        $paidTransactions = $allTransactions->filter(fn (Transaction $t) => !$t->isFreeEsim());

        $freeCount = $freeTransactions->count();
        $freeTotal = $freeTransactions->sum(fn (Transaction $t) => $t->getCommissionAmount());
        $freeAvgRate = $freeCount > 0 ? ($freeTotal / $freeCount) : 0;

        $paidCount = $paidTransactions->count();
        $paidCommissionTotal = $paidTransactions->sum(function (Transaction $t) {
            $partnerCommission = (float) ($t->partner_sale_commission_amount ?? 0);
            $spCommission = (float) ($t->super_partner_sale_commission_amount ?? 0);
            return $partnerCommission + $spCommission;
        });

        return [
            'total_transactions'    => $totalTransactions,
            'free_count'            => $freeCount,
            'free_total'            => $freeTotal,
            'free_avg_rate'         => $freeAvgRate,
            'paid_count'            => $paidCount,
            'paid_commission_total' => $paidCommissionTotal,
        ];
    }
}
