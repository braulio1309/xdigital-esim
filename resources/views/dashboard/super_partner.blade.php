@extends('layouts.app')

@section('title', 'Panel de Super Partner')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Panel de Super Partner</h3>
                    <p class="text-muted mb-2">Bienvenido, {{ $superPartner->nombre }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Mis Partners</h4>
                            <i class="mdi mdi-account-multiple text-primary icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_partners }}</h2>
                        <p class="text-muted mb-0">Partners creados</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Mis Clientes</h4>
                            <i class="mdi mdi-account text-success icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_clientes }}</h2>
                        <p class="text-muted mb-0">Clientes totales</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Transacciones</h4>
                            <i class="mdi mdi-swap-horizontal text-info icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_transactions }}</h2>
                        <p class="text-muted mb-0">Transacciones totales</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">eSIMs Gratuitas</h4>
                            <i class="mdi mdi-sim text-warning icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_free_esims }}</h2>
                        <p class="text-muted mb-0">eSIMs gratuitas activadas</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Accesos Rápidos</h4>
                        <div class="list-group list-group-flush">
                            <a href="{{ url('/admin/beneficiarios') }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-account-multiple mr-2 text-primary"></i>
                                Ver mis Partners
                            </a>
                            <a href="{{ url('/admin/clientes') }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-account mr-2 text-success"></i>
                                Ver mis Clientes
                            </a>
                            <a href="{{ url('/admin/transactions') }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-swap-horizontal mr-2 text-info"></i>
                                Ver Transacciones
                            </a>
                            <a href="{{ url('/report-view') }}" class="list-group-item list-group-item-action">
                                <i class="mdi mdi-file-chart mr-2 text-warning"></i>
                                Ver Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
