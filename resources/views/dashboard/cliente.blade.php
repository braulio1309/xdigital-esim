@extends('layouts.app')

@section('title', 'Panel de Cliente')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Panel de Cliente</h3>
                    <p class="text-muted">Bienvenido, {{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                </div>
            </div>
        </div>

        <!-- Active Plan Section -->
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Plan Activo</h4>
                        <p class="card-description">Detalles de tu plan actual</p>
                        
                        @if($active_plan)
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <h6 class="text-muted mb-2">ID de Transacción</h6>
                                        <p class="font-weight-bold">{{ $active_plan->transaction_id }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <h6 class="text-muted mb-2">Estado</h6>
                                        <span class="badge badge-success">{{ ucfirst($active_plan->status) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <h6 class="text-muted mb-2">ICCID</h6>
                                        <p class="font-weight-bold">{{ $active_plan->iccid ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item">
                                        <h6 class="text-muted mb-2">Fecha de Creación</h6>
                                        <p>{{ $active_plan->creation_time ? $active_plan->creation_time->format('d/m/Y H:i') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($active_plan->esim_qr)
                                <div class="row mt-4">
                                    <div class="col-md-12 text-center">
                                        <h6 class="text-muted mb-3">Código QR eSIM</h6>
                                        <div class="qr-code-container">
                                            <img src="{{ $active_plan->esim_qr }}" alt="eSIM QR Code" class="img-fluid" style="max-width: 300px;">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning" role="alert">
                                <h5 class="alert-heading">Sin Plan Activo</h5>
                                <p>Actualmente no tienes un plan activo. Contacta con tu administrador para activar un plan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Historial de Transacciones</h4>
                        <p class="card-description">Todas tus transacciones registradas</p>
                        
                        @if($transactions->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Transacción</th>
                                            <th>Estado</th>
                                            <th>ICCID</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_id }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->iccid ?? 'N/A' }}</td>
                                                <td>{{ $transaction->creation_time ? $transaction->creation_time->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td>
                                                    @if($transaction->esim_qr)
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                data-toggle="modal" 
                                                                data-target="#qrModal{{ $transaction->id }}">
                                                            Ver QR
                                                        </button>
                                                        
                                                        <!-- QR Modal -->
                                                        <div class="modal fade" id="qrModal{{ $transaction->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Código QR - {{ $transaction->transaction_id }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="{{ $transaction->esim_qr }}" alt="QR Code" class="img-fluid">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Sin QR</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mt-3" role="alert">
                                <p class="mb-0">No tienes transacciones registradas aún.</p>
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
    .metric-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .qr-code-container {
        padding: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        display: inline-block;
    }
</style>
@endpush
