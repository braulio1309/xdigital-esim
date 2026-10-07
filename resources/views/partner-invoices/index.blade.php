@extends('layouts.app')

@section('title', 'Facturación')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="page-title mb-1">Facturación</h3>
                        <p class="text-muted mb-0">Histórico mensual de cargos por eSIM.</p>
                    </div>
                </div>
            </div>
        </div>

        @if($owner['type'] === 'super_partner')
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Total mensual consolidado de tus partners</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Periodo</th>
                                            <th class="text-right">Partners</th>
                                            <th class="text-right">Total neto</th>
                                            <th class="text-right">Comisiones</th>
                                            <th class="text-right">Pendiente</th>
                                            <th class="text-center">Documentos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monthlyPartnerTotals as $monthlyTotal)
                                            <tr class="table-primary font-weight-bold">
                                                <td>{{ $monthlyTotal->period_start->format('m/Y') }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->partner_count) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->net_amount, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->earned_commissions, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->due_amount, 2) }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-sm btn-outline-light"
                                                       href="{{ route('partner-invoices.monthly-invoice', $monthlyTotal->period_start->format('Y-m')) }}"
                                                       title="Descargar factura mensual consolidada"
                                                       aria-label="Descargar factura mensual consolidada">
                                                        <i class="mdi mdi-download"></i>
                                                    </a>
                                                    @if((float) $monthlyTotal->earned_commissions > 0)
                                                        <a class="btn btn-sm btn-outline-primary"
                                                           href="{{ route('partner-invoices.monthly-commission-report', $monthlyTotal->period_start->format('Y-m')) }}"
                                                           title="Descargar desglose mensual de comisiones"
                                                           aria-label="Descargar desglose mensual de comisiones">
                                                            <i class="mdi mdi-file-document-outline"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    No hay facturas mensuales de partners.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Periodo</th>
                                        @if($owner['type'] === 'super_partner')
                                            <th>Partner / cuenta</th>
                                        @endif
                                        <th>Número de factura</th>
                                        <th class="text-right">eSIMs</th>
                                        <th class="text-right">Total neto</th>
                                        <th class="text-right">Comisiones</th>
                                        <th class="text-right">Pendiente</th>
                                        <th class="text-center">Documentos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <strong>{{ $invoice->period_start->format('m/Y') }}</strong>
                                                <small class="d-block text-muted">
                                                    {{ $invoice->period_start->format('d/m/Y') }} - {{ $invoice->period_end->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            @if($owner['type'] === 'super_partner')
                                                <td>{{ $invoice->recipient_name ?: 'Cuenta no disponible' }}</td>
                                            @endif
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td class="text-right">{{ number_format($invoice->transactions_count) }}</td>
                                            <td class="text-right">{{ $invoice->currency }} {{ number_format(max(0, (float) $invoice->amount - (float) $invoice->applied_commissions), 2) }}</td>
                                            <td class="text-right">{{ $invoice->currency }} {{ number_format((float) $invoice->earned_commissions, 2) }}</td>
                                            <td class="text-right">
                                                <span class="badge {{ (float) $invoice->due_amount > 0 ? 'badge-warning' : 'badge-success' }}">
                                                    {{ $invoice->currency }} {{ number_format((float) $invoice->due_amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-outline-primary"
                                                   href="{{ route('partner-invoices.download', $invoice) }}"
                                                   title="Descargar factura PDF"
                                                   aria-label="Descargar factura PDF">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                                @if($invoice->commissionNote)
                                                    <a class="btn btn-sm btn-outline-secondary"
                                                       href="{{ route('partner-invoices.commission-note', $invoice) }}"
                                                       title="Descargar nota de comisiones PDF"
                                                       aria-label="Descargar nota de comisiones PDF">
                                                        <i class="mdi mdi-file-document-outline"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $owner['type'] === 'super_partner' ? 8 : 7 }}" class="text-center text-muted py-5">
                                                No hay facturas generadas todavía.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($invoices->hasPages())
                            <div class="px-3 py-2">
                                {{ $invoices->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection