<?php

namespace App\Exports\App\Beneficiario;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiarioRegisteredClientsExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    private $clients;

    public function __construct(Collection $clients)
    {
        $this->clients = $clients;
    }

    public function array(): array
    {
        return $this->clients
            ->map(function ($client) {
                return [
                    $this->safeCell($client->nombre),
                    $this->safeCell($client->apellido),
                    $this->safeCell($client->email),
                    $this->safeCell($client->identificador),
                    optional($client->created_at)->format('Y-m-d H:i:s'),
                    $this->safeCell($client->esim_status),
                    $this->safeCell($client->esim_plan),
                    (int) $client->companions_count,
                    $this->safeCell($client->companions),
                ];
            })
            ->all();
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Correo electrónico',
            'Identificador',
            'Fecha de registro',
            'Estado de eSIM',
            'Plan de eSIM',
            'Cantidad de acompañantes',
            'Acompañantes',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function safeCell(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) === 1 ? "'" . $value : $value;
    }
}
