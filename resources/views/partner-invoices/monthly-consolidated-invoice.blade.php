<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura consolidada {{ $periodStart->format('m/Y') }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #282828; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        .header { width: 100%; margin-bottom: 24px; }
        .header td { vertical-align: top; }
        .logo { width: 160px; height: auto; }
        .title { color: #252525; font-size: 24px; font-weight: bold; margin: 0 0 8px; text-align: right; }
        .meta { color: #666; font-size: 9px; margin-left: auto; }
        .meta td { padding: 2px 0 2px 12px; }
        .meta td:first-child { font-weight: bold; padding-left: 0; }
        .parties { width: 100%; margin-bottom: 22px; }
        .parties td { vertical-align: top; width: 50%; }
        .items { border-collapse: collapse; width: 100%; }
        .items th { background: #303030; color: #fff; padding: 7px 6px; text-align: left; }
        .items td { border-bottom: 1px solid #e8e8e8; padding: 7px 6px; }
        .items tr:nth-child(even) td { background: #f7f7f7; }
        .section-title { font-size: 12px; margin: 20px 0 8px; }
        .number { text-align: right !important; white-space: nowrap; }
        .totals { border-collapse: collapse; margin: 18px 0 0 auto; width: 290px; }
        .totals td { padding: 5px 0; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .grand td { border-top: 1px solid #888; font-size: 12px; font-weight: bold; padding-top: 8px; }
        .payment { border: 1px solid #ddd; margin-top: 22px; padding: 10px; width: 55%; }
        .payment-title { font-size: 11px; font-weight: bold; margin-bottom: 6px; }
        .payment td { padding: 3px 14px 3px 0; }
        .footer { border-top: 1px solid #ddd; color: #777; font-size: 8px; margin-top: 28px; padding-top: 8px; }
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
                <h1 class="title">FACTURA CONSOLIDADA</h1>
                <table class="meta">
                    <tr><td>Periodo</td><td>{{ $periodStart->format('m/Y') }}</td></tr>
                    <tr><td>Fecha</td><td>{{ now()->format('d/m/Y') }}</td></tr>
                    <tr><td>Documentos relacionados</td><td>{{ $invoiceNumbers->implode(', ') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <strong>Emisor</strong><br>
                Xcertus<br>
                {{ config('mail.from.address') }}
            </td>
            <td>
                <strong>Facturado a</strong><br>
                {{ $superPartner->nombre }}<br>
                Super Partner
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Partner</th>
                <th>Factura</th>
                <th>Plan</th>
                <th class="number">Cantidad</th>
                <th class="number">Precio unitario</th>
                <th class="number">Descuento</th>
                <th class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['partner_name'] }}</td>
                    <td>{{ $item['invoice_number'] }}</td>
                    <td>
                        {{ $item['plan_name'] }}
                        @if($item['data_amount'])
                            · {{ $item['data_amount'] }} GB
                        @endif
                        @if($item['duration_days'])
                            · {{ $item['duration_days'] }} días
                        @endif
                        @if(!empty($item['country_code']))
                            · {{ $item['country_code'] }}
                        @endif
                    </td>
                    <td class="number">{{ number_format($item['quantity']) }}</td>
                    <td class="number">{{ $currency }} {{ number_format((float) $item['unit_price'], 2) }}</td>
                    <td class="number">
                        {{ !empty($item['discount_percentage']) ? number_format((float) $item['discount_percentage'], 0) . '%' : '-' }}
                    </td>
                    <td class="number">{{ $currency }} {{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($commissionRows->isNotEmpty())
        <h2 class="section-title">Comisiones a favor por compras de clientes</h2>
        <table class="items">
            <thead>
                <tr>
                    <th>Beneficiario</th>
                    <th>Factura / nota</th>
                    <th>Plan</th>
                    <th>País y comisión</th>
                    <th class="number">Ventas</th>
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
                        </td>
                        <td>
                            {{ $row['country_code'] ?? 'N/D' }}
                            @if(!empty($row['commission_percentage']))
                                · {{ number_format((float) $row['commission_percentage'], 0) }}% {{ $row['commission_region'] ?? '' }}
                            @endif
                        </td>
                        <td class="number">{{ number_format($row['quantity']) }}</td>
                        <td class="number">{{ $currency }} {{ number_format($row['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php($discountTotal = $items->sum('discount_total'))
    <table class="totals">
        <tr><td>Subtotal de cargos</td><td>{{ $currency }} {{ number_format($amount + $discountTotal, 2) }}</td></tr>
        <tr><td>Descuentos aplicados</td><td>- {{ $currency }} {{ number_format($discountTotal, 2) }}</td></tr>
        <tr><td>Comisiones aplicadas</td><td>- {{ $currency }} {{ number_format($appliedCommissions, 2) }}</td></tr>
        <tr class="grand"><td>Total consolidado</td><td>{{ $currency }} {{ number_format(max(0, $amount - $appliedCommissions), 2) }}</td></tr>
        <tr><td>Saldo pendiente</td><td>{{ $currency }} {{ number_format($dueAmount, 2) }}</td></tr>
        <tr><td>Saldo a favor</td><td>{{ $currency }} {{ number_format($earnedCommissions, 2) }}</td></tr>
    </table>

    <div class="payment">
        <div class="payment-title">Datos para realizar el pago</div>
        <table>
            <tr><td><strong>Banco</strong></td><td>{{ $paymentDetails['bank_name'] ?? '' }}</td></tr>
            <tr><td><strong>Número de cuenta</strong></td><td>{{ $paymentDetails['account_number'] ?? '' }}</td></tr>
            <tr><td><strong>Routing number</strong></td><td>{{ $paymentDetails['routing_number'] ?? '' }}</td></tr>
        </table>
    </div>

    <div class="footer">Factura consolidada de cargos y comisiones a favor relacionados con los partners y el super partner durante el periodo indicado. Impuestos no incluidos.</div>
</body>
</html>
