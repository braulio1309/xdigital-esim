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

    /* --- ESTILOS RESPONSIVOS PARA LOGOS --- */
    .brand-alliance-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px; /* Espacio entre logos */
        margin-bottom: 20px;
        flex-wrap: nowrap; /* Mantiene logos en una línea */
    }

    .logo-img {
        /* Clave para que no se corten: */
        max-width: 100%; 
        height: auto;
        object-fit: contain;
    }

    .logo-xcertus {
        max-height: 50px;
        /* En móviles muy pequeños, no dejar que ocupe más del 45% del ancho */
        max-width: 45%; 
    }

    .logo-nomad {
        max-height: 40px;
        max-width: 40%;
    }

    .alliance-x {
        font-size: 1.5rem;
        color: #ccc;
        font-weight: 300;
        padding: 0 5px;
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

    /* --- MEDIA QUERIES PARA MÓVILES (Menos de 576px) --- */
    @media (max-width: 576px) {
        .auth-form-light {
            padding: 2rem 1.5rem !important; /* Reducir padding interno */
        }
        
        .brand-alliance-container {
            gap: 10px;
        }

        .logo-xcertus { max-height: 40px; }
        .logo-nomad { max-height: 32px; }
        .alliance-x { font-size: 1.2rem; }
        
        h4 { font-size: 1.3rem; } /* Títulos más pequeños */
    }
</style>

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                {{-- 
                    CAMBIO IMPORTANTE EN GRID: 
                    col-12 en móviles, col-sm-10 en tablets, col-md-8 y col-lg-6 en escritorio.
                    Esto da más espacio al formulario en pantallas pequeñas.
                --}}
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm rounded">
                        
                        {{-- 1. HEADER LOGOS (Alianza) --}}
                        <div class="text-center mb-3">
                            <p class="small text-muted text-uppercase mb-2 font-weight-bold" style="letter-spacing: 1px;">Alianza Estratégica</p>
                            <div class="brand-alliance-container">
                                {{-- Logo Xcertus --}}
                                <img src="{{ asset('images/logo.png') }}" alt="Xcertus" class="logo-img logo-xcertus">
                                {{-- Logo Nomad --}}
                                <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-img logo-nomad">
                            </div>
                        </div>

                        {{-- 2. TEXTO PROMOCIONAL --}}
                        @if(!isset($esim_data) && !session('esim_data'))
                        
                            @if(isset($beneficiario) && $beneficiario)
                            <div class="alert alert-success animate__animated animate__fadeIn mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-account-check mr-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-break"> {{-- text-break evita desborde de nombres largos --}}
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
                                    <div style="flex: 1;"> {{-- flex: 1 permite que el texto ocupe el resto del espacio --}}
                                        <h5 class="font-weight-bold mb-1" style="color: var(--xcertus-purple);">¡Regalo Exclusivo!</h5>
                                        <p class="mb-0 small text-justify">
                                            Gracias a nuestra alianza, te obsequiamos un <strong>plan de datos gratuito por 3 días</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- LÓGICA PRINCIPAL --}}
                            @if(isset($esim_data) || session('esim_data'))                        
                            {{-- ================= VISTA DE ÉXITO (QR + DATOS) ================= --}}
                            <div class="text-center animate__animated animate__fadeIn">
                                <h4 class="font-weight-bold mb-2" style="color: var(--xcertus-purple);">¡Tu eSIM está Lista!</h4>
                                <p class="text-muted mb-4">Escanea el código para activar tu plan.</p>

                                {{-- 1. Contenedor del Código QR Responsive --}}
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="p-3 bg-white border rounded shadow-sm" style="display: inline-block; border-color: var(--xcertus-yellow) !important; max-width: 100%;">
                                        <div style="max-width: 100%; overflow: hidden;">
                                            {!! $esim_data['qr_svg'] !!}
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Botón de Activación Automática --}}
                                <div class="mb-3">
                                    <button type="button" class="btn btn-brand-gradient btn-lg" onclick="activarEsimAutomaticamente()" id="btn-activar-esim">
                                        <i class="mdi mdi-cellphone-check mr-2"></i>Activar eSIM Automáticamente
                                    </button>
                                    <p class="small text-muted mt-2">Si no sabes usar el QR, haz clic aquí para activar tu eSIM</p>
                                </div>

                                <p class="small text-muted">O escanea el código y ve a <strong>Configuración > Red Móvil > Agregar eSIM</strong>.</p>

                                <hr class="my-4">

                                {{-- 2. Datos de Instalación Manual --}}
                                <div class="text-left bg-light p-3 rounded border">
                                    <h5 class="mb-3 font-weight-bold" style="color: var(--nomad-navy);"><i class="mdi mdi-cellphone-settings"></i> Manual</h5>
                                    
                                    {{-- Dirección SM-DP+ --}}
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-small mb-1 text-xcertus">SM-DP+:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white text-truncate" value="{{ $esim_data['smdp'] }}" id="smdp_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-brand-gradient" type="button" onclick="copiarTexto('smdp_input')"><i class="mdi mdi-content-copy"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Código de Activación --}}
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-small mb-1 text-xcertus">Código:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white text-truncate" value="{{ $esim_data['code'] }}" id="code_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-brand-gradient" type="button" onclick="copiarTexto('code_input')"><i class="mdi mdi-content-copy"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ request()->url() }}" class="btn btn-block btn-light btn-lg font-weight-medium">
                                        Volver
                                    </a>
                                </div>
                            </div>

                            <script>
                                function copiarTexto(id) {
                                    var copyText = document.getElementById(id);
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999);
                                    document.execCommand("copy");
                                    alert("Copiado: " + copyText.value);
                                }

                                function activarEsimAutomaticamente() {
                                    // Validar que los elementos existen
                                    var smdpInput = document.getElementById('smdp_input');
                                    var codeInput = document.getElementById('code_input');
                                    
                                    if (!smdpInput || !codeInput) {
                                        alert('Error: No se encontraron los datos de activación. Por favor, recarga la página.');
                                        return;
                                    }
                                    
                                    // Construir el string LPA completo desde los datos disponibles
                                    var smdp = smdpInput.value;
                                    var code = codeInput.value;
                                    
                                    // Validar que los datos no estén vacíos
                                    if (!smdp || !code || smdp === 'N/A' || code === 'N/A') {
                                        alert('Error: Los datos de activación no están disponibles.');
                                        return;
                                    }
                                    
                                    var lpaString = 'LPA:1$' + smdp + '$' + code;
                                    
                                    // Detectar el tipo de dispositivo
                                    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
                                    var isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
                                    var isAndroid = /android/i.test(userAgent);
                                    
                                    if (isIOS) {
                                        // Para iOS, intentar abrir la configuración de eSIM
                                        // iOS no permite deep linking directo a eSIM desde web, mostrar instrucciones
                                        mostrarInstruccionesIOS(smdp, code);
                                    } else if (isAndroid) {
                                        // Para Android, intentar activación automática
                                        activarEnAndroid(lpaString, smdp, code);
                                    } else {
                                        // Para escritorio o dispositivos no compatibles
                                        mostrarInstruccionesDesktop(smdp, code);
                                    }
                                }

                                function mostrarInstruccionesIOS(smdp, code) {
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
                                    
                                    alert(mensaje);
                                }

                                function activarEnAndroid(lpaString, smdp, code) {
                                    // Intentar abrir la configuración de eSIM en Android
                                    // Usar el intent de Android para eSIM si está disponible
                                    var intentUrl = 'intent://esim#Intent;scheme=esim;package=com.android.settings;S.activation_code=' + 
                                                    encodeURIComponent(lpaString) + ';end';
                                    
                                    // Intentar abrir con el intent
                                    window.location.href = intentUrl;
                                    
                                    // Mostrar instrucciones de respaldo después de un breve delay
                                    // (si el intent funciona, el usuario habrá cambiado de app; si no, verá las instrucciones)
                                    setTimeout(function() {
                                        mostrarInstruccionesAndroid(smdp, code);
                                    }, 2000);
                                }

                                function mostrarInstruccionesAndroid(smdp, code) {
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
                                    
                                    alert(mensaje);
                                }

                                function mostrarInstruccionesDesktop(smdp, code) {
                                    var mensaje = '💻 ACTIVACIÓN DESDE COMPUTADORA:\n\n' +
                                        'Para activar tu eSIM necesitas hacerlo desde tu teléfono móvil.\n\n' +
                                        '📋 Opciones:\n\n' +
                                        '1. Escanea el código QR mostrado arriba con tu teléfono\n' +
                                        '2. Copia los datos manualmente y ábrelos en tu teléfono:\n\n' +
                                        '   SM-DP+: ' + smdp + '\n' +
                                        '   Código: ' + code + '\n\n' +
                                        '3. Envía esta página a tu teléfono y activa desde allí';
                                    
                                    alert(mensaje);
                                }
                            </script>

                        @else

                            {{-- ================= VISTA DE FORMULARIO ================= --}}
                            
                            <h4 class="text-center mb-2 font-weight-bold" style="color: var(--nomad-navy);">Registro de Cliente</h4>
                            
                            @if($parametro)
                                <p class="text-center text-muted mb-4 small">Beneficiario: <strong>{{ $parametro }}</strong></p>
                            @else
                                <p class="text-center text-muted mb-4 small">Completa tus datos para activar el beneficio</p>
                            @endif

                            {{-- Alertas --}}
                            @if(session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 small pl-3">
                                        @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}">
                                @csrf
                                
                                @if(isset($referralCode) && $referralCode)
                                <input type="hidden" name="referralCode" value="{{ $referralCode }}">
                                @endif

                                <div class="form-group">
                                    <label for="nombre" class="font-weight-bold text-small">Nombre</label>
                                    <input type="text" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellido" class="font-weight-bold text-small">Apellido</label>
                                    <input type="text" class="form-control form-control-lg @error('apellido') is-invalid @enderror" 
                                           id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="font-weight-bold text-small">Email</label>
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           id="email" name="email" placeholder="tucorreo@ejemplo.com" value="{{ old('email') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="country_code" class="font-weight-bold text-small">Seleccione su País</label>
                                    <select class="form-control form-control-lg" name="country_code" id="country_code" required>
                                        <option value="">-- Seleccionar País --</option>
                                        @foreach($affordableCountries as $country)
                                        <option value="{{ $country['code'] }}">
                                            {{ \App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']) }} {{ $country['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-brand-gradient btn-lg font-weight-medium auth-form-btn">
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