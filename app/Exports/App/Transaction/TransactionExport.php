<?php

namespace App\Exports\App\Transaction;

use App\Models\App\Transaction\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Transaction::with('cliente.beneficiario.planMargins');

        // Filter by beneficiario
        if (!empty($this->filters['beneficiario_id'])) {
            $beneficiarioId = $this->filters['beneficiario_id'];
            $query->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });
        }

        // Filter by type (free / paid plans)
        if (!empty($this->filters['type'])) {
            if ($this->filters['type'] === 'free') {
                $query->where('purchase_amount', 0);
            } elseif ($this->filters['type'] === 'paid') {
                $query->where('purchase_amount', '>', 0);
            }
        }

        // Filter by payment status
        if (isset($this->filters['payment_status']) && $this->filters['payment_status'] !== null && $this->filters['payment_status'] !== '') {
            $ps = $this->filters['payment_status'];
            if ($ps === 'paid' || $ps === '1' || $ps === 1 || $ps === true) {
                $query->where('is_paid', true);
            } elseif ($ps === 'unpaid' || $ps === '0' || $ps === 0 || $ps === false) {
                $query->where('is_paid', false);
            }
        }

        // Date range on creation_time
        if (!empty($this->filters['start_date'])) {
            $query->where('creation_time', '>=', Carbon::parse($this->filters['start_date'])->startOfDay());
        }
        if (!empty($this->filters['end_date'])) {
            $query->where('creation_time', '<=', Carbon::parse($this->filters['end_date'])->endOfDay());
        }

        // Restrict to own beneficiario if the authenticated user is a beneficiario
        if (auth()->check() && auth()->user()->user_type === 'beneficiario') {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::where('user_id', auth()->id())->first();
            if ($beneficiario) {
                $query->whereHas('cliente', function ($q) use ($beneficiario) {
                    $q->where('beneficiario_id', $beneficiario->id);
                });
            }
        }

        return $query->latest('creation_time');
    }

    public function headings(): array
    {
        return [
            'ID Transacción',
            'Fecha',
            'Plan',
            'Datos (GB)',
            'Duración (días)',
            'Monto de Compra',
            'Comisión',
            'Beneficiario',
            'Cliente',
            'Estado eSIM',
            'Estado de Pago',
            'Fecha de Pago',
        ];
    }

    public function map($transaction): array
    {
        $commission = number_format((float) $transaction->getCommissionAmount(), 2);
        $beneficiario = $transaction->cliente->beneficiario ?? null;
        $cliente = $transaction->cliente;
        $isPaid = $transaction->is_paid;

        return [
            $transaction->transaction_id ?? '',
            $transaction->creation_time ? $transaction->creation_time->format('Y-m-d H:i:s') : '',
            $transaction->plan_name ?? '',
            $transaction->data_amount ?? '',
            $transaction->duration_days ?? '',
            ($transaction->purchase_amount == 0) ? 'Gratis' : ('$' . number_format((float) $transaction->purchase_amount, 2)),
            '$' . $commission,
            $beneficiario ? $beneficiario->nombre : '',
            $cliente ? trim($cliente->nombre . ' ' . $cliente->apellido) : '',
            $transaction->status ?? '',
            $isPaid ? 'Pagado' : 'Sin Pagar',
            ($isPaid && $transaction->paid_at) ? $transaction->paid_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
