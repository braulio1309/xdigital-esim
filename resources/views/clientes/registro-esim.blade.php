@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM - Nomad')

@section('contents')
{{-- Estilos personalizados para esta vista (Brand Colors) --}}
<style>
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    /* --- ESTRUCTURA DE TRIÁNGULO PARA LOGOS --- */
    .brand-alliance-container {
        display: flex;
        flex-direction: column; /* Alineación vertical para crear filas */
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }

    .top-row-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        width: 100%;
    }

    .partner-row-logo {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 5px; /* Espacio extra para definir el triángulo */
    }

    .logo-img {
        max-width: 100%; 
        height: auto;
        object-fit: contain;
    }

    .logo-nomad {
        max-height: 42px;
        max-width: 48%;
    }

    .logo-partner {
        max-height: 58px; /* Un poco más grande para que destaque abajo */
        max-width: 58%;
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
    }

    .top-row-logos.single-brand {
        justify-content: center;
    }

    .brand-footnote {
        margin-top: 24px;
        text-align: center;
        font-size: 0.72rem;
        line-height: 1.5;
        color: rgba(24, 28, 54, 0.58);
    }

    /* --- ESTILOS GENERALES --- */
    .promo-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e1f5fe 100%);
        border-left: 5px solid var(--nomad-blue);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
        color: var(--nomad-navy);
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

    .form-control:focus {
        border-color: var(--nomad-blue);
        box-shadow: 0 0 0 0.2rem rgba(45, 156, 219, 0.25);
    }

    .country-search-input {
        margin-bottom: 12px;
    }

    .inline-plans-panel {
        margin-top: 28px;
        padding: 22px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(45, 156, 219, 0.08) 0%, rgba(98, 59, 134, 0.06) 100%);
        border: 1px solid rgba(45, 156, 219, 0.18);
    }

    .inline-plans-title {
        color: var(--nomad-navy);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .inline-plans-copy {
        color: rgba(24, 28, 54, 0.72);
        font-size: 0.92rem;
        margin-bottom: 18px;
    }

    .inline-plans-status {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 96px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.72);
        color: rgba(24, 28, 54, 0.72);
        text-align: center;
        padding: 18px;
    }

    .inline-plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .inline-plan-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(24, 28, 54, 0.08);
        padding: 18px;
        box-shadow: 0 12px 24px rgba(24, 28, 54, 0.08);
    }

    .inline-plan-meta {
        color: rgba(24, 28, 54, 0.62);
        font-size: 0.85rem;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .inline-plan-data {
        font-size: 1.5rem;
        line-height: 1.1;
        font-weight: 700;
        color: var(--nomad-navy);
        margin-bottom: 8px;
    }

    .inline-plan-price {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--xcertus-purple);
        margin-bottom: 14px;
    }

    .inline-plan-action {
        width: 100%;
    }

    .inline-plan-card.is-selected {
        border-color: rgba(98, 59, 134, 0.35);
        box-shadow: 0 16px 28px rgba(98, 59, 134, 0.14);
    }

    .inline-plans-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .registro-auth-modal .modal-header,
    .registro-payment-modal .modal-header {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        color: #fff;
    }

    .registro-auth-modal .nav-tabs .nav-link.active {
        color: var(--xcertus-purple);
        font-weight: 700;
    }

    .registro-success-copy {
        color: rgba(24, 28, 54, 0.7);
    }

    .registro-qr-box {
        display: flex;
        justify-content: center;
        margin-bottom: 18px;
    }

    .registro-manual-data {
        padding: 16px;
        border-radius: 14px;
        background: rgba(24, 28, 54, 0.04);
        text-align: left;
    }

    .registro-plan-summary {
        margin: 0 auto 18px;
        padding: 12px 16px;
        border-radius: 14px;
        background: rgba(45, 156, 219, 0.08);
        color: var(--nomad-navy);
        max-width: 420px;
        text-align: center;
    }

    .registro-plan-summary strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 4px;
    }
    
    @media (max-width: 576px) {
        .auth-form-light { padding: 2rem 1.5rem !important; }
        .logo-nomad { max-height: 34px; max-width: 60%; }
        .logo-partner { max-height: 45px; }
        .brand-footnote { font-size: 0.68rem; }
        .inline-plans-panel { padding: 18px; }
    }
</style>

@php
    $displayPartner = $brandPartner ?? $beneficiario ?? $superPartner ?? null;
    $displayPartnerName = $displayPartner->nombre ?? null;
    $displayPartnerLogo = $displayPartner->logo_url ?? null;
    $selectedCountryForPlans = session('selected_country', old('country_code'));
    $showAvailablePlans = session('show_available_plans') && !empty($selectedCountryForPlans);
    $esimData = $esim_data ?? session('esim_data');
    $esimEmailStatus = $esim_email_status ?? session('esim_email_status');
    $showFreeEsimForm = !$showAvailablePlans && empty($esimData);

    if (!$displayPartnerLogo && $displayPartner && !empty($displayPartner->logo)) {
        $displayPartnerLogo = asset('storage/' . $displayPartner->logo);
    }
@endphp

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm rounded">
                        
                        {{-- 1. HEADER LOGOS (Estructura de Triángulo) --}}
                        <div class="text-center mb-4">
                            <p class="small text-muted text-uppercase mb-3 font-weight-bold" style="letter-spacing: 1px;">Alianza Corporativa</p>
                            
                            <div class="brand-alliance-container">
                                {{-- Fila Superior: Nomad --}}
                                <div class="top-row-logos single-brand">
                                    <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-img logo-nomad">
                                </div>

                                {{-- Fila Inferior: Partner o Super Partner --}}
                                @if($displayPartner && $displayPartnerLogo)
                                    <div class="partner-row-logo animate__animated animate__zoomIn">
                                        <img src="{{ $displayPartnerLogo }}"
                                             alt="{{ $displayPartnerName }}"
                                             class="logo-img logo-partner">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. CONTENIDO PRINCIPAL --}}
                        @if($displayPartner)
                            <div class="alert alert-success animate__animated animate__fadeIn mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-account-check mr-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-break">
                                        <strong>Exclusivo para clientes de:</strong> {{ $displayPartnerName }}
                                    </div>
                                </div>
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

                        @if(!empty($esimData))
                            <div class="text-center mb-4">
                                <h4 class="mb-2 font-weight-bold" style="color: var(--nomad-navy);">Has activado una eSIM</h4>
                                <p class="text-muted mb-0 small">Escanea el QR o usa los datos manuales para terminar la activación.</p>
                            </div>

                            @if(!empty($esimEmailStatus))
                                <div class="alert {{ !empty($esimEmailStatus['sent']) ? 'alert-success' : 'alert-warning' }} mb-4">
                                    {{ $esimEmailStatus['message'] }}
                                </div>
                            @endif

                            <div class="registro-plan-summary">
                                <strong>{{ ($esimData['data_amount'] ?? 'N/A') . ' GB' }}</strong>
                                <span>{{ ($esimData['duration_days'] ?? 'N/A') . ' días de duración' }}</span>
                            </div>

                            <div class="registro-qr-box">{!! $esimData['qr_svg'] ?? '' !!}</div>

                            <div class="registro-manual-data mb-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">SM-DP+ Address</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-smdp-input" value="{{ $esimData['smdp'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-smdp-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">ICCID</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-iccid-input" value="{{ $esimData['iccid'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-iccid-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">Código de activación</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly id="registro-code-input" value="{{ $esimData['code'] ?? '' }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-code-input">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($showFreeEsimForm)
                            <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">Activar eSIM gratis</h4>
                            <p class="text-center text-muted mb-4 small">Completa tus datos para validar si tienes habilitada la activación gratuita.</p>

                            <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}">
                                @csrf
                                @if(isset($referralCode))
                                    <input type="hidden" name="referralCode" value="{{ $referralCode }}">
                                @endif

                                <div class="form-group">
                                    <label for="identificador" class="font-weight-bold text-small">DNI o Pasaporte</label>
                                    <input type="text" class="form-control form-control-lg" name="identificador" value="{{ old('identificador') }}" placeholder="Ingrese su número de documento o pasaporte" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="font-weight-bold text-small">Email</label>
                                    <input type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="country_code" class="font-weight-bold text-small">Seleccione su País</label>
                                    <input type="text"
                                           class="form-control form-control-lg country-search-input"
                                           id="registro-country-search"
                                           placeholder="Buscar país">
                                    <select class="form-control form-control-lg" name="country_code" id="registro-country-code" required>
                                        <option value="">-- Seleccionar País --</option>
                                        @foreach($affordableCountries as $country)
                                            <option value="{{ $country['code'] }}"
                                                    data-country-label="{{ mb_strtolower(\App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']) . ' ' . $country['name']) }}"
                                                    {{ old('country_code') === $country['code'] ? 'selected' : '' }}>
                                                {{ \App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']) }} {{ $country['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-brand-gradient btn-lg font-weight-medium">
                                        Obtener eSIM Gratis
                                    </button>
                                </div>
                            </form>
                        @elseif($showAvailablePlans)
                            <div class="text-center mb-3">
                                <h4 class="mb-2 font-weight-bold" style="color: var(--nomad-navy);">Planes disponibles</h4>
                                <p class="text-muted mb-0 small">Tu eSIM gratis no está habilitada. Puedes continuar comprando un plan para el país que ya seleccionaste.</p>
                            </div>

                            <div id="registro-available-plans-app"
                                 data-country="{{ $selectedCountryForPlans }}"
                                 data-plans-endpoint="{{ route('planes.get') }}"
                                 data-auth-check-endpoint="{{ route('api.auth.check') }}"
                                 data-auth-login-endpoint="{{ route('api.auth.login') }}"
                                 data-auth-register-endpoint="{{ route('api.auth.register') }}"
                                 data-payment-intent-endpoint="{{ route('planes.payment.intent') }}"
                                 data-process-payment-endpoint="{{ route('planes.pago') }}"
                                 data-activate-free-endpoint="{{ route('planes.activar.gratis') }}"
                                 data-stripe-public-key="{{ $stripePublicKey ?? '' }}">
                                <div class="inline-plans-panel">
                                    <h5 class="inline-plans-title">Planes disponibles para tu país</h5>
                                    <p class="inline-plans-copy mb-0">
                                        Ya usamos el país que seleccionaste para que no tengas que elegirlo de nuevo. Te mostramos primero las opciones más económicas.
                                    </p>

                                    <div class="inline-plans-status inline-plans-loading mt-3" id="registro-plans-status">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                        <div>Cargando planes disponibles...</div>
                                    </div>

                                    <div class="inline-plans-grid mt-3 d-none" id="registro-plans-grid"></div>

                                    <div class="alert alert-danger mt-3 mb-0 d-none" role="alert" id="registro-plans-error"></div>
                                </div>
                            </div>

                            <div class="modal fade registro-auth-modal" id="registroAuthModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Iniciar sesión o registrarse</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="nav nav-tabs mb-4" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" href="#registroLoginTab">Iniciar sesión</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#registroRegisterTab">Registrarse</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="registroLoginTab" class="tab-pane fade show active">
                                                    <form id="registro-login-form">
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" id="registro-login-email" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Contraseña</label>
                                                            <input type="password" class="form-control" id="registro-login-password" required>
                                                        </div>
                                                        <div class="alert alert-danger d-none" id="registro-auth-error"></div>
                                                        <button type="submit" class="btn btn-brand-gradient btn-block" id="registro-login-submit">
                                                            Iniciar sesión
                                                        </button>
                                                    </form>
                                                </div>

                                                <div id="registroRegisterTab" class="tab-pane fade">
                                                    <form id="registro-register-form">
                                                        <div class="form-group">
                                                            <label>Nombre</label>
                                                            <input type="text" class="form-control" id="registro-register-nombre" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Apellido</label>
                                                            <input type="text" class="form-control" id="registro-register-apellido" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" id="registro-register-email" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Contraseña</label>
                                                            <input type="password" class="form-control" id="registro-register-password" required minlength="6">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Confirmar contraseña</label>
                                                            <input type="password" class="form-control" id="registro-register-password-confirmation" required>
                                                        </div>
                                                        <div class="alert alert-danger d-none" id="registro-register-error"></div>
                                                        <button type="submit" class="btn btn-brand-gradient btn-block" id="registro-register-submit">
                                                            Registrarse
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade registro-payment-modal" id="registroPaymentModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar pago</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="registro-payment-summary" class="d-none">
                                                <h5>Plan seleccionado</h5>
                                                <p id="registro-payment-plan-copy"></p>
                                                <h4 class="mb-4" id="registro-payment-total"></h4>

                                                <div id="registro-card-element" class="form-control mb-3" style="padding: 12px;"></div>
                                                <div id="registro-card-errors" class="text-danger mb-3"></div>

                                                <button type="button" class="btn btn-brand-gradient btn-block" id="registro-pay-button">
                                                    Pagar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="registroSuccessModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body p-4 p-sm-5 text-center">
                                            <div class="success-icon mb-3">
                                                <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                                            </div>
                                            <h3>Has activado una eSIM</h3>
                                            <p class="registro-success-copy mb-4">Tu eSIM ya está lista. Aquí mismo tienes el QR y los datos manuales para activarla.</p>

                                            <div id="registro-success-content" class="d-none">
                                                <div class="registro-plan-summary" id="registro-success-plan-summary">
                                                    <strong id="registro-success-plan-amount"></strong>
                                                    <span id="registro-success-plan-duration"></span>
                                                </div>

                                                <div class="registro-qr-box" id="registro-success-qr"></div>

                                                <div class="registro-manual-data">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">SM-DP+ Address</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-smdp-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-smdp-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">ICCID</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-iccid-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-iccid-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="font-weight-bold">Código de activación</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" readonly id="registro-code-modal-input">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-copy-target="registro-code-modal-input">Copiar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-brand-gradient mt-4" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
@if($showAvailablePlans)
<script src="https://js.stripe.com/v3/"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('registro-country-search');
    const countrySelect = document.getElementById('registro-country-code');
    const availablePlansContainer = document.getElementById('registro-available-plans-app');

    if (searchInput && countrySelect) {
        searchInput.addEventListener('input', function() {
            const term = searchInput.value.trim().toLowerCase();

            Array.from(countrySelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const label = option.dataset.countryLabel || option.text.toLowerCase();
                option.hidden = term !== '' && !label.includes(term);
            });
        });
    }

    document.addEventListener('click', function(event) {
        const copyButton = event.target.closest('[data-copy-target]');

        if (!copyButton) {
            return;
        }

        event.preventDefault();
        copyInputValue(copyButton.dataset.copyTarget);
    });

    if (!availablePlansContainer) {
        return;
    }

    const state = {
        selectedCountry: availablePlansContainer.dataset.country || '',
        plansEndpoint: availablePlansContainer.dataset.plansEndpoint,
        authCheckEndpoint: availablePlansContainer.dataset.authCheckEndpoint,
        authLoginEndpoint: availablePlansContainer.dataset.authLoginEndpoint,
        authRegisterEndpoint: availablePlansContainer.dataset.authRegisterEndpoint,
        paymentIntentEndpoint: availablePlansContainer.dataset.paymentIntentEndpoint,
        processPaymentEndpoint: availablePlansContainer.dataset.processPaymentEndpoint,
        activateFreeEndpoint: availablePlansContainer.dataset.activateFreeEndpoint,
        stripePublicKey: availablePlansContainer.dataset.stripePublicKey || '',
        plans: [],
        selectedPlan: null,
        isAuthenticated: false,
        paymentIntentId: null,
        paymentProcessing: false,
        stripe: null,
        cardElement: null,
        cardMounted: false
    };

    const statusNode = document.getElementById('registro-plans-status');
    const gridNode = document.getElementById('registro-plans-grid');
    const errorNode = document.getElementById('registro-plans-error');
    const authErrorNode = document.getElementById('registro-auth-error');
    const registerErrorNode = document.getElementById('registro-register-error');
    const paymentSummaryNode = document.getElementById('registro-payment-summary');
    const paymentPlanCopyNode = document.getElementById('registro-payment-plan-copy');
    const paymentTotalNode = document.getElementById('registro-payment-total');
    const payButton = document.getElementById('registro-pay-button');
    const cardErrorsNode = document.getElementById('registro-card-errors');
    const successContentNode = document.getElementById('registro-success-content');
    const successQrNode = document.getElementById('registro-success-qr');
    const successPlanAmountNode = document.getElementById('registro-success-plan-amount');
    const successPlanDurationNode = document.getElementById('registro-success-plan-duration');
    const smdpInput = document.getElementById('registro-smdp-modal-input');
    const iccidInput = document.getElementById('registro-iccid-modal-input');
    const codeInput = document.getElementById('registro-code-modal-input');
    const loginForm = document.getElementById('registro-login-form');
    const registerForm = document.getElementById('registro-register-form');
    const loginSubmitButton = document.getElementById('registro-login-submit');
    const registerSubmitButton = document.getElementById('registro-register-submit');

    function formatDuration(duration, unit) {
        const labels = {
            DAY: 'días',
            DAYS: 'días',
            MONTH: 'meses',
            MONTHS: 'meses',
            YEAR: 'años',
            YEARS: 'años'
        };

        return duration + ' ' + (labels[unit] || unit || '');
    }

    function formatPrice(price, unit) {
        const numericPrice = Number(price);

        if (!Number.isFinite(numericPrice)) {
            return [price, unit].filter(Boolean).join(' ');
        }

        return numericPrice.toFixed(2) + ' ' + (unit || 'USD');
    }

    function showErrorMessage(message) {
        errorNode.textContent = message;
        errorNode.classList.remove('d-none');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    }

    function showSuccessMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: message,
                timer: 1800,
                showConfirmButton: false
            });
        }
    }

    function clearInlineError() {
        errorNode.textContent = '';
        errorNode.classList.add('d-none');
    }

    function setLoadingState(isLoading, message) {
        statusNode.classList.toggle('d-none', !isLoading);
        if (isLoading) {
            statusNode.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div><div>' + (message || 'Cargando planes disponibles...') + '</div>';
            gridNode.classList.add('d-none');
        }
    }

    function renderPlans() {
        clearInlineError();

        if (!state.plans.length) {
            statusNode.classList.remove('d-none');
            statusNode.innerHTML = 'No encontramos planes disponibles para este país en este momento.';
            gridNode.innerHTML = '';
            gridNode.classList.add('d-none');
            return;
        }

        statusNode.classList.add('d-none');
        gridNode.classList.remove('d-none');
        gridNode.innerHTML = state.plans.map(function(plan, index) {
            const selectedClass = state.selectedPlan && String(state.selectedPlan.id) === String(plan.id) ? ' is-selected' : '';
            return '<div class="inline-plan-card' + selectedClass + '">' +
                '<div class="inline-plan-meta">' + formatDuration(plan.duration, plan.duration_unit) + '</div>' +
                '<div class="inline-plan-data">' + plan.amount + (plan.amount_unit || '') + '</div>' +
                '<div class="inline-plan-price">' + formatPrice(plan.price, plan.price_unit) + '</div>' +
                '<button type="button" class="btn btn-brand-gradient inline-plan-action" data-plan-index="' + index + '">Comprar ahora</button>' +
                '</div>';
        }).join('');
    }

    function updateSelectedCard() {
        Array.from(gridNode.querySelectorAll('.inline-plan-card')).forEach(function(card, index) {
            const plan = state.plans[index];
            const isSelected = state.selectedPlan && plan && String(plan.id) === String(state.selectedPlan.id);
            card.classList.toggle('is-selected', !!isSelected);
        });
    }

    function setButtonBusy(button, isBusy, busyLabel, idleLabel) {
        if (!button) {
            return;
        }

        button.disabled = isBusy;
        button.textContent = isBusy ? busyLabel : idleLabel;
    }

    function setAuthError(node, message) {
        if (!node) {
            return;
        }

        node.textContent = message || '';
        node.classList.toggle('d-none', !message);
    }

    async function checkAuth() {
        try {
            const response = await axios.get(state.authCheckEndpoint);
            state.isAuthenticated = !!response.data.authenticated;
        } catch (error) {
            state.isAuthenticated = false;
        }
    }

    async function loadPlans() {
        setLoadingState(true, 'Cargando planes disponibles...');
        clearInlineError();

        try {
            const response = await axios.post(state.plansEndpoint, {
                country: state.selectedCountry
            });

            if (response.data && response.data.success) {
                state.plans = response.data.products || [];
                renderPlans();
            } else {
                state.plans = [];
                renderPlans();
                showErrorMessage('No fue posible cargar los planes disponibles en este momento.');
            }
        } catch (error) {
            state.plans = [];
            renderPlans();
            showErrorMessage('No fue posible cargar los planes disponibles en este momento.');
        } finally {
            availablePlansContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function mountStripeCard() {
        if (!state.stripePublicKey || typeof Stripe === 'undefined') {
            return false;
        }

        if (!state.stripe) {
            state.stripe = Stripe(state.stripePublicKey);
        }

        if (!state.cardElement) {
            const elements = state.stripe.elements();
            state.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d'
                    }
                }
            });
        }

        if (!state.cardMounted) {
            state.cardElement.mount('#registro-card-element');
            state.cardMounted = true;
        }

        return true;
    }

    function showPaymentModal() {
        if (!state.selectedPlan) {
            return;
        }

        clearInlineError();
        paymentSummaryNode.classList.remove('d-none');
        paymentPlanCopyNode.innerHTML = '<strong>' + state.selectedPlan.amount + (state.selectedPlan.amount_unit || '') + '</strong> - ' + formatDuration(state.selectedPlan.duration, state.selectedPlan.duration_unit);
        paymentTotalNode.textContent = 'Total: ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit);
        payButton.textContent = 'Pagar ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit);
        cardErrorsNode.textContent = '';

        if (!mountStripeCard()) {
            showErrorMessage('No fue posible inicializar el pago en este momento.');
            return;
        }

        $('#registroPaymentModal').modal('show');
    }

    function showSuccessModal(esimData) {
        successContentNode.classList.remove('d-none');
        successPlanAmountNode.textContent = (esimData.data_amount || 'N/A') + ' GB';
        successPlanDurationNode.textContent = (esimData.duration_days || 'N/A') + ' días de duración';
        successQrNode.innerHTML = esimData.qr_svg || '';
        smdpInput.value = esimData.smdp || '';
        iccidInput.value = esimData.iccid || '';
        codeInput.value = esimData.code || '';
        $('#registroSuccessModal').modal('show');
    }

    async function processFreeActivation() {
        if (!state.selectedPlan) {
            return;
        }

        if (!state.isAuthenticated) {
            $('#registroAuthModal').modal('show');
            return;
        }

        setLoadingState(true, 'Activando tu eSIM...');
        clearInlineError();

        try {
            const response = await axios.post(state.activateFreeEndpoint, {
                product_id: state.selectedPlan.id,
                plan_name: state.selectedPlan.name,
                data_amount: state.selectedPlan.amount,
                duration: state.selectedPlan.duration,
                original_price: state.selectedPlan.original_price,
            });

            if (!response.data.success) {
                throw new Error(response.data.message || 'No fue posible activar la eSIM');
            }

            renderPlans();
            showSuccessModal(response.data.esim_data || {});
        } catch (error) {
            renderPlans();
            showErrorMessage(error.response?.data?.message || error.message || 'Error al activar el plan.');
        }
    }

    async function processPayment() {
        if (!state.selectedPlan || state.paymentProcessing) {
            return;
        }

        state.paymentProcessing = true;
        clearInlineError();
        setButtonBusy(payButton, true, 'Procesando...', 'Pagar');
        cardErrorsNode.textContent = '';

        try {
            const intentResponse = await axios.post(state.paymentIntentEndpoint, {
                product_id: state.selectedPlan.id,
                amount: state.selectedPlan.price,
                currency: String(state.selectedPlan.price_unit || 'usd').toLowerCase()
            });

            if (!intentResponse.data.success) {
                throw new Error(intentResponse.data.message || 'Error creando el intento de pago');
            }

            state.paymentIntentId = intentResponse.data.payment_intent_id;

            const paymentResult = await state.stripe.confirmCardPayment(intentResponse.data.client_secret, {
                payment_method: {
                    card: state.cardElement
                }
            });

            if (paymentResult.error) {
                throw new Error(paymentResult.error.message);
            }

            const activationResponse = await axios.post(state.processPaymentEndpoint, {
                product_id: state.selectedPlan.id,
                payment_intent_id: state.paymentIntentId,
                plan_name: state.selectedPlan.name,
                data_amount: state.selectedPlan.amount,
                duration: state.selectedPlan.duration,
                purchase_amount: state.selectedPlan.price,
                currency: state.selectedPlan.price_unit
            });

            if (!activationResponse.data.success) {
                throw new Error(activationResponse.data.message || 'No fue posible activar la eSIM');
            }

            $('#registroPaymentModal').modal('hide');
            showSuccessModal(activationResponse.data.esim_data || {});
        } catch (error) {
            const message = error.response?.data?.message || error.message || 'Error procesando el pago.';
            cardErrorsNode.textContent = message;
            showErrorMessage(message);
        } finally {
            state.paymentProcessing = false;
            setButtonBusy(payButton, false, 'Procesando...', 'Pagar ' + formatPrice(state.selectedPlan.price, state.selectedPlan.price_unit));
        }
    }

    function copyInputValue(inputId) {
        const input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value)
                .then(function() {
                    showSuccessMessage('Copiado al portapapeles');
                })
                .catch(function() {
                    input.select();
                    document.execCommand('copy');
                    showSuccessMessage('Copiado al portapapeles');
                });
            return;
        }

        input.select();
        input.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');
            showSuccessMessage('Copiado al portapapeles');
        } catch (error) {
            showErrorMessage('No fue posible copiar el valor.');
        }
    }

    gridNode.addEventListener('click', function(event) {
        const actionButton = event.target.closest('[data-plan-index]');

        if (!actionButton) {
            return;
        }

        event.preventDefault();

        const planIndex = Number(actionButton.dataset.planIndex);
        const selectedPlan = state.plans[planIndex];

        if (!selectedPlan) {
            return;
        }

        state.selectedPlan = selectedPlan;
        updateSelectedCard();

        if (selectedPlan.is_free) {
            processFreeActivation();
            return;
        }

        if (!state.isAuthenticated) {
            $('#registroAuthModal').modal('show');
            return;
        }

        showPaymentModal();
    });

    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        setAuthError(authErrorNode, '');
        setButtonBusy(loginSubmitButton, true, 'Iniciando...', 'Iniciar sesión');

        try {
            const response = await axios.post(state.authLoginEndpoint, {
                email: document.getElementById('registro-login-email').value,
                password: document.getElementById('registro-login-password').value
            });

            if (response.data.success) {
                state.isAuthenticated = true;
                $('#registroAuthModal').modal('hide');
                if (state.selectedPlan && state.selectedPlan.is_free) {
                    processFreeActivation();
                } else {
                    showPaymentModal();
                }
            }
        } catch (error) {
            setAuthError(authErrorNode, error.response?.data?.message || 'Error al iniciar sesión');
        } finally {
            setButtonBusy(loginSubmitButton, false, 'Iniciando...', 'Iniciar sesión');
        }
    });

    registerForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        setAuthError(registerErrorNode, '');
        setButtonBusy(registerSubmitButton, true, 'Registrando...', 'Registrarse');

        try {
            const response = await axios.post(state.authRegisterEndpoint, {
                nombre: document.getElementById('registro-register-nombre').value,
                apellido: document.getElementById('registro-register-apellido').value,
                email: document.getElementById('registro-register-email').value,
                password: document.getElementById('registro-register-password').value,
                password_confirmation: document.getElementById('registro-register-password-confirmation').value
            });

            if (response.data.success) {
                state.isAuthenticated = true;
                $('#registroAuthModal').modal('hide');
                if (state.selectedPlan && state.selectedPlan.is_free) {
                    processFreeActivation();
                } else {
                    showPaymentModal();
                }
            }
        } catch (error) {
            const errors = error.response?.data?.errors;
            const message = errors ? Object.values(errors).flat().join('. ') : (error.response?.data?.message || 'Error al registrar usuario');
            setAuthError(registerErrorNode, message);
        } finally {
            setButtonBusy(registerSubmitButton, false, 'Registrando...', 'Registrarse');
        }
    });

    payButton.addEventListener('click', function(event) {
        event.preventDefault();
        processPayment();
    });

    checkAuth();
    loadPlans();
});
</script>
@endpush
@endsection