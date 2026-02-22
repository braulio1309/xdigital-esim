@extends('layouts.app')

@section('title', 'Panel de Beneficiario')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Panel de Beneficiario</h3>
                    <div>
                        <p class="text-muted mb-2">Bienvenido, {{ $beneficiario->nombre }}</p>
                        
                        <div class="input-group mb-3" style="max-width: 500px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary text-white">Tu Link:</span>
                            </div>
                            <input type="text" class="form-control bg-white" id="referralLink" 
                                   value="{{ $beneficiario->referral_link }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" onclick="copyLink()">
                                    <i class="mdi mdi-content-copy"></i> Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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

        <!-- Plan Commissions Section -->
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title mb-0">Comisiones por Plan</h4>
                                <p class="card-description mb-0">Porcentaje de comisión configurado para cada plan</p>
                            </div>
                            <i class="mdi mdi-currency-usd text-success icon-lg"></i>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Comisión</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['3' => '3 GB', '5' => '5 GB', '10' => '10 GB'] as $capacity => $label)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold">{{ $label }}</span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-success">
                                                    {{ number_format($plan_commissions[$capacity] ?? 0, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if(($plan_commissions[$capacity] ?? 0) > 0)
                                                    <span class="badge badge-success">Configurado</span>
                                                @else
                                                    <span class="badge badge-secondary">Sin comisión</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            Contacta al administrador para modificar tus comisiones.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Free eSIM Clients Section -->
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title mb-0">eSIMs Gratuitas Pendientes</h4>
                                <p class="card-description mb-0">Clientes con permiso de eSIM gratuita aún no activada</p>
                            </div>
                            <span class="badge badge-pill badge-primary font-weight-bold" style="font-size: 1rem;">
                                {{ $free_esim_clients->count() }}
                            </span>
                        </div>

                        @if($free_esim_clients->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Email</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($free_esim_clients as $cliente)
                                            <tr>
                                                <td>{{ $cliente->nombre }}</td>
                                                <td>{{ $cliente->apellido }}</td>
                                                <td>{{ $cliente->email }}</td>
                                                <td>
                                                    <span class="badge badge-warning">Pendiente</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success mt-3 mb-0" role="alert">
                                <p class="mb-0">
                                    <i class="mdi mdi-check-circle mr-1"></i>
                                    No hay eSIMs gratuitas pendientes de activar.
                                </p>
                            </div>
                        @endif
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
    .input-group-text {
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyLink() {
        var copyText = document.getElementById("referralLink");
        var textToCopy = copyText.value;
        
        // Use modern Clipboard API if available
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy).then(function() {
                alert("¡Enlace copiado al portapapeles!");
            }).catch(function() {
                // Fallback to older method
                fallbackCopy(copyText);
            });
        } else {
            // Fallback for older browsers
            fallbackCopy(copyText);
        }
    }
    
    function fallbackCopy(element) {
        element.select();
        element.setSelectionRange(0, 99999); // Para móviles
        try {
            document.execCommand("copy");
            alert("¡Enlace copiado al portapapeles!");
        } catch (err) {
            alert("Error al copiar el enlace");
        }
    }
</script>
@endpush