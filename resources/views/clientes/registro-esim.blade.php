@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM - Alianza Xcertus & Nomad')

@section('contents')
{{-- Estilos personalizados para esta vista (Brand Colors) --}}
<style>
    /* Colores extraídos de los logos */
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    .brand-alliance-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .logo-xcertus {
        height: 50px; /* Ajusta según necesidad */
        object-fit: contain;
    }

    .logo-nomad {
        height: 40px; /* Ajusta según necesidad */
        object-fit: contain;
    }

    .alliance-x {
        font-size: 1.5rem;
        color: #ccc;
        font-weight: 300;
    }

    .promo-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e1f5fe 100%);
        border-left: 5px solid var(--nomad-blue);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
        color: var(--nomad-navy);
    }

    .btn-brand-gradient {
        /* Degradado desde el Navy de Nomad al Morado de Xcertus */
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        transition: transform 0.2s;
    }

    .btn-brand-gradient:hover {
        opacity: 0.95;
        transform: scale(1.01);
        color: #fff;
    }

    .form-control:focus {
        border-color: var(--nomad-blue);
        box-shadow: 0 0 0 0.2rem rgba(45, 156, 219, 0.25);
    }
    
    .text-xcertus { color: var(--xcertus-purple); }
</style>

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm rounded">
                        
                        {{-- 1. HEADER LOGOS (Alianza) --}}
                        <div class="text-center mb-3">
                            <p class="small text-muted text-uppercase mb-2 font-weight-bold" style="letter-spacing: 1px;">Alianza Estratégica</p>
                            <div class="brand-alliance-container">
                                {{-- Logo Xcertus --}}
                                <img src="{{ asset('images/logo.png') }}" alt="Xcertus" class="logo-xcertus">
                                <span class="alliance-x">&times;</span>
                                {{-- Logo Nomad --}}
                                <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-nomad">
                            </div>
                        </div>

                        {{-- 2. TEXTO PROMOCIONAL (Solo visible si NO estamos en éxito aun, opcional) --}}
                        @if(!isset($esim_data) && !session('esim_data'))
                        <div class="promo-card animate__animated animate__fadeIn">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <i class="mdi mdi-gift-outline" style="font-size: 2rem; color: var(--nomad-blue);"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-1" style="color: var(--xcertus-purple);">¡Regalo Exclusivo!</h5>
                                    <p class="mb-0 small">
                                        Gracias a nuestra alianza, te obsequiamos un <strong>plan de datos gratuito por 3 días</strong>. Regístrate abajo para obtener tu eSIM al instante.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- LÓGICA PRINCIPAL: ¿Tenemos datos de eSIM en la sesión? --}}
                            @if(isset($esim_data) || session('esim_data'))                        
                            {{-- ================= VISTA DE ÉXITO (QR + DATOS) ================= --}}
                            <div class="text-center animate__animated animate__fadeIn">
                                <h4 class="font-weight-bold mb-2" style="color: var(--xcertus-purple);">¡Tu eSIM está Lista!</h4>
                                <p class="text-muted mb-4">Tu registro fue exitoso. Escanea el código para activar tu plan gratuito.</p>

                                {{-- 1. Contenedor del Código QR --}}
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="p-3 bg-white border rounded shadow-sm" style="display: inline-block; border-color: var(--xcertus-yellow) !important;">
                                        {{-- Renderizamos el SVG que viene desde el controlador --}}
                                        {!! $esim_data['qr_svg'] !!}
                                    </div>
                                </div>
                                <p class="small text-muted">Ve a <strong>Configuración > Red Móvil > Agregar eSIM</strong> y escanea.</p>

                                <hr class="my-4">

                                {{-- 2. Datos de Instalación Manual --}}
                                <div class="text-left bg-light p-3 rounded border">
                                    <h5 class="mb-3 font-weight-bold" style="color: var(--nomad-navy);"><i class="mdi mdi-cellphone-settings"></i> Instalación Manual</h5>
                                    <p class="text-muted small mb-3">Si no puedes escanear el QR, copia y pega estos datos:</p>

                                    {{-- Dirección SM-DP+ --}}
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-small mb-1 text-xcertus">Dirección SM-DP+:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $esim_data['smdp'] }}" id="smdp_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-brand-gradient" type="button" onclick="copiarTexto('smdp_input')">Copiar</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Código de Activación --}}
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-small mb-1 text-xcertus">Código de Activación:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $esim_data['code'] }}" id="code_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-brand-gradient" type="button" onclick="copiarTexto('code_input')">Copiar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botón para volver al inicio --}}
                                <div class="mt-4">
                                    <a href="{{ request()->url() }}" class="btn btn-block btn-light btn-lg font-weight-medium">
                                        Volver / Nuevo Registro
                                    </a>
                                </div>
                            </div>

                            {{-- Script JS inline para la funcionalidad de copiar --}}
                            <script>
                                function copiarTexto(id) {
                                    var copyText = document.getElementById(id);
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999); /* Para móviles */
                                    document.execCommand("copy");
                                    // Feedback visual simple
                                    alert("Copiado al portapapeles");
                                }
                            </script>

                        @else

                            {{-- ================= VISTA DE FORMULARIO (Defecto) ================= --}}
                            
                            <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">Registro de Cliente</h4>
                            
                            @if($parametro)
                                <p class="text-center text-muted mb-4">Beneficiario: <strong>{{ $parametro }}</strong></p>
                            @else
                                <p class="text-center text-muted mb-4">Completa tus datos para activar el beneficio</p>
                            @endif

                            {{-- Mensajes de error/warning del sistema --}}
                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 small">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="nombre" class="font-weight-bold text-small">Nombre</label>
                                    <input type="text" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" placeholder="Ingrese su nombre" value="{{ old('nombre') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellido" class="font-weight-bold text-small">Apellido</label>
                                    <input type="text" class="form-control form-control-lg @error('apellido') is-invalid @enderror" 
                                           id="apellido" name="apellido" placeholder="Ingrese su apellido" value="{{ old('apellido') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="font-weight-bold text-small">Email</label>
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           id="email" name="email" placeholder="Ingrese su email" value="{{ old('email') }}" required>
                                </div>

                                {{-- Selector de Plan --}}
                                <div class="form-group">
                                    <label for="product_id" class="font-weight-bold text-small">Seleccione su Plan</label>
                                    <select class="form-control form-control-lg" name="product_id" required>
                                        <option value="">-- Seleccionar Plan --</option>
                                        {{-- IDs de ejemplo, asegurar que coincidan con la DB --}}
                                        <option value="esim_1gb_usa">Plan USA 1GB</option>
                                        <option value="esim_3gb_eu">Plan Europa 3GB</option>
                                        <option value="esim_unlimited">Plan Ilimitado Global</option>
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-brand-gradient btn-lg font-weight-medium auth-form-btn">
                                        Registrar y Obtener eSIM Gratis
                                    </button>
                                </div>
                            </form>
                        @endif
                        {{-- Fin del bloque if/else --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection