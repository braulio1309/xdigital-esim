<?php

namespace App\Console\Commands;

use App\Services\App\Billing\PartnerInvoiceService;
use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GeneratePartnerInvoices extends Command
{
    protected $signature = 'partner-invoices:generate {--month= : Period to generate in YYYY-MM format} {--backfill : Generate all closed periods with eSIM charges}';

    protected $description = 'Generate monthly invoices for partner and super partner eSIM charges';

    public function handle(PartnerInvoiceService $invoiceService): int
    {
        if ($this->option('backfill') && $this->option('month')) {
            $this->error('Usa --month o --backfill, no ambos.');

            return self::INVALID;
        }

        if ($this->option('backfill')) {
            return $this->backfill($invoiceService);
        }

        $month = $this->option('month') ?: now()->subMonthNoOverflow()->format('Y-m');

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $this->error('El periodo debe tener el formato YYYY-MM.');

            return self::INVALID;
        }

        $periodStart = Carbon::createFromFormat('!Y-m', $month);
        $count = $invoiceService->generateForPeriod($periodStart, $periodStart->copy()->endOfMonth());

        $this->info("Facturas nuevas generadas: {$count} ({$month}).");

        return self::SUCCESS;
    }

    private function backfill(PartnerInvoiceService $invoiceService): int
    {
        $firstCharge = Transaction::query()
            ->where('purchase_amount', 0)
            ->whereNotNull('creation_time')
            ->min('creation_time');

        if (!$firstCharge) {
            $this->info('No hay cargos por eSIM para facturar.');

            return self::SUCCESS;
        }

        $period = Carbon::parse($firstCharge)->startOfMonth();
        $lastClosedPeriod = now()->subMonthNoOverflow()->startOfMonth();
        $generated = 0;

        while ($period->lte($lastClosedPeriod)) {
            $generated += $invoiceService->generateForPeriod($period, $period->copy()->endOfMonth(), true);
            $period->addMonth();
        }

        $this->info("Facturas históricas nuevas o actualizadas: {$generated}.");

        return self::SUCCESS;
    }
}