@extends('auth-layouts.auth')

@section('title', 'Planes Disponibles - eSIM Internacional')

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
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .logo-nomad {
        height: 50px;
        object-fit: contain;
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
</style>

<div id="planes-disponibles-app" class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-start auth px-0 py-5">
            <div class="row w-100 mx-0">
                <div class="col-lg-11 col-xl-10 mx-auto">
                    
                    {{-- Header con logo --}}
                    <div class="brand-alliance-container">
                        <img src="{{ asset('images/nomadesim.png') }}" alt="Nomad eSIM" class="logo-nomad">
                    </div>

                    <h2 class="page-title">Planes eSIM Disponibles</h2>
                    
                    {{-- Selector de país --}}
                    <div class="country-selector">
                        <select class="form-control form-control-lg" v-model="selectedCountry" @change="loadPlans">
                            <option value="">Seleccione un país</option>
                            <option value="ES">España</option>
                            <option value="US">Estados Unidos</option>
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
            paymentIntentId: null
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
                    alert('Error al cargar los planes disponibles');
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
                        payment_intent_id: this.paymentIntentId
                    });

                    if (activationResponse.data.success) {
                        this.esimData = activationResponse.data.esim_data;
                        $('#paymentModal').modal('hide');
                        $('#successModal').modal('show');
                    }
                } catch (error) {
                    alert('Error procesando el pago: ' + (error.message || error));
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
                    // Procesar activación directa sin pago
                    // Aquí deberías tener un endpoint específico para planes gratuitos
                    alert('Activación de planes gratuitos en desarrollo');
                } catch (error) {
                    alert('Error activando el plan gratuito');
                }
            },
            copyToClipboard(inputId) {
                const input = document.getElementById(inputId);
                input.select();
                document.execCommand('copy');
                alert('Copiado al portapapeles');
            }
        }
    });
});
</script>
@endsection
