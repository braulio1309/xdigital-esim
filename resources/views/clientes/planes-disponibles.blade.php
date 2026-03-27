@extends('auth-layouts.auth')

@section('title', 'Planes Disponibles - Nomad eSIM')

@section('contents')
{{-- Estilos personalizados para esta vista --}}
<style>
    /* Colores de marca */
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    .brand-alliance-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-bottom: 30px;
    }

    .top-row-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .partner-row-logo {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .logo-nomad {
        height: 44px;
        object-fit: contain;
    }

    .logo-partner {
        max-height: 58px;
        max-width: 220px;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
    }

    .brand-footnote {
        margin-top: 22px;
        text-align: center;
        font-size: 0.72rem;
        line-height: 1.5;
        color: rgba(24, 28, 54, 0.58);
    }

    .sales-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(24, 28, 54, 0.98) 0%, rgba(45, 156, 219, 0.92) 100%);
        border-radius: 28px;
        padding: 38px 34px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 20px 45px rgba(24, 28, 54, 0.18);
    }

    .sales-hero::before,
    .sales-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .sales-hero::before {
        width: 240px;
        height: 240px;
        top: -110px;
        right: -70px;
    }

    .sales-hero::after {
        width: 160px;
        height: 160px;
        bottom: -75px;
        left: -35px;
    }

    .sales-hero-content {
        position: relative;
        z-index: 1;
    }

    .sales-kicker {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .sales-title {
        font-size: 2.45rem;
        line-height: 1.05;
        font-weight: 800;
        margin-bottom: 14px;
        color: white;
    }

    .sales-copy {
        font-size: 1rem;
        line-height: 1.7;
        max-width: 700px;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 22px;
    }

    .sales-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .sales-badge {
        display: inline-flex;
        align-items: center;
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.12);
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .sales-partner-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .sales-panel {
        position: relative;
        z-index: 1;
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sales-panel-item {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 18px;
        padding: 16px;
    }

    .sales-panel-label {
        display: block;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: rgba(255, 255, 255, 0.72);
        margin-bottom: 6px;
    }

    .sales-panel-value {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        line-height: 1.4;
    }

    .sales-message {
        border: none;
        border-radius: 24px;
        padding: 22px 24px;
        margin-bottom: 26px;
        box-shadow: 0 14px 34px rgba(24, 28, 54, 0.09);
    }

    .sales-message.sales-warning {
        background: linear-gradient(135deg, #fff6dc 0%, #ffe8ae 100%);
        color: #5a4300;
    }

    .sales-message.sales-success {
        background: linear-gradient(135deg, #e9fbf4 0%, #d8f6e8 100%);
        color: #125d3f;
    }

    .sales-message-title {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .sales-message-copy {
        margin-bottom: 0;
        line-height: 1.7;
        font-size: 0.98rem;
    }

    .page-title {
        color: var(--nomad-navy);
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    .country-selector {
        max-width: 300px;
        margin: 0 auto 30px;
    }

    .country-selector select {
        border: 2px solid var(--nomad-blue);
        font-size: 1.1rem;
        padding: 12px;
    }

    .country-selector select:focus {
        border-color: var(--xcertus-purple);
        box-shadow: 0 0 0 0.2rem rgba(98, 59, 134, 0.25);
    }

    /* Grid de planes */
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 30px;
    }

    @media (max-width: 1200px) {
        .plans-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .plans-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .plans-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card de plan */
    .plan-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(45, 156, 219, 0.3);
        border-color: var(--nomad-blue);
    }

    .plan-card.free-plan {
        border-color: #28a745;
    }

    .plan-card.free-plan::before {
        content: 'GRATIS';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .plan-duration {
        color: var(--nomad-navy);
        font-size: 0.95rem;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .plan-data {
        color: var(--xcertus-purple);
        font-size: 2rem;
        font-weight: bold;
        margin: 10px 0;
    }

    .plan-price {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 15px 0;
    }

    .plan-price.free {
        color: #28a745;
    }

    .plan-price.paid {
        color: var(--nomad-navy);
    }

    .btn-buy {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-buy:hover {
        opacity: 0.9;
        transform: scale(1.02);
        color: white;
    }

    .loading-spinner {
        text-align: center;
        padding: 40px;
    }

    .loading-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
        color: var(--nomad-blue);
    }

    .no-plans {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 1.1rem;
    }

    .alert-info {
        background-color: #e1f5fe;
        border-color: var(--nomad-blue);
        color: var(--nomad-navy);
    }

    /* Modal de autenticación */
    .auth-modal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .auth-modal .modal-header {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        border: none;
    }

    .auth-modal .nav-tabs {
        border-bottom: 2px solid var(--nomad-blue);
    }

    .auth-modal .nav-tabs .nav-link {
        color: #666;
        border: none;
        padding: 12px 24px;
    }

    .auth-modal .nav-tabs .nav-link.active {
        color: var(--nomad-navy);
        background-color: transparent;
        border-bottom: 3px solid var(--nomad-blue);
        font-weight: 600;
    }

    /* Modal de confirmación */
    .success-modal .modal-content {
        border-radius: 15px;
        text-align: center;
    }

    .success-icon {
        font-size: 4rem;
        color: #28a745;
        margin: 20px 0;
    }

    .qr-code-container {
        padding: 20px;
        background: white;
        border: 3px solid var(--xcertus-yellow);
        border-radius: 10px;
        display: inline-block;
        margin: 20px auto;
    }

    @media (max-width: 576px) {
        .sales-hero {
            padding: 28px 22px;
            border-radius: 22px;
        }

        .sales-title {
            font-size: 1.9rem;
        }

        .sales-panel {
            grid-template-columns: 1fr;
        }

        .logo-nomad {
            height: 36px;
        }

        .logo-partner {
            max-height: 46px;
            max-width: 180px;
        }

        .brand-footnote {
            font-size: 0.68rem;
        }
    }

</style>

@php
    $displayPartner = $brandPartner ?? $beneficiario ?? $superPartner ?? null;
    $displayPartnerName = $displayPartner->nombre ?? null;
    $displayPartnerLogo = $displayPartner->logo_url ?? null;
    $permissionError = session('error');
    $hasPermissionError = is_string($permissionError) && str_contains($permissionError, 'No tienes permiso');

    if (!$displayPartnerLogo && $displayPartner && !empty($displayPartner->logo)) {
        $displayPartnerLogo = asset('storage/' . $displayPartner->logo);
    }
@endphp

<div id="planes-disponibles-app" class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-start auth px-0 py-5">
            <div class="row w-100 mx-0">
                <div class="col-lg-11 col-xl-10 mx-auto">
                    
                    {{-- Header con logo --}}
                    <div class="text-center mb-3">
                            <p class="small text-muted text-uppercase mb-2 font-weight-bold" style="letter-spacing: 1px;">Alianza Estratégica</p>
                            <div class="brand-alliance-container">
                                <div class="top-row-logos">
                                    <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-nomad">
                                </div>
                                @if($displayPartner && $displayPartnerLogo)
                                    <div class="partner-row-logo">
                                        <img src="{{ $displayPartnerLogo }}" alt="{{ $displayPartnerName }}" class="logo-partner">
                                    </div>
                                @endif
                            </div>
                        </div>

                    <div class="sales-hero">
                        <div class="sales-hero-content">
                            <div class="sales-kicker">Amplia tu conectividad internacional</div>
                            <h1 class="sales-title">Más datos, más destinos y una experiencia lista para viajar.</h1>
                            <p class="sales-copy">
                                Elige tu siguiente plan eSIM y sigue conectado con una activación simple, cobertura internacional y una compra pensada para convertir visitantes en viajeros conectados en minutos.
                            </p>

                            <div class="sales-badges">
                                <span class="sales-badge"><i class="mdi mdi-earth mr-2"></i>Cobertura internacional</span>
                                <span class="sales-badge"><i class="mdi mdi-lightning-bolt-outline mr-2"></i>Activación rápida</span>
                                <span class="sales-badge"><i class="mdi mdi-cellphone-wireless mr-2"></i>Sin chip físico</span>
                            </div>

                            @if($displayPartner)
                                <div class="sales-partner-chip">
                                    <i class="mdi mdi-handshake-outline"></i>
                                    Beneficio exclusivo con {{ $displayPartnerName }}
                                </div>
                            @endif

                            <div class="sales-panel">
                                <div class="sales-panel-item">
                                    <span class="sales-panel-label">Ideal para</span>
                                    <div class="sales-panel-value">Viajes, trabajo remoto y ampliaciones de datos sin fricción</div>
                                </div>
                                <div class="sales-panel-item">
                                    <span class="sales-panel-label">Recomendación</span>
                                    <div class="sales-panel-value">Compara 3GB, 5GB y 10GB para elegir el plan que mejor acompaña tu ruta</div>
                                </div>
                                <div class="sales-panel-item">
                                    <span class="sales-panel-label">Ventaja</span>
                                    <div class="sales-panel-value">Compra en línea y recibe tu eSIM lista para usar en el momento</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Flash messages --}}
                    @if(session('error'))
                        @if($hasPermissionError)
                            <div class="sales-message sales-warning" role="alert">
                                <div class="sales-message-title">Tu beneficio gratuito ya fue utilizado, pero puedes seguir conectado ahora mismo.</div>
                                <p class="sales-message-copy">
                                    Tienes disponibles planes listos para ampliar tu conectividad de forma inmediata. Elige el país, compara opciones y activa más datos en pocos pasos con una compra rápida y segura.
                                </p>
                            </div>
                        @else
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle-outline mr-2"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    @endif
                    @if(session('success'))
                        <div class="sales-message sales-success" role="alert">
                            <div class="sales-message-title">Tu acceso está listo para seguir comprando.</div>
                            <p class="sales-message-copy">{{ session('success') }}</p>
                        </div>
                    @endif

                    <h2 class="page-title">Planes eSIM Disponibles</h2>
                    
                    {{-- Selector de país --}}
                    <div class="country-selector">
                        <select class="form-control form-control-lg" v-model="selectedCountry" @change="loadPlans">
                            <option value="">Seleccione un país</option>
                            @foreach($allCountries as $country)
                            <option value="{{ $country['code'] }}">{{ \App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']) }} {{ $country['name'] }}</option>
                            @endforeach
                        </select>
                        
                    </div>

                    {{-- Mensajes informativos --}}
                    <div v-if="!selectedCountry" class="alert alert-info">
                        <i class="mdi mdi-information-outline mr-2"></i>
                        Seleccione un país para ver los planes disponibles
                    </div>

                    {{-- Loading spinner --}}
                    <div v-if="loading" class="loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3">Cargando planes disponibles...</p>
                    </div>

                    {{-- Grid de planes --}}
                    <div v-if="!loading && plans.length > 0" class="plans-grid">
                        <div v-for="plan in plans" :key="plan.id" 
                             class="plan-card" 
                             :class="{ 'free-plan': plan.is_free }">
                            
                            <div class="plan-duration">
                                @{{ plan.duration }} @{{ formatDurationUnit(plan.duration_unit) }}
                            </div>
                            
                            <div class="plan-data">
                                @{{ plan.amount }}@{{ plan.amount_unit }}
                            </div>
                            
                            <div class="plan-price" :class="plan.is_free ? 'free' : 'paid'">
                                <span v-if="plan.is_free">GRATIS</span>
                                <span v-else>@{{ plan.price }} @{{ plan.price_unit }}</span>
                            </div>
                            
                            <button @click="selectPlan(plan)" class="btn btn-buy">
                                Comprar
                            </button>
                        </div>
                    </div>

                    {{-- Sin planes disponibles --}}
                    <div v-if="!loading && selectedCountry && plans.length === 0" class="no-plans">
                        <i class="mdi mdi-alert-circle-outline" style="font-size: 3rem;"></i>
                        <p class="mt-3">No hay planes disponibles para el país seleccionado</p>
                    </div>

                </div>
                <div class="col-12">
                    <div class="brand-footnote px-4 px-sm-5">
                        Servicio de Nomad eSIM con distribución para Iberoamérica mediante alianza con Xcertus.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Autenticación --}}
    <div class="modal fade auth-modal" id="authModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Iniciar Sesión o Registrarse</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Tabs para Login/Register --}}
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#loginTab">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#registerTab">Registrarse</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Tab de Login --}}
                        <div id="loginTab" class="tab-pane fade show active">
                            <form @submit.prevent="handleLogin">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" v-model="loginForm.email" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" v-model="loginForm.password" required>
                                </div>
                                <div v-if="authError" class="alert alert-danger">@{{ authError }}</div>
                                <button type="submit" class="btn btn-buy" :disabled="authLoading">
                                    <span v-if="authLoading">Iniciando...</span>
                                    <span v-else>Iniciar Sesión</span>
                                </button>
                            </form>
                        </div>

                        {{-- Tab de Registro --}}
                        <div id="registerTab" class="tab-pane fade">
                            <form @submit.prevent="handleRegister">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" v-model="registerForm.nombre" required>
                                </div>
                                <div class="form-group">
                                    <label>Apellido</label>
                                    <input type="text" class="form-control" v-model="registerForm.apellido" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" v-model="registerForm.email" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" v-model="registerForm.password" required minlength="6">
                                </div>
                                <div class="form-group">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" class="form-control" v-model="registerForm.password_confirmation" required>
                                </div>
                                <div v-if="authError" class="alert alert-danger">@{{ authError }}</div>
                                <button type="submit" class="btn btn-buy" :disabled="authLoading">
                                    <span v-if="authLoading">Registrando...</span>
                                    <span v-else>Registrarse</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Pago con Stripe --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%); color: white;">
                    <h5 class="modal-title">Confirmar Pago</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedPlan">
                        <h5>Plan seleccionado:</h5>
                        <p>
                            <strong>@{{ selectedPlan.amount }}@{{ selectedPlan.amount_unit }}</strong> - 
                            @{{ selectedPlan.duration }} @{{ formatDurationUnit(selectedPlan.duration_unit) }}
                        </p>
                        <h4 class="mb-4">Total: @{{ selectedPlan.price }} @{{ selectedPlan.price_unit }}</h4>

                        {{-- Stripe Elements se cargará aquí --}}
                        <div id="card-element" class="form-control mb-3" style="padding: 12px;"></div>
                        <div id="card-errors" class="text-danger mb-3"></div>

                        <button @click="processPayment" class="btn btn-buy" :disabled="paymentProcessing">
                            <span v-if="paymentProcessing">Procesando...</span>
                            <span v-else>Pagar @{{ selectedPlan.price }} @{{ selectedPlan.price_unit }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Éxito con QR --}}
    <div class="modal fade success-modal" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-5">
                    <div class="success-icon">
                        <i class="mdi mdi-check-circle"></i>
                    </div>
                    <h3>¡Pago Exitoso!</h3>
                    <p class="mb-4">Tu eSIM ha sido activada correctamente</p>

                    <div v-if="esimData" class="text-center">
                        {{-- QR Code --}}
                        <div class="qr-code-container" v-html="esimData.qr_svg"></div>

                        {{-- Botón de Activación Automática --}}
                        <div class="my-3">
                            <button type="button" class="btn btn-success btn-lg" @click="activarEsimAutomaticamente">
                                <i class="mdi mdi-cellphone-check mr-2"></i>Activar eSIM Automáticamente
                            </button>
                            <p class="small text-muted mt-2">Si no sabes usar el QR, haz clic aquí para activar tu eSIM</p>
                        </div>

                        {{-- Datos manuales --}}
                        <div class="text-left mt-4 p-3 bg-light rounded">
                            <h5 class="mb-3">Instalación Manual</h5>
                            <div class="form-group">
                                <label class="font-weight-bold">SM-DP+ Address:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" :value="esimData.smdp" readonly id="smdp-input">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" @click="copyToClipboard('smdp-input')">Copiar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Código de Activación:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" :value="esimData.code" readonly id="code-input">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" @click="copyToClipboard('code-input')">Copiar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary mt-4" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Vue({
        el: '#planes-disponibles-app',
        data: {
            selectedCountry: '',
            plans: [],
            loading: false,
            selectedPlan: null,
            isAuthenticated: false,
            loginForm: {
                email: '',
                password: ''
            },
            registerForm: {
                nombre: '',
                apellido: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            authLoading: false,
            authError: '',
            paymentProcessing: false,
            esimData: null,
            stripe: null,
            cardElement: null,
            paymentIntentId: null,
            errorMessage: ''
        },
        mounted() {
            // Inicializar Stripe
            this.stripe = Stripe('{{ $stripePublicKey }}');
            const elements = this.stripe.elements();
            this.cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                    }
                }
            });

            // Verificar si el usuario está autenticado
            this.checkAuth();
        },
        methods: {
            async checkAuth() {
                try {
                    const response = await axios.get('/api/auth/check');
                    this.isAuthenticated = response.data.authenticated;
                } catch (error) {
                    this.isAuthenticated = false;
                }
            },
            async loadPlans() {
                if (!this.selectedCountry) return;
                
                this.loading = true;
                this.plans = [];

                try {
                    const response = await axios.post('/planes/get-by-country', {
                        country: this.selectedCountry
                    });
                    
                    if (response.data.success) {
                        this.plans = response.data.products;
                    }
                } catch (error) {
                    console.error('Error cargando planes:', error);
                    this.showErrorMessage('Error al cargar los planes. Por favor, verifica tu conexión e intenta nuevamente.');
                } finally {
                    this.loading = false;
                }
            },
            formatDurationUnit(unit) {
                const units = {
                    'DAY': 'días',
                    'DAYS': 'días',
                    'MONTH': 'meses',
                    'MONTHS': 'meses',
                    'YEAR': 'años',
                    'YEARS': 'años'
                };
                return units[unit] || unit;
            },
            selectPlan(plan) {
                this.selectedPlan = plan;
                
                if (plan.is_free) {
                    // Para planes gratuitos, proceder directamente
                    this.processFreeActivation();
                } else {
                    // Para planes de pago, verificar autenticación
                    if (!this.isAuthenticated) {
                        $('#authModal').modal('show');
                    } else {
                        this.showPaymentModal();
                    }
                }
            },
            async handleLogin() {
                this.authLoading = true;
                this.authError = '';

                try {
                    const response = await axios.post('/api/auth/login', this.loginForm);
                    
                    if (response.data.success) {
                        this.isAuthenticated = true;
                        $('#authModal').modal('hide');
                        this.showPaymentModal();
                    }
                } catch (error) {
                    this.authError = error.response?.data?.message || 'Error al iniciar sesión';
                } finally {
                    this.authLoading = false;
                }
            },
            async handleRegister() {
                this.authLoading = true;
                this.authError = '';

                try {
                    const response = await axios.post('/api/auth/register', this.registerForm);
                    
                    if (response.data.success) {
                        this.isAuthenticated = true;
                        $('#authModal').modal('hide');
                        this.showPaymentModal();
                    }
                } catch (error) {
                    const errors = error.response?.data?.errors;
                    if (errors) {
                        this.authError = Object.values(errors).flat().join('. ');
                    } else {
                        this.authError = error.response?.data?.message || 'Error al registrar usuario';
                    }
                } finally {
                    this.authLoading = false;
                }
            },
            showPaymentModal() {
                $('#paymentModal').modal('show');
                // Montar el elemento de tarjeta después de que el modal esté visible
                setTimeout(() => {
                    if (!document.querySelector('#card-element').hasChildNodes()) {
                        this.cardElement.mount('#card-element');
                    }
                }, 300);
            },
            async processPayment() {
                if (!this.selectedPlan || this.paymentProcessing) return;

                this.paymentProcessing = true;

                try {
                    // Crear Payment Intent
                    const intentResponse = await axios.post('/planes/create-payment-intent', {
                        product_id: this.selectedPlan.id,
                        amount: this.selectedPlan.price,
                        currency: this.selectedPlan.price_unit.toLowerCase()
                    });

                    if (!intentResponse.data.success) {
                        throw new Error('Error creando Payment Intent');
                    }

                    const clientSecret = intentResponse.data.client_secret;
                    this.paymentIntentId = intentResponse.data.payment_intent_id;

                    // Confirmar pago con Stripe
                    const { error, paymentIntent } = await this.stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: this.cardElement
                        }
                    });

                    if (error) {
                        throw new Error(error.message);
                    }

                    // Procesar activación de eSIM
                    const activationResponse = await axios.post('/planes/procesar-pago', {
                        product_id: this.selectedPlan.id,
                        payment_intent_id: this.paymentIntentId,
                        plan_name: this.selectedPlan.name,
                        data_amount: this.selectedPlan.amount,
                        duration: this.selectedPlan.duration,
                        purchase_amount: this.selectedPlan.price,
                        currency: this.selectedPlan.price_unit
                    });

                    if (activationResponse.data.success) {
                        this.esimData = activationResponse.data.esim_data;
                        $('#paymentModal').modal('hide');
                        $('#successModal').modal('show');
                    }
                } catch (error) {
                    this.showErrorMessage('Error procesando el pago: ' + (error.message || error));
                    console.error('Payment error:', error);
                } finally {
                    this.paymentProcessing = false;
                }
            },
            async processFreeActivation() {
                // Para planes gratuitos, verificar autenticación
                if (!this.isAuthenticated) {
                    $('#authModal').modal('show');
                    return;
                }

                try {
                    this.loading = true;
                    // Llamar al endpoint de activación gratuita (reutilizando registro)
                    const response = await axios.post('/planes/activar-gratis', {
                        product_id: this.selectedPlan.id,
                        plan_name: this.selectedPlan.name,
                        data_amount: this.selectedPlan.amount,
                        duration: this.selectedPlan.duration
                    });

                    if (response.data.success) {
                        this.esimData = response.data.esim_data;
                        $('#successModal').modal('show');
                    } else {
                        this.showErrorMessage('Error activando el plan gratuito');
                    }
                } catch (error) {
                    this.showErrorMessage('Error al activar el plan gratuito. Por favor, intenta nuevamente.');
                    console.error('Free activation error:', error);
                } finally {
                    this.loading = false;
                }
            },
            copyToClipboard(inputId) {
                const input = document.getElementById(inputId);
                
                // Usar la API moderna de Clipboard
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(input.value)
                        .then(() => {
                            this.showSuccessMessage('Copiado al portapapeles');
                        })
                        .catch(err => {
                            console.error('Error copiando:', err);
                            // Fallback al método antiguo
                            this.fallbackCopyToClipboard(input);
                        });
                } else {
                    // Fallback para navegadores antiguos o contextos no seguros
                    this.fallbackCopyToClipboard(input);
                }
            },
            fallbackCopyToClipboard(input) {
                input.select();
                input.setSelectionRange(0, 99999); // Para móviles
                try {
                    document.execCommand('copy');
                    this.showSuccessMessage('Copiado al portapapeles');
                } catch (err) {
                    console.error('Error copiando:', err);
                }
            },
            showErrorMessage(message) {
                this.errorMessage = message;
                // Opcionalmente usar SweetAlert2 si está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                } else {
                    alert(message);
                }
            },
            showSuccessMessage(message) {
                // Opcionalmente usar SweetAlert2 si está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(message);
                }
            },
            activarEsimAutomaticamente() {
                if (!this.esimData) {
                    this.showErrorMessage('Error: No hay datos de eSIM disponibles.');
                    return;
                }
                
                // Validar que existen las propiedades necesarias
                if (!this.esimData.smdp || !this.esimData.code) {
                    this.showErrorMessage('Error: Los datos de activación no están completos.');
                    return;
                }
                
                // Validar que los datos no sean 'N/A'
                if (this.esimData.smdp === 'N/A' || this.esimData.code === 'N/A') {
                    this.showErrorMessage('Error: Los datos de activación no son válidos.');
                    return;
                }
                
                // Construir el string LPA completo
                var smdp = this.esimData.smdp;
                var code = this.esimData.code;
                var lpaString = 'LPA:1$' + smdp + '$' + code;
                
                // Detectar el tipo de dispositivo
                var userAgent = navigator.userAgent || navigator.vendor || window.opera;
                var isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
                var isAndroid = /android/i.test(userAgent);
                
                if (isIOS) {
                    this.mostrarInstruccionesIOS(smdp, code);
                } else if (isAndroid) {
                    this.activarEnAndroid(lpaString, smdp, code);
                } else {
                    this.mostrarInstruccionesDesktop(smdp, code);
                }
            },
            mostrarInstruccionesIOS(smdp, code) {
                var mensaje = '📱 INSTRUCCIONES PARA iOS:\n\n' +
                    '1. Ve a Configuración\n' +
                    '2. Toca "Celular" o "Datos móviles"\n' +
                    '3. Toca "Agregar plan celular"\n' +
                    '4. Toca "Usar código QR" (escanea el QR arriba) o "Introducir información manualmente"\n' +
                    '5. Si eliges manual, introduce:\n\n' +
                    '   SM-DP+: ' + smdp + '\n' +
                    '   Código: ' + code + '\n\n' +
                    '6. Sigue las instrucciones en pantalla\n\n' +
                    '💡 Consejo: Los códigos ya están copiables arriba para facilitar el proceso.';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Instrucciones para iOS',
                        html: mensaje.replace(/\n/g, '<br>'),
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(mensaje);
                }
            },
            activarEnAndroid(lpaString, smdp, code) {
                // Intentar abrir la configuración de eSIM en Android
                var intentUrl = 'intent://esim#Intent;scheme=esim;package=com.android.settings;S.activation_code=' + 
                                encodeURIComponent(lpaString) + ';end';
                
                // Intentar abrir con el intent
                window.location.href = intentUrl;
                
                // Mostrar instrucciones de respaldo después de un breve delay
                // (si el intent funciona, el usuario habrá cambiado de app; si no, verá las instrucciones)
                setTimeout(() => {
                    this.mostrarInstruccionesAndroid(smdp, code);
                }, 2000);
            },
            mostrarInstruccionesAndroid(smdp, code) {
                var mensaje = '📱 INSTRUCCIONES PARA ANDROID:\n\n' +
                    '1. Ve a Configuración\n' +
                    '2. Busca "Red móvil" o "Conexiones"\n' +
                    '3. Toca "Administrador de SIM" o "SIM"\n' +
                    '4. Toca "Agregar eSIM" o "Descargar eSIM"\n' +
                    '5. Escanea el código QR de arriba o introduce manualmente:\n\n' +
                    '   SM-DP+: ' + smdp + '\n' +
                    '   Código: ' + code + '\n\n' +
                    '6. Confirma la instalación\n\n' +
                    '💡 Nota: Los pasos pueden variar según tu modelo de teléfono.';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Instrucciones para Android',
                        html: mensaje.replace(/\n/g, '<br>'),
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(mensaje);
                }
            },
            mostrarInstruccionesDesktop(smdp, code) {
                var mensaje = '💻 ACTIVACIÓN DESDE COMPUTADORA:\n\n' +
                    'Para activar tu eSIM necesitas hacerlo desde tu teléfono móvil.\n\n' +
                    '📋 Opciones:\n\n' +
                    '1. Escanea el código QR mostrado arriba con tu teléfono\n' +
                    '2. Copia los datos manualmente y ábrelos en tu teléfono:\n\n' +
                    '   SM-DP+: ' + smdp + '\n' +
                    '   Código: ' + code + '\n\n' +
                    '3. Envía esta página a tu teléfono y activa desde allí';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Activación desde Computadora',
                        html: mensaje.replace(/\n/g, '<br>'),
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    alert(mensaje);
                }
            }
        }
    });
});
</script>
@endsection
