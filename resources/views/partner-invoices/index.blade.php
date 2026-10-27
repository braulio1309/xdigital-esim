@extends('layouts.app')

@section('title', 'Facturación')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="page-title mb-1">Facturación</h3>
                        <p class="text-muted mb-0">Histórico mensual de facturas y estatus de pago.</p>
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
                                                    <a class="btn btn-sm btn-success text-nowrap"
                                                       href="{{ route('partner-invoices.monthly-invoice', $monthlyTotal->period_start->format('Y-m')) }}"
                                                       title="Descargar factura mensual consolidada"
                                                       aria-label="Descargar factura mensual consolidada">
                                                        <i class="mdi mdi-download mr-1"></i>Descargar factura
                                                    </a>
                                                    @if((float) $monthlyTotal->earned_commissions > 0)
                                                        <a class="btn btn-sm btn-outline-primary text-nowrap"
                                                           href="{{ route('partner-invoices.monthly-commission-report', $monthlyTotal->period_start->format('Y-m')) }}"
                                                           title="Descargar desglose mensual de comisiones"
                                                           aria-label="Descargar desglose mensual de comisiones">
                                                            <i class="mdi mdi-file-document-outline mr-1"></i>Desglose
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

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        @if($owner['type'] === 'admin')
            <div class="row mt-3">
                <div class="col-12">
                    <form method="GET" action="{{ route('partner-invoices.index') }}" class="card card-body">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-4 mb-md-0">
                                <label for="invoice-month">Filtrar por mes</label>
                                <select id="invoice-month" name="month" class="form-control">
                                    <option value="">Todos los meses</option>
                                    @foreach($months as $month)
                                        <option value="{{ $month }}" {{ request('month') === $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromFormat('!Y-m', $month)->format('m/Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-md-0">
                                <label for="invoice-super-partner">Super Partner</label>
                                <select id="invoice-super-partner" name="super_partner_id" class="form-control">
                                    <option value="">Todos los super partners</option>
                                    @foreach($superPartners as $superPartner)
                                        <option value="{{ $superPartner->id }}" {{ (string) request('super_partner_id') === (string) $superPartner->id ? 'selected' : '' }}>
                                            {{ $superPartner->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3 mb-md-0">
                                <label for="invoice-type">Tipo de factura</label>
                                <select id="invoice-type" name="invoice_type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="super_partner" {{ request('invoice_type') === 'super_partner' ? 'selected' : '' }}>Super Partners</option>
                                    <option value="partner" {{ request('invoice_type') === 'partner' ? 'selected' : '' }}>Partners</option>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                @if(request()->filled('month') || request()->filled('super_partner_id') || request()->filled('invoice_type'))
                                    <a href="{{ route('partner-invoices.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($hasFilters)
            <div class="row">
            @if(!$superPartnerInvoiceTotals->isEmpty())
                <div class="col-12 mt-3">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">Facturas mensuales de super partners</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Periodo</th>
                                            <th>Super Partner</th>
                                            <th class="text-right">Facturas</th>
                                            <th class="text-right">Total neto</th>
                                            <th class="text-right">Comisiones</th>
                                            <th class="text-right">Pendiente</th>
                                            <th class="text-right">Pagadas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($superPartnerInvoiceTotals as $monthlyTotal)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($monthlyTotal->period_start)->format('m/Y') }}</td>
                                                <td>{{ $monthlyTotal->super_partner_name ?: 'Cuenta no disponible' }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->invoice_count) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->net_amount, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->earned_commissions, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->due_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->paid_count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!$adminPartnerInvoiceTotals->isEmpty())
                <div class="col-12 mt-3">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Total mensual de partners por super partner</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Periodo</th>
                                            <th>Super Partner</th>
                                            <th class="text-right">Partners</th>
                                            <th class="text-right">Facturas</th>
                                            <th class="text-right">Total neto</th>
                                            <th class="text-right">Comisiones</th>
                                            <th class="text-right">Pendiente</th>
                                            <th class="text-right">Facturas pagadas</th>
                                            <th class="text-center">Documentos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($adminPartnerInvoiceTotals as $monthlyTotal)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($monthlyTotal->period_start)->format('m/Y') }}</td>
                                                <td>{{ $monthlyTotal->super_partner_name ?: 'Sin super partner' }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->partner_count) }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->invoice_count) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->net_amount, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->earned_commissions, 2) }}</td>
                                                <td class="text-right">USD {{ number_format((float) $monthlyTotal->due_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($monthlyTotal->paid_count) }}</td>
                                                <td class="text-center">
                                                    @if($monthlyTotal->super_partner_id)
                                                        <a class="btn btn-sm btn-success text-nowrap"
                                                           href="{{ route('partner-invoices.monthly-invoice', ['period' => \Carbon\Carbon::parse($monthlyTotal->period_start)->format('Y-m'), 'super_partner_id' => $monthlyTotal->super_partner_id]) }}"
                                                           title="Descargar factura mensual consolidada"
                                                           aria-label="Descargar factura mensual consolidada">
                                                            <i class="mdi mdi-download mr-1"></i>Factura consolidada
                                                        </a>
                                                        @if((float) $monthlyTotal->earned_commissions > 0)
                                                            <a class="btn btn-sm btn-outline-primary text-nowrap"
                                                               href="{{ route('partner-invoices.monthly-commission-report', ['period' => \Carbon\Carbon::parse($monthlyTotal->period_start)->format('Y-m'), 'super_partner_id' => $monthlyTotal->super_partner_id]) }}"
                                                               title="Descargar desglose mensual de comisiones"
                                                               aria-label="Descargar desglose mensual de comisiones">
                                                                <i class="mdi mdi-file-document-outline mr-1"></i>Desglose
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Sin super partner</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
            @else
                <div class="row mt-3">
                    <div class="col-12 text-center text-muted py-4">
                        Selecciona al menos un filtro para consultar la información de facturación.
                    </div>
                </div>
            @endif
        @endif

        @if($owner['type'] !== 'admin' || $hasFilters)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Periodo</th>
                                        @if($owner['type'] === 'admin')
                                            <th>Tipo</th>
                                            <th>Super Partner / Partner</th>
                                        @elseif($owner['type'] === 'super_partner')
                                            <th>Partner / cuenta</th>
                                        @endif
                                        <th>Número de factura</th>
                                        <th class="text-right">eSIMs</th>
                                        <th class="text-right">Total neto</th>
                                        <th class="text-right">Comisiones</th>
                                        <th class="text-right">Pendiente</th>
                                        <th class="text-center">Estatus</th>
                                        <th class="text-center">Documentos</th>
                                        @if($owner['type'] === 'admin')
                                            <th class="text-center">Acción</th>
                                        @endif
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
                                            @if($owner['type'] === 'admin')
                                                <td>{{ $invoice->owner_type === 'super_partner' ? 'Super Partner' : 'Partner' }}</td>
                                                <td>{{ $invoice->recipient_name ?: 'Cuenta no disponible' }}</td>
                                            @endif
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
                                                <span class="badge {{ $invoice->is_paid ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $invoice->is_paid ? 'Pagada' : 'Pendiente' }}
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
                                            @if($owner['type'] === 'admin')
                                                <td class="text-center">
                                                    <form method="POST" action="{{ route('partner-invoices.payment-status', $invoice) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        @foreach(['month', 'super_partner_id', 'invoice_type'] as $filter)
                                                            @if(request()->filled($filter))
                                                                <input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">
                                                            @endif
                                                        @endforeach
                                                        <input type="hidden" name="is_paid" value="{{ $invoice->is_paid ? 0 : 1 }}">
                                                        <button type="submit" class="btn btn-sm {{ $invoice->is_paid ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                            {{ $invoice->is_paid ? 'Marcar pendiente' : 'Marcar pagada' }}
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $owner['type'] === 'admin' ? 11 : ($owner['type'] === 'super_partner' ? 9 : 8) }}" class="text-center text-muted py-5">
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
                @endif
            </div>
        </div>
    </div>
@endsection