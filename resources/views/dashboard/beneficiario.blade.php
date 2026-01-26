@extends('layouts.app')

@section('title', 'Panel de Beneficiario')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Panel de Beneficiario</h3>
                    <p class="text-muted">Bienvenido, {{ $beneficiario->nombre }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Commission Percentage Card -->
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Porcentaje de Comisión</h4>
                            <i class="mdi mdi-percent text-primary icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ number_format($commission_percentage, 2) }}%</h2>
                        <p class="text-muted mb-0">Tasa actual de comisión</p>
                    </div>
                </div>
            </div>

            <!-- Total Earnings Card -->
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Ganancias Totales</h4>
                            <i class="mdi mdi-currency-usd text-success icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">${{ number_format($total_earnings, 2) }}</h2>
                        <p class="text-muted mb-0">Comisiones acumuladas</p>
                    </div>
                </div>
            </div>

            <!-- Total Sales Card -->
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Ventas Totales</h4>
                            <i class="mdi mdi-chart-line text-info icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_sales }}</h2>
                        <p class="text-muted mb-0">Transacciones realizadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Resumen Financiero</h4>
                        <p class="card-description">Estadísticas de desempeño</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    <h5 class="alert-heading">Estado Actual</h5>
                                    <p>Actualmente, tu comisión está en <strong>{{ number_format($commission_percentage, 2) }}%</strong> 
                                       y has acumulado <strong>${{ number_format($total_earnings, 2) }}</strong> en ganancias.</p>
                                    <hr>
                                    <p class="mb-0">Continúa promoviendo para aumentar tus comisiones y ganancias.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="metric-card">
                                    <h6 class="text-muted">Descripción</h6>
                                    <p>{{ $beneficiario->descripcion ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-card">
                                    <h6 class="text-muted">Información de Contacto</h6>
                                    <p>{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .icon-lg {
        font-size: 2.5rem;
    }
    .metric-card {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
    }
</style>
@endpush
