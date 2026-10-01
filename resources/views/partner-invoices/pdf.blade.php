<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 34px 38px; }
        body { color: #282828; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { width: 100%; margin-bottom: 38px; }
        .header td { vertical-align: top; }
        .logo { width: 190px; height: auto; }
        .title { color: #252525; font-size: 31px; font-weight: bold; margin: 0 0 12px; text-align: right; }
        .meta { color: #777; font-size: 9px; margin-left: auto; }
        .meta td { padding: 2px 0 2px 14px; }
        .meta td:first-child { font-weight: bold; padding-left: 0; }
        .parties { width: 100%; margin-bottom: 34px; }
        .parties td { vertical-align: top; width: 50%; }
        .party-label { font-weight: bold; margin-bottom: 5px; }
        .party-name { font-size: 12px; font-weight: bold; margin-bottom: 4px; }
        .muted { color: #777; line-height: 1.6; }
        .items { border-collapse: collapse; width: 100%; }
        .items th { background: #303030; color: #fff; font-weight: bold; padding: 9px 7px; text-align: left; }
        .items td { border-bottom: 1px solid #e8e8e8; padding: 9px 7px; }
        .items tr:nth-child(even) td { background: #f7f7f7; }
        .number { text-align: right !important; white-space: nowrap; }
        .totals { border-collapse: collapse; margin: 20px 0 0 auto; width: 260px; }
        .totals td { padding: 5px 0; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .rule td { border-top: 1px solid #888; padding-top: 9px; }
        .totals .grand td { font-size: 12px; font-weight: bold; }
        .totals .due td { border-top: 1px solid #888; font-size: 11px; font-weight: bold; padding-top: 9px; }
        .footer { border-top: 1px solid #ddd; color: #777; font-size: 8px; margin-top: 55px; padding-top: 9px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                @if($logoData)
                    <img class="logo" src="{{ $logoData }}" alt="Xcertus">
                @else
                    <strong>Xcertus</strong>
                @endif
            </td>
            <td>
                <h1 class="title">FACTURA</h1>
                <table class="meta">
                    <tr><td>Número</td><td>{{ $invoice->invoice_number }}</td></tr>
                    <tr><td>Fecha</td><td>{{ $invoice->issued_at->format('d/m/Y') }}</td></tr>
                    <tr><td>Periodo</td><td>{{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}</td></tr>
                    <tr><td>Impuesto</td><td>No incluido</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <div class="party-label">De</div>
                <div class="party-name">Xcertus</div>
                <div class="muted">{{ config('mail.from.address') }}</div>
            </td>
            <td>
                <div class="party-label">Para</div>
                <div class="party-name">{{ $recipient->nombre }}</div>
                <div class="muted">{{ optional($recipient->user)->email }}</div>
                <div class="muted">{{ $invoice->owner_type === 'super_partner' ? 'Super Partner' : 'Partner' }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Plan</th>
                <th class="number">Cantidad</th>
                <th class="number">Precio unitario</th>
                <th class="number">Impuesto</th>
                <th class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{ $item['plan_name'] }}
                        @if($item['data_amount'])
                            · {{ $item['data_amount'] }} GB
                        @endif
                        @if($item['duration_days'])
                            · {{ $item['duration_days'] }} días
                        @endif
                    </td>
                    <td class="number">{{ number_format($item['quantity']) }}</td>
                    <td class="number">{{ $invoice->currency }} {{ number_format((float) $item['unit_price'], 2) }}</td>
                    <td class="number">N/A</td>
                    <td class="number">{{ $invoice->currency }} {{ number_format((float) $item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr class="rule"><td>Subtotal de cargos</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->amount, 2) }}</td></tr>
        <tr><td>Impuestos</td><td>{{ $invoice->currency }} 0.00</td></tr>
        <tr><td>Comisiones aplicadas</td><td>- {{ $invoice->currency }} {{ number_format((float) $invoice->applied_commissions, 2) }}</td></tr>
        <tr class="grand"><td>Total neto</td><td>{{ $invoice->currency }} {{ number_format(max(0, (float) $invoice->amount - (float) $invoice->applied_commissions), 2) }}</td></tr>
        <tr><td>Pagado</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->paid_amount, 2) }}</td></tr>
        <tr class="due"><td>Saldo pendiente</td><td>{{ $invoice->currency }} {{ number_format((float) $invoice->due_amount, 2) }}</td></tr>
    </table>

    <div class="footer">Factura emitida por Xcertus. Los importes corresponden a cargos por eSIM del periodo indicado.</div>
</body>
</html>