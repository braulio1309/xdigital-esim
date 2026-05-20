@extends('auth-layouts.auth')

@section('title', 'Registrar Acompañantes - eSIM Gratuita')

@section('contents')
<style>
    :root {
        --xcertus-purple: #623b86;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    .btn-brand-gradient {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-brand-gradient:hover {
        opacity: 0.95;
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(98, 59, 134, 0.3);
        color: #fff;
    }

    .companion-row .btn-remove-companion {
        border-radius: 0 8px 8px 0;
    }

    .brand-footnote {
        margin-top: 24px;
        text-align: center;
        font-size: 0.72rem;
        line-height: 1.5;
        color: rgba(24, 28, 54, 0.58);
    }
</style>

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm rounded">

                        <div class="text-center mb-4">
                            <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" style="max-height: 42px;">
                        </div>

                        <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">
                            <i class="mdi mdi-account-group mr-1" style="color: var(--nomad-blue);"></i>
                            Registrar Acompañantes
                        </h4>
                        <p class="text-center text-muted mb-4 small">
                            Destino: <strong>{{ $countryName }}</strong> &mdash;
                            Tu voucher <strong>{{ $voucher->numero_voucher }}</strong> permite hasta
                            <strong>{{ $allowedCompanions }}</strong> acompañante(s) adicional(es).
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success mb-3">
                                <i class="mdi mdi-check-circle mr-1"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mb-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0 pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($remainingSlots > 0)
                            <form method="POST" action="{{ request()->fullUrl() }}" id="acompanantes-form">
                                @csrf

                                <div class="alert alert-info mb-4 d-flex align-items-center" style="border-radius: 10px;">
                                    <i class="mdi mdi-information-outline mr-2" style="font-size: 1.3rem;"></i>
                                    <span>
                                        Puedes registrar hasta <strong>{{ $remainingSlots }}</strong> acompañante(s) ahora.
                                        Cada uno recibirá su eSIM gratuita por correo.
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-small">
                                        <i class="mdi mdi-email-multiple-outline mr-1"></i>
                                        Correos de acompañantes
                                    </label>
                                    <div id="companions-list">
                                        @foreach(old('companion_emails', ['']) as $companionEmail)
                                            <div class="companion-row input-group mb-2">
                                                <input type="email"
                                                       class="form-control form-control-lg"
                                                       name="companion_emails[]"
                                                       value="{{ $companionEmail }}"
                                                       placeholder="correo@ejemplo.com"
                                                       autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-remove-companion"
                                                            tabindex="-1">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" id="btn-add-companion" class="btn btn-outline-primary btn-sm mt-1"
                                            {{ count(old('companion_emails', [''])) >= $remainingSlots ? 'disabled' : '' }}>
                                        <i class="mdi mdi-plus"></i> Agregar acompañante
                                    </button>
                                    <small class="form-text text-muted mt-2">
                                        El correo del titular ya cuenta como viajero 1; aquí solo se registran los adicionales.
                                    </small>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-brand-gradient btn-lg font-weight-medium">
                                        <i class="mdi mdi-send mr-1"></i>
                                        Enviar eSIM a acompañantes
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-success text-center">
                                <i class="mdi mdi-check-circle mr-1" style="font-size: 1.4rem;"></i>
                                <strong>Ya se utilizaron todos los cupos de acompañantes</strong> para este voucher.
                            </div>
                        @endif

                    </div>
                    <div class="brand-footnote px-4 px-sm-5">
                        Servicio de Nomad eSIM con distribución para Iberoamérica mediante alianza con Xcertus.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var maxSlots = {{ $remainingSlots }};
    var companionsList = document.getElementById('companions-list');
    var addButton = document.getElementById('btn-add-companion');

    function countRows() {
        return companionsList ? companionsList.querySelectorAll('.companion-row').length : 0;
    }

    function syncAddButton() {
        if (!addButton) return;
        addButton.disabled = countRows() >= maxSlots;
    }

    if (addButton) {
        addButton.addEventListener('click', function () {
            if (countRows() >= maxSlots) return;

            var row = document.createElement('div');
            row.className = 'companion-row input-group mb-2';
            row.innerHTML =
                '<input type="email" class="form-control form-control-lg" name="companion_emails[]" placeholder="correo@ejemplo.com" autocomplete="off">' +
                '<div class="input-group-append">' +
                '<button type="button" class="btn btn-outline-danger btn-remove-companion" tabindex="-1"><i class="mdi mdi-close"></i></button>' +
                '</div>';
            companionsList.appendChild(row);
            row.querySelector('input').focus();
            syncAddButton();
        });
    }

    if (companionsList) {
        companionsList.addEventListener('click', function (event) {
            var removeBtn = event.target.closest('.btn-remove-companion');
            if (!removeBtn) return;
            var rows = companionsList.querySelectorAll('.companion-row');
            if (rows.length <= 1) {
                removeBtn.closest('.companion-row').querySelector('input').value = '';
                return;
            }
            removeBtn.closest('.companion-row').remove();
            syncAddButton();
        });
    }

    syncAddButton();
});
</script>
@endpush
@endsection
