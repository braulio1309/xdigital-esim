<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comisiones de partners · {{ $periodStart->format('m/Y') }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #282828; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        .header { width: 100%; margin-bottom: 24px; }
        .header td { vertical-align: top; }
        .logo { width: 160px; height: auto; }
        .title { color: #252525; font-size: 22px; font-weight: bold; margin: 0 0 8px; text-align: right; }
        .meta { color: #666; font-size: 9px; margin-left: auto; }
        .meta td { padding: 2px 0 2px 12px; }
        .meta td:first-child { font-weight: bold; padding-left: 0; }
        .items { border-collapse: collapse; width: 100%; }
        .items th { background: #303030; color: #fff; padding: 7px 6px; text-align: left; }
        .items td { border-bottom: 1px solid #e8e8e8; padding: 7px 6px; }
        .items tr:nth-child(even) td { background: #f7f7f7; }
        .number { text-align: right !important; white-space: nowrap; }
        .totals { border-collapse: collapse; margin: 18px 0 0 auto; width: 280px; }
        .totals td { padding: 5px 0; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .grand td { border-top: 1px solid #888; font-size: 11px; font-weight: bold; padding-top: 8px; }
        .footer { border-top: 1px solid #ddd; color: #777; font-size: 8px; margin-top: 32px; padding-top: 8px; }
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
                <h1 class="title">DESGLOSE MENSUAL DE COMISIONES</h1>
                <table class="meta">
                    <tr><td>Super Partner</td><td>{{ $superPartner->nombre }}</td></tr>
                    <tr><td>Periodo</td><td>{{ $periodStart->format('m/Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Partner</th>
                <th>Factura / nota</th>
                <th>Plan</th>
                <th class="number">Ventas</th>
                <th class="number">Comisión unitaria</th>
                <th class="number">Comisión total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commissionRows as $row)
                <tr>
                    <td>{{ $row['partner_name'] }}</td>
                    <td>{{ $row['invoice_number'] }}<br>{{ $row['note_number'] }}</td>
                    <td>
                        {{ $row['plan_name'] }}
                        @if($row['data_amount'])
                            · {{ $row['data_amount'] }} GB
                        @endif
                        @if($row['duration_days'])
                            · {{ $row['duration_days'] }} días
                        @endif
                    </td>
                    <td class="number">{{ number_format($row['quantity']) }}</td>
                    <td class="number">USD {{ number_format($row['unit_price'], 2) }}</td>
                    <td class="number">USD {{ number_format($row['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Comisiones a favor</td><td>USD {{ number_format($earnedCommissions, 2) }}</td></tr>
        <tr><td>Aplicadas a facturas</td><td>USD {{ number_format($appliedCommissions, 2) }}</td></tr>
        <tr class="grand"><td>Saldo a favor</td><td>USD {{ number_format($balanceCommissions, 2) }}</td></tr>
    </table>

    <div class="footer">Desglose mensual de comisiones a favor de los partners asociados al super partner.</div>
</body>
</html>
