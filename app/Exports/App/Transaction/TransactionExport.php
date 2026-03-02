<?php

namespace App\Exports\App\Transaction;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new TransactionDataSheet($this->filters),
            new FreeEsimDebtSummarySheet($this->filters),
        ];
    }
}
