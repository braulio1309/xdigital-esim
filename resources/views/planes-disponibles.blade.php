@extends('auth-layouts.auth')

@section('title', 'Planes eSIM Disponibles - Xcertus & Nomad')

@section('contents')
{{-- Estilos personalizados para esta vista --}}
<style>
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

    body {
        background: #f8f9fa;
    }

    .brand-alliance-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .logo-xcertus {
        height: 50px;
        object-fit: contain;
    }

    .logo-nomad {
        height: 40px;
        object-fit: contain;
    }

    .alliance-x {
        font-size: 1.5rem;
        color: #ccc;
        font-weight: 300;
    }

    .plan-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .plan-card:hover {
        border-color: var(--nomad-blue);
        box-shadow: 0 4px 15px rgba(45, 156, 219, 0.2);
        transform: translateY(-5px);
    }

    .plan-duration {
        font-size: 1.1rem;
        color: var(--nomad-navy);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .plan-data {
        font-size: 1.8rem;
        color: var(--xcertus-purple);
        font-weight: bold;
        margin-bottom: 15px;
    }

    .plan-price {
        font-size: 1.5rem;
        color: #28a745;
        font-weight: bold;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .plan-price.paid {
        color: var(--nomad-blue);
    }

    .btn-brand-gradient {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        transition: transform 0.2s;
    }

    .btn-brand-gradient:hover {
        opacity: 0.95;
        transform: scale(1.02);
        color: #fff;
    }

    .country-selector {
        max-width: 300px;
        margin: 20px auto;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-overlay.active {
        display: flex;
    }

    .spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 4px solid white;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .modal-tabs {
        display: flex;
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 20px;
    }

    .modal-tab {
        flex: 1;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        background: #f8f9fa;
        border: none;
        transition: all 0.3s;
    }

    .modal-tab.active {
        background: white;
        border-bottom: 3px solid var(--nomad-blue);
        font-weight: bold;
        color: var(--nomad-blue);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .qr-result-container {
        text-align: center;
        padding: 30px;
    }

    .qr-code-box {
        display: inline-block;
        padding: 20px;
        background: white;
        border: 3px solid var(--xcertus-yellow);
        border-radius: 10px;
        margin: 20px 0;
    }

    .manual-install {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .copy-button {
        cursor: pointer;
    }

    .form-control:focus {
        border-color: var(--nomad-blue);
        box-shadow: 0 0 0 0.2rem rgba(45, 156, 219, 0.25);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .stripe-card-element {
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: white;
    }
</style>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-start auth px-0 py-4">
            <div class="row w-100 mx-0">
                <div class="col-lg-10 mx-auto">
                    <div class="auth-form-light text-left py-4 px-4 px-sm-5 shadow-sm rounded" style="background: white;">
                        
                        {{-- Header con Logos --}}
                        <div class="text-center mb-4">
                            <p class="small text-muted text-uppercase mb-2 font-weight-bold" style="letter-spacing: 1px;">Alianza Estratégica</p>
                            <div class="brand-alliance-container">
                                <img src="{{ asset('images/logo.png') }}" alt="Xcertus" class="logo-xcertus">
                                <span class="alliance-x">&times;</span>
                                <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-nomad">
                            </div>
                        </div>

                        <h3 class="text-center font-weight-bold mb-3" style="color: var(--nomad-navy);">Planes eSIM Disponibles</h3>
                        <p class="text-center text-muted mb-4">Selecciona tu país y elige el plan perfecto para ti</p>

                        {{-- Selector de País --}}
                        <div class="country-selector">
                            <select class="form-control form-control-lg" id="countrySelector">
                                <option value="">-- Seleccionar País --</option>
                                <option value="ES">España</option>
                                <option value="US">Estados Unidos (USA)</option>
                            </select>
                        </div>

                        {{-- Grid de Planes --}}
                        <div id="planesContainer" class="row mt-4">
                            <div class="col-12 empty-state">
                                <i class="mdi mdi-earth"></i>
                                <h5>Selecciona un país para ver los planes disponibles</h5>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Autenticación --}}
<div class="modal fade" id="authModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Autenticación Requerida</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" data-tab="login">Iniciar Sesión</button>
                    <button class="modal-tab" data-tab="registro">Registrarse</button>
                </div>

                {{-- Tab Login --}}
                <div class="tab-content active" id="loginTab">
                    <form id="loginForm">
                        @csrf
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-brand-gradient btn-block">Iniciar Sesión</button>
                    </form>
                </div>

                {{-- Tab Registro --}}
                <div class="tab-content" id="registroTab">
                    <form id="registroForm">
                        @csrf
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono (Opcional)</label>
                            <input type="text" class="form-control" name="telefono">
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-brand-gradient btn-block">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Checkout --}}
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirmar Compra</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="planDetails" class="mb-4"></div>
                
                <div id="paymentSection">
                    <h6 class="font-weight-bold mb-3">Información de Pago</h6>
                    <form id="paymentForm">
                        <div id="card-element" class="stripe-card-element mb-3"></div>
                        <div id="card-errors" class="text-danger mb-3"></div>
                        <button type="submit" class="btn btn-brand-gradient btn-block" id="submitPayment">
                            Procesar Pago
                        </button>
                    </form>
                </div>

                <div id="freeSection" style="display: none;">
                    <button class="btn btn-brand-gradient btn-block" id="submitFree">
                        Obtener Plan Gratuito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Resultado (QR) --}}
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">¡Tu eSIM está Lista!</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="qr-result-container">
                    <p class="text-muted mb-4">Escanea el código QR para activar tu plan</p>
                    
                    <div class="qr-code-box" id="qrCodeContainer"></div>
                    
                    <p class="small text-muted mt-3">Ve a <strong>Configuración > Red Móvil > Agregar eSIM</strong> y escanea.</p>

                    <hr class="my-4">

                    <div class="manual-install text-left">
                        <h6 class="font-weight-bold mb-3"><i class="mdi mdi-cellphone-settings"></i> Instalación Manual</h6>
                        <p class="text-muted small mb-3">Si no puedes escanear el QR, copia estos datos:</p>

                        <div class="form-group">
                            <label class="font-weight-bold small">Dirección SM-DP+:</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="smdpInput" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-brand-gradient copy-button" onclick="copiarTexto('smdpInput')">Copiar</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold small">Código de Activación:</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" id="codeInput" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-brand-gradient copy-button" onclick="copiarTexto('codeInput')">Copiar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="location.reload()">Comprar Otro Plan</button>
                <button type="button" class="btn btn-brand-gradient" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Stripe.js --}}
<script src="https://js.stripe.com/v3/"></script>

<script>
    // Configuración
    const STRIPE_PUBLIC_KEY = '{{ config("services.stripe.public") }}';
    const stripe = Stripe(STRIPE_PUBLIC_KEY);
    const elements = stripe.elements();
    let cardElement = null;
    let selectedPlan = null;

    // Función para mostrar/ocultar loading
    function toggleLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    }

    // Función para copiar texto
    function copiarTexto(id) {
        const input = document.getElementById(id);
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        // Feedback visual
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = '¡Copiado!';
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    }

    // Cargar planes por país
    document.getElementById('countrySelector').addEventListener('change', function() {
        const country = this.value;
        if (!country) {
            document.getElementById('planesContainer').innerHTML = `
                <div class="col-12 empty-state">
                    <i class="mdi mdi-earth"></i>
                    <h5>Selecciona un país para ver los planes disponibles</h5>
                </div>
            `;
            return;
        }

        toggleLoading(true);

        fetch('{{ route("planes.get") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ country: country })
        })
        .then(response => response.json())
        .then(data => {
            toggleLoading(false);
            if (data.success) {
                renderizarPlanes(data.data);
            } else {
                alert('Error al cargar los planes');
            }
        })
        .catch(error => {
            toggleLoading(false);
            console.error('Error:', error);
            alert('Error al cargar los planes');
        });
    });

    // Renderizar planes
    function renderizarPlanes(planes) {
        const container = document.getElementById('planesContainer');
        
        if (planes.length === 0) {
            container.innerHTML = `
                <div class="col-12 empty-state">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <h5>No hay planes disponibles para este país</h5>
                </div>
            `;
            return;
        }

        let html = '';
        planes.forEach(plan => {
            const precio = plan.price === 0 
                ? '<span style="color: #28a745;">FREE</span>' 
                : `$${plan.price} ${plan.price_unit}`;
            
            html += `
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="plan-card">
                        <div class="plan-duration">${plan.duration} ${plan.duration_unit}s</div>
                        <div class="plan-data">${plan.amount} ${plan.amount_unit}</div>
                        <div class="plan-price ${plan.price > 0 ? 'paid' : ''}">${precio}</div>
                        <button class="btn btn-brand-gradient btn-block" onclick="iniciarCompra(${JSON.stringify(plan).replace(/"/g, '&quot;')})">
                            Comprar
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Iniciar proceso de compra
    async function iniciarCompra(plan) {
        selectedPlan = plan;

        // Verificar autenticación
        try {
            const response = await fetch('{{ route("planes.verificar-auth") }}');
            const data = await response.json();

            if (!data.authenticated) {
                $('#authModal').modal('show');
            } else {
                mostrarCheckout(plan);
            }
        } catch (error) {
            console.error('Error:', error);
            $('#authModal').modal('show');
        }
    }

    // Mostrar modal de checkout
    function mostrarCheckout(plan) {
        const detailsHtml = `
            <div class="alert alert-info">
                <strong>Plan Seleccionado:</strong><br>
                ${plan.name}<br>
                <strong>Datos:</strong> ${plan.amount} ${plan.amount_unit}<br>
                <strong>Duración:</strong> ${plan.duration} ${plan.duration_unit}s<br>
                <strong>Precio:</strong> ${plan.price === 0 ? 'GRATIS' : '$' + plan.price + ' ' + plan.price_unit}
            </div>
        `;
        document.getElementById('planDetails').innerHTML = detailsHtml;

        if (plan.price === 0) {
            document.getElementById('paymentSection').style.display = 'none';
            document.getElementById('freeSection').style.display = 'block';
        } else {
            document.getElementById('paymentSection').style.display = 'block';
            document.getElementById('freeSection').style.display = 'none';
            
            // Crear Stripe card element
            if (!cardElement) {
                cardElement = elements.create('card');
                cardElement.mount('#card-element');
                
                cardElement.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }
        }

        $('#authModal').modal('hide');
        $('#checkoutModal').modal('show');
    }

    // Tabs del modal de autenticación
    document.querySelectorAll('.modal-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab + 'Tab').classList.add('active');
        });
    });

    // Login form
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleLoading(true);

        const formData = new FormData(this);
        formData.append('tipo', 'login');

        try {
            const response = await fetch('{{ route("planes.auth") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            toggleLoading(false);

            if (data.success) {
                $('#authModal').modal('hide');
                mostrarCheckout(selectedPlan);
            } else {
                alert(data.message || 'Error en el login');
            }
        } catch (error) {
            toggleLoading(false);
            console.error('Error:', error);
            alert('Error en el login');
        }
    });

    // Registro form
    document.getElementById('registroForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleLoading(true);

        const formData = new FormData(this);
        formData.append('tipo', 'registro');

        try {
            const response = await fetch('{{ route("planes.auth") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            toggleLoading(false);

            if (data.success) {
                $('#authModal').modal('hide');
                mostrarCheckout(selectedPlan);
            } else {
                alert(data.message || 'Error en el registro');
            }
        } catch (error) {
            toggleLoading(false);
            console.error('Error:', error);
            alert('Error en el registro');
        }
    });

    // Payment form
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleLoading(true);

        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement
        });

        if (error) {
            toggleLoading(false);
            document.getElementById('card-errors').textContent = error.message;
            return;
        }

        procesarPago(paymentMethod.id);
    });

    // Free plan button
    document.getElementById('submitFree').addEventListener('click', function() {
        procesarPago(null);
    });

    // Procesar pago
    async function procesarPago(paymentMethodId) {
        try {
            const response = await fetch('{{ route("planes.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: selectedPlan.id,
                    product_name: selectedPlan.name,
                    amount: selectedPlan.price,
                    payment_method_id: paymentMethodId
                })
            });

            const data = await response.json();
            toggleLoading(false);

            if (data.success) {
                mostrarResultado(data.data);
            } else {
                alert(data.message || 'Error al procesar el pago');
            }
        } catch (error) {
            toggleLoading(false);
            console.error('Error:', error);
            alert('Error al procesar el pago');
        }
    }

    // Mostrar resultado con QR
    function mostrarResultado(data) {
        document.getElementById('qrCodeContainer').innerHTML = data.qr_svg;
        document.getElementById('smdpInput').value = data.smdp;
        document.getElementById('codeInput').value = data.code;

        $('#checkoutModal').modal('hide');
        $('#resultModal').modal('show');
    }
</script>

@endsection
