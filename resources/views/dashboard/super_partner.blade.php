@extends('layouts.app')

@section('title', 'Panel de Super Partner')

@section('contents')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Panel de Super Partner</h3>
                    <div>
                        <p class="text-muted mb-2">Bienvenido, {{ $superPartner->nombre }}</p>
                        <div class="input-group mb-3" style="max-width: 560px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary text-white">Tu Link:</span>
                            </div>
                            <input type="text" class="form-control bg-white" id="superPartnerReferralLink"
                                   value="{{ $superPartner->referral_link }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" onclick="copySuperPartnerLink()">
                                    <i class="mdi mdi-content-copy"></i> Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Este panel solo muestra información de tu cuenta y de los partners asociados a tu red.</p>
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
                            <h4 class="card-title mb-0">Clientes con Transacciones</h4>
                            <i class="mdi mdi-account text-success icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_clients_with_transactions }}</h2>
                        <p class="text-muted mb-0">Clientes identificables por actividad en tu red</p>
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

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Pendientes</h4>
                            <i class="mdi mdi-cash-multiple text-danger icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">{{ $total_unpaid_transactions }}</h2>
                        <p class="text-muted mb-0">Transacciones sin pagar</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Deuda Total</h4>
                            <i class="mdi mdi-currency-usd text-danger icon-lg"></i>
                        </div>
                        <h2 class="font-weight-bold mb-2">${{ number_format($total_debt, 2) }}</h2>
                        <p class="text-muted mb-0">Incluye cualquier cantidad de GB</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Accesos de Tu Red</h4>
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Partners Relacionados</h4>
                        @if(collect($related_partners)->isEmpty())
                            <p class="text-muted mb-0">No tienes partners relacionados registrados todavía.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Partner</th>
                                            <th>Código</th>
                                            <th>Clientes con transacciones</th>
                                            <th>Transacciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($related_partners as $partner)
                                            <tr>
                                                <td>{{ $partner['nombre'] }}</td>
                                                <td>{{ $partner['codigo'] ?: 'N/A' }}</td>
                                                <td>{{ $partner['clientes'] }}</td>
                                                <td>{{ $partner['transactions'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
function copySuperPartnerLink() {
    const input = document.getElementById('superPartnerReferralLink');

    if (!input) {
        return;
    }

    const linkValue = input.value;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(linkValue)
            .then(function () {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Link copiado al portapapeles.');
                }
            })
            .catch(function () {
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');

                if (typeof toastr !== 'undefined') {
                    toastr.success('Link copiado al portapapeles.');
                }
            });

        return;
    }

    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');

    if (typeof toastr !== 'undefined') {
        toastr.success('Link copiado al portapapeles.');
    }
}
</script>
@endpush
