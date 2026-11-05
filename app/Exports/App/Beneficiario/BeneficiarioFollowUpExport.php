<?php

namespace App\Exports\App\Beneficiario;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiarioFollowUpExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    private $partners;

    public function __construct(Collection $partners)
    {
        $this->partners = $partners;
    }

    public function array(): array
    {
        return $this->partners
            ->map(function ($partner) {
                $name = (string) $partner->nombre;

                if (preg_match('/^[=+\-@]/', $name) === 1) {
                    $name = "'" . $name;
                }

                return [
                    $name,
                    (int) $partner->registered_clients_count,
                ];
            })
            ->all();
    }

    public function headings(): array
    {
        return ['Partner', 'Clientes registrados'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
