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
                                        
                                        {{-- Botón de Activación Automática --}}
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-success btn-lg" onclick="activarEsimDesdeDashboard('{{ $active_plan->esim_qr }}')">
                                                <i class="mdi mdi-cellphone-check mr-2"></i>Activar eSIM Automáticamente
                                            </button>
                                            <p class="small text-muted mt-2">Si no sabes usar el QR, haz clic aquí para activar tu eSIM</p>
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
                                                                        
                                                                        {{-- Botón de Activación Automática --}}
                                                                        <div class="mt-3">
                                                                            <button type="button" class="btn btn-success" onclick="activarEsimDesdeDashboard('{{ $transaction->esim_qr }}')">
                                                                                <i class="mdi mdi-cellphone-check mr-2"></i>Activar eSIM Automáticamente
                                                                            </button>
                                                                            <p class="small text-muted mt-2">Si no sabes usar el QR, haz clic aquí</p>
                                                                        </div>
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

@push('scripts')
<script>
function activarEsimDesdeDashboard(lpaString) {
    // Validar el formato del LPA string
    if (!lpaString || typeof lpaString !== 'string') {
        alert('Error: Datos de eSIM no válidos.');
        return;
    }
    
    // Separar datos del LPA string
    // Formato: LPA:1$smdp.address$activationCode
    var parts = lpaString.split('$');
    
    // Validar que tenemos todas las partes necesarias
    if (parts.length < 3 || !parts[1] || !parts[2]) {
        alert('Error: El formato de los datos de eSIM no es válido. Por favor, contacta al soporte.');
        return;
    }
    
    var smdp = parts[1];
    var code = parts[2];
    
    // Detectar el tipo de dispositivo
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    var isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
    var isAndroid = /android/i.test(userAgent);
    
    if (isIOS) {
        mostrarInstruccionesIOS(smdp, code);
    } else if (isAndroid) {
        activarEnAndroid(lpaString, smdp, code);
    } else {
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
        '💡 Consejo: Puedes copiar estos datos desde la sección de instalación manual.';
    
    alert(mensaje);
}

function activarEnAndroid(lpaString, smdp, code) {
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
@endpush
