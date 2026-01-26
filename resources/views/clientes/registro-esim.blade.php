@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM')

@section('contents')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                        <div class="brand-logo text-center mb-4">
                            <img src="{{ filter_var(config('settings.application.company_logo'), FILTER_VALIDATE_URL) ? config('settings.application.company_logo') : request()->root().config('settings.application.company_logo') }}" alt="logo">
                        </div>

                        {{-- LÓGICA PRINCIPAL: ¿Tenemos datos de eSIM en la sesión? --}}
                            @if(isset($esim_data) || session('esim_data'))                        
                            {{-- ================= VISTA DE ÉXITO (QR + DATOS) ================= --}}
                            <div class="text-center animate__animated animate__fadeIn">
                                <h4 class="text-success font-weight-bold mb-2">¡eSIM Lista!</h4>
                                <p class="text-muted mb-4">Tu registro fue exitoso. Escanea el código para activar.</p>

                                {{-- 1. Contenedor del Código QR --}}
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="p-3 bg-white border rounded shadow-sm" style="display: inline-block;">
                                        {{-- Renderizamos el SVG que viene desde el controlador --}}
                                        {!! $esim_data['qr_svg'] !!}
                                    </div>
                                </div>
                                <p class="small text-muted">Ve a <strong>Configuración > Red Móvil > Agregar eSIM</strong> y escanea.</p>

                                <hr class="my-4">

                                {{-- 2. Datos de Instalación Manual --}}
                                <div class="text-left bg-light p-3 rounded">
                                    <h5 class="mb-3 text-dark font-weight-bold"><i class="mdi mdi-cellphone-settings"></i> Instalación Manual</h5>
                                    <p class="text-muted small mb-3">Si no puedes escanear el QR, copia y pega estos datos:</p>

                                    {{-- Dirección SM-DP+ --}}
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-small mb-1">Dirección SM-DP+:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $esim_data['smdp'] }}" id="smdp_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" onclick="copiarTexto('smdp_input')">Copiar</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Código de Activación --}}
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-small mb-1">Código de Activación:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $esim_data['code'] }}" id="code_input" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" onclick="copiarTexto('code_input')">Copiar</button>
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
                                    // Feedback visual simple (opcional)
                                    alert("Copiado al portapapeles: " + copyText.value);
                                }
                            </script>

                        @else

                            {{-- ================= VISTA DE FORMULARIO (Defecto) ================= --}}
                            
                            <h4 class="text-center mb-2">Registro de Cliente eSIM</h4>
                            @if($parametro)
                                <p class="text-center text-muted mb-4">Beneficiario: <strong>{{ $parametro }}</strong></p>
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
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" placeholder="Ingrese su nombre" value="{{ old('nombre') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" class="form-control form-control-lg @error('apellido') is-invalid @enderror" 
                                           id="apellido" name="apellido" placeholder="Ingrese su apellido" value="{{ old('apellido') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           id="email" name="email" placeholder="Ingrese su email" value="{{ old('email') }}" required>
                                </div>

                                {{-- CAMPO IMPORTANTE: Selector de Plan --}}
                                {{-- Es necesario enviar 'product_id' para que el controlador genere la eSIM --}}
                                <div class="form-group">
                                    <label for="product_id">Seleccione su Plan</label>
                                    <select class="form-control form-control-lg" name="product_id" required>
                                        <option value="">-- Seleccionar Plan --</option>
                                        {{-- Aquí deberías iterar sobre tus productos reales si los pasas desde el controlador --}}
                                        <option value="esim_1gb_usa">Plan USA 1GB</option>
                                        <option value="esim_3gb_eu">Plan Europa 3GB</option>
                                        <option value="esim_unlimited">Plan Ilimitado Global</option>
                                    </select>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        Registrar y Obtener eSIM
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