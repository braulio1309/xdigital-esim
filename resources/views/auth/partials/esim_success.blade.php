@php
    // Normalizar fuente de datos: puede venir como variable directa o desde sesión
    $esimData = $esim_data ?? session('esim_data') ?? null;
@endphp

@if($esimData)
    <div id="esim-success" class="mb-4">
        {{-- Mensaje principal de éxito --}}
        <div class="alert alert-success animate__animated animate__fadeIn mb-4">
            <h4 class="font-weight-bold mb-1">¡eSIM activada con éxito!</h4>
            <p class="mb-0 small">
                Tu eSIM gratuita ha sido generada correctamente. A continuación encontrarás el código QR y los datos
                para activarla manualmente en tu dispositivo.
            </p>
        </div>

        {{-- Botones de acción rápida --}}
        <div class="d-flex flex-wrap align-items-center mb-3">
            <a href="#esim-details" class="btn btn-sm btn-brand-gradient mr-2 mb-2">
                Ir directamente a los datos de activación
            </a>

            @if(isset($esimData['smdp']) && isset($esimData['code']) && $esimData['smdp'] !== 'N/A' && $esimData['code'] !== 'N/A')
                @php
                    // Reconstruimos el string LPA estándar con los datos recibidos
                    $lpaString = 'LPA:1$'.$esimData['smdp'].'$'.$esimData['code'];
                @endphp
                <a href="{{$lpaString}}" class="btn btn-sm btn-outline-primary mb-2" target="_blank">
                    Intentar activar automáticamente en el dispositivo
                </a>
            @endif
        </div>

        {{-- Sección QR --}}
        <div class="card mb-4" id="esim-details">
            <div class="card-body">
                <h5 class="card-title font-weight-bold mb-3">Código QR de eSIM</h5>
                <p class="small text-muted mb-3">
                    Escanea este código con la cámara de tu teléfono o desde la opción
                    <strong>"Agregar eSIM" / "Agregar plan de datos"</strong> en los ajustes de tu dispositivo.
                </p>

                <div class="text-center mb-3">
                    @if(!empty($esimData['qr_svg']))
                        {{-- El QR llega ya generado como SVG desde el controlador --}}
                        {!! $esimData['qr_svg'] !!}
                    @else
                        <p class="text-muted small">No se pudo generar el código QR. Usa los datos manuales de activación.</p>
                    @endif
                </div>

                @if(isset($lpaString))
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyEsimValue('esim-lpa-string')">
                            Copiar enlace de activación
                        </button>
                        <input type="text" id="esim-lpa-string" class="d-none" value="{{$lpaString}}">
                    </div>
                @endif
            </div>
        </div>

        {{-- Datos de activación manual --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title font-weight-bold mb-3">Datos para activación manual</h5>
                <p class="small text-muted mb-3">
                    Si tu teléfono no permite escanear el QR, puedes introducir estos datos manualmente en la sección
                    de <strong>"Agregar eSIM"</strong> o <strong>"Plan de datos móviles"</strong>.
                </p>

                <div class="form-group">
                    <label class="font-weight-bold small mb-1">Dirección SM-DP+</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control" id="esim-smdp" value="{{$esimData['smdp'] ?? 'N/A'}}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyEsimValue('esim-smdp')">Copiar</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold small mb-1">Código de activación</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control" id="esim-code" value="{{$esimData['code'] ?? 'N/A'}}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyEsimValue('esim-code')">Copiar</button>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold small mb-1">ICCID</label>
                    <div class="input-group input-group-sm">
                        <input type="text" readonly class="form-control" id="esim-iccid" value="{{$esimData['iccid'] ?? 'N/A'}}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyEsimValue('esim-iccid')">Copiar</button>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-1">
                        El ICCID puede ser solicitado por algunos dispositivos durante la activación.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Función JS simple para copiar al portapapeles --}}
    @push('after-scripts')
        <script>
            function copyEsimValue(elementId) {
                var input = document.getElementById(elementId);
                if (!input) return;

                var value = input.value || input.textContent || '';
                if (!value) return;

                // Intentar usar API moderna del portapapeles si está disponible
                if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(value).catch(function (err) {
                        console.error('Clipboard API error, fallback to execCommand', err);
                        fallbackCopyEsimValue(value);
                    });
                } else {
                    fallbackCopyEsimValue(value);
                }
            }

            function fallbackCopyEsimValue(value) {
                var tempInput = document.createElement('input');
                tempInput.type = 'text';
                tempInput.value = value;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999); // Para móviles

                try {
                    document.execCommand('copy');
                } catch (e) {
                    console.error('Error al copiar al portapapeles', e);
                }

                document.body.removeChild(tempInput);
            }
        </script>
    @endpush
@endif
