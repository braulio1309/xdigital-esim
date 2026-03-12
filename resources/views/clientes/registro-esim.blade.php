@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM - Alianza Xcertus & Nomad')

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

    .logo-xcertus {
        max-height: 45px;
        max-width: 40%; 
    }

    .logo-nomad {
        max-height: 35px;
        max-width: 35%;
    }

    .logo-partner {
        max-height: 55px; /* Un poco más grande para que destaque abajo */
        max-width: 50%;
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));
    }

    .alliance-x {
        font-size: 1.2rem;
        color: #ddd;
        font-weight: 300;
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
    
    .text-xcertus { color: var(--xcertus-purple); }

    @media (max-width: 576px) {
        .auth-form-light { padding: 2rem 1.5rem !important; }
        .logo-xcertus { max-height: 35px; }
        .logo-nomad { max-height: 28px; }
        .logo-partner { max-height: 45px; }
    }
</style>

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
                                {{-- Fila Superior: Xcertus y Nomad --}}
                                <div class="top-row-logos">
                                    <img src="{{ asset('images/logo.png') }}" alt="Xcertus" class="logo-img logo-xcertus">
                                    <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-img logo-nomad">
                                </div>

                                {{-- Fila Inferior: Partner (Vértice del triángulo) --}}
                                @if(isset($beneficiario) && $beneficiario && ($beneficiario->logo || $beneficiario->logo_url))
                                    <div class="partner-row-logo animate__animated animate__zoomIn">
                                        {{-- Usamos asset('storage/...') si guardaste la ruta en el paso anterior --}}
                                        <img src="{{ $beneficiario->logo_url ?? asset('storage/' . $beneficiario->logo) }}" 
                                             alt="{{ $beneficiario->nombre }}" 
                                             class="logo-img logo-partner">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. TEXTO PROMOCIONAL --}}
                        @if(!isset($esim_data) && !session('esim_data'))
                            @if(isset($beneficiario) && $beneficiario)
                            <div class="alert alert-success animate__animated animate__fadeIn mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-account-check mr-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-break">
                                        <strong>Exclusivo para clientes de:</strong> {{ $beneficiario->nombre }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        
                            <div class="promo-card animate__animated animate__fadeIn">
                                <div class="d-flex align-items-start">
                                    <div class="mr-3">
                                        <i class="mdi mdi-gift-outline" style="font-size: 2rem; color: var(--nomad-blue);"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h5 class="font-weight-bold mb-1" style="color: var(--xcertus-purple);">¡Regalo Exclusivo!</h5>
                                        <p class="mb-0 small text-justify">
                                            Gracias a nuestra alianza, te obsequiamos un <strong>plan de datos gratuito por 3 días</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- LÓGICA DE CONTENIDO (QR o Formulario) --}}
                        @if(isset($esim_data) || session('esim_data'))                        
                            {{-- Vista de éxito omitida aquí por brevedad, pero se mantiene igual que tu original --}}
                            @include('auth.partials.esim_success') {{-- Sugerencia: Mover el éxito a un partial si es muy largo --}}
                        @else

                            <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">Registro de Cliente</h4>
                            <p class="text-center text-muted mb-4 small">Completa tus datos para activar el beneficio</p>

                            <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}">
                                @csrf
                                @if(isset($referralCode)) <input type="hidden" name="referralCode" value="{{ $referralCode }}"> @endif

                                <div class="form-group">
                                    <label for="nombre" class="font-weight-bold text-small">Nombre</label>
                                    <input type="text" class="form-control form-control-lg" name="nombre" value="{{ old('nombre') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellido" class="font-weight-bold text-small">Apellido</label>
                                    <input type="text" class="form-control form-control-lg" name="apellido" value="{{ old('apellido') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="font-weight-bold text-small">Email</label>
                                    <input type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="country_code" class="font-weight-bold text-small">Seleccione su País</label>
                                    <select class="form-control form-control-lg" name="country_code" required>
                                        <option value="">-- Seleccionar País --</option>
                                        @foreach($affordableCountries as $country)
                                            <option value="{{ $country['code'] }}">
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
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection