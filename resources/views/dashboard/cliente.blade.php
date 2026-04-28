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
                                            <button type="button" class="btn btn-success btn-lg" onclick="activarEsimDesdeDashboard('{{ e($active_plan->esim_qr) }}')">
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

        <!-- eSIM Plans Purchase Section -->
        @php
            $countryAutocompleteOptions = collect($allCountries ?? [])->map(function ($country) {
                return [
                    'code' => $country['code'],
                    'name' => $country['name'],
                    'emoji' => \App\Helpers\CountryTariffHelper::getCountryEmoji($country['code']),
                ];
            })->values()->all();
        @endphp
        <div class="row" id="cliente-dashboard-planes">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Comprar Plan eSIM</h4>
                        <p class="card-description">Selecciona un país y elige tu plan de 3GB, 5GB o 10GB</p>

                        <div id="cliente-dashboard-planes-app" data-initial-country="{{ $initialCountry ?? '' }}">

                            {{-- Selector de país --}}
                            <div class="country-selector">
                                <div class="country-autocomplete">
                                    <input type="text"
                                           class="form-control form-control-lg country-search-input"
                                           id="dashboard-country-autocomplete"
                                           v-model="countryAutocomplete"
                                           @input="handleCountryAutocompleteInput"
                                           @focus="openCountrySuggestions"
                                           @keydown.down.prevent="moveCountrySuggestion(1)"
                                           @keydown.up.prevent="moveCountrySuggestion(-1)"
                                           @keydown.enter.prevent="confirmActiveCountrySuggestion"
                                           @keydown.esc="hideCountrySuggestions"
                                           placeholder="Escribe y selecciona un país"
                                           autocomplete="off">
                                    <div v-if="showCountrySuggestions" class="country-suggestions" role="listbox" aria-label="Paises sugeridos">
                                        <template v-if="filteredCountryOptions.length">
                                            <button v-for="(country, index) in filteredCountryOptions"
                                                    :key="country.code"
                                                    type="button"
                                                    class="country-suggestion-item"
                                                    :class="{ 'is-active': index === activeCountrySuggestionIndex }"
                                                    @mousedown.prevent="selectCountrySuggestion(country)">
                                                <span>@{{ country.emoji || '🌍' }}</span>
                                                <span>@{{ country.name }}</span>
                                            </button>
                                        </template>
                                        <div v-else class="country-suggestion-empty">No encontramos paises con ese criterio.</div>
                                    </div>
                                </div>
                                <div v-if="selectedCountry" class="country-current-selection">
                                    <strong>@{{ displayedCountryName }}</strong>
                                    <small>Los precios mostrados corresponden a este destino.</small>
                                </div>
                                <small v-if="hasPendingCountrySelection" class="country-selection-hint">Selecciona una opción de la lista para actualizar los precios.</small>
                                <small class="form-text text-muted mt-2">Empieza a escribir para autocompletar.</small>
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
        </div>

        {{-- Modal de Pago con Stripe (dashboard) --}}
        <div class="modal fade" id="paymentModal-dashboard" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(90deg, #181c36 0%, #623b86 100%); color: white;">
                        <h5 class="modal-title">Confirmar Pago</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="dashboard-payment-modal-body">
                        <div v-if="selectedPlan">
                            <h5>Plan seleccionado:</h5>
                            <p>
                                <strong>@{{ selectedPlan.amount }}@{{ selectedPlan.amount_unit }}</strong> -
                                @{{ selectedPlan.duration }} @{{ formatDurationUnit(selectedPlan.duration_unit) }}
                            </p>
                            <h4 class="mb-4">Total: @{{ selectedPlan.price }} @{{ selectedPlan.price_unit }}</h4>

                            <div id="card-element-dashboard" class="form-control mb-3" style="padding: 12px;"></div>
                            <div id="card-errors-dashboard" class="text-danger mb-3"></div>

                            <button @click="processPayment" class="btn btn-buy" :disabled="paymentProcessing">
                                <span v-if="paymentProcessing">Procesando...</span>
                                <span v-else>Pagar @{{ selectedPlan.price }} @{{ selectedPlan.price_unit }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de Éxito con QR (dashboard) --}}
        <div class="modal fade success-modal" id="successModal-dashboard" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body p-5">
                        <div class="success-icon">
                            <i class="mdi mdi-check-circle"></i>
                        </div>
                        <h3>¡Pago Exitoso!</h3>
                        <p class="mb-4">Tu eSIM ha sido activada correctamente</p>

                        <div v-if="esimData" class="text-center">
                            <div class="qr-code-container" v-html="esimData.qr_svg"></div>

                            <div class="text-left mt-4 p-3 bg-light rounded">
                                <h5 class="mb-3">Instalación Manual</h5>
                                <div class="form-group">
                                    <label class="font-weight-bold">SM-DP+ Address:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" :value="esimData.smdp" readonly id="smdp-input-dashboard">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" @click="copyToClipboard('smdp-input-dashboard')">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">ICCID:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" :value="esimData.iccid" readonly id="iccid-input-dashboard">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" @click="copyToClipboard('iccid-input-dashboard')">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Código de Activación:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" :value="esimData.code" readonly id="code-input-dashboard">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" @click="copyToClipboard('code-input-dashboard')">Copiar</button>
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
                                                                            <button type="button" class="btn btn-success" onclick="activarEsimDesdeDashboard('{{ e($transaction->esim_qr) }}')">
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
    /* CSS variables for eSIM plans section */
    :root {
        --xcertus-purple: #623b86;
        --xcertus-yellow: #ffcc00;
        --nomad-blue: #2d9cdb;
        --nomad-navy: #181c36;
    }

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

    /* Country selector */
    #cliente-dashboard-planes-app .country-selector {
        max-width: 540px;
        margin: 0 auto 30px;
    }
    #cliente-dashboard-planes-app .country-autocomplete {
        position: relative;
    }
    #cliente-dashboard-planes-app .country-suggestions {
        position: absolute;
        top: calc(100% - 10px);
        left: 0;
        right: 0;
        z-index: 30;
        background: #fff;
        border: 1px solid rgba(24, 28, 54, 0.12);
        border-radius: 14px;
        box-shadow: 0 18px 28px rgba(24, 28, 54, 0.12);
        max-height: 260px;
        overflow-y: auto;
        padding: 8px;
    }
    #cliente-dashboard-planes-app .country-suggestion-item {
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--nomad-navy);
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    #cliente-dashboard-planes-app .country-suggestion-item:hover,
    #cliente-dashboard-planes-app .country-suggestion-item.is-active {
        background: rgba(45, 156, 219, 0.12);
        color: var(--xcertus-purple);
        outline: none;
    }
    #cliente-dashboard-planes-app .country-suggestion-empty {
        padding: 10px 12px;
        color: rgba(24, 28, 54, 0.62);
        font-size: 0.92rem;
    }
    #cliente-dashboard-planes-app .country-current-selection {
        margin-top: 12px;
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(45, 156, 219, 0.08);
        color: var(--nomad-navy);
    }
    #cliente-dashboard-planes-app .country-current-selection strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 4px;
    }
    #cliente-dashboard-planes-app .country-selection-hint {
        display: block;
        color: rgba(24, 28, 54, 0.7);
        line-height: 1.5;
        margin-top: 10px;
        font-size: 0.88rem;
    }
    #cliente-dashboard-planes-app .country-selector .form-control {
        border: 2px solid var(--nomad-blue);
        font-size: 1.1rem;
        padding: 12px;
    }
    #cliente-dashboard-planes-app .country-selector .form-control:focus {
        border-color: var(--xcertus-purple);
        box-shadow: 0 0 0 0.2rem rgba(98, 59, 134, 0.25);
    }

    /* Plans grid */
    #cliente-dashboard-planes-app .plans-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 30px;
    }
    @media (max-width: 1200px) {
        #cliente-dashboard-planes-app .plans-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        #cliente-dashboard-planes-app .plans-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        #cliente-dashboard-planes-app .plans-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Plan card */
    #cliente-dashboard-planes-app .plan-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    #cliente-dashboard-planes-app .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(45, 156, 219, 0.3);
        border-color: var(--nomad-blue);
    }
    #cliente-dashboard-planes-app .plan-card.free-plan {
        border-color: #28a745;
    }
    #cliente-dashboard-planes-app .plan-card.free-plan::before {
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
    #cliente-dashboard-planes-app .plan-duration {
        color: var(--nomad-navy);
        font-size: 0.95rem;
        margin-bottom: 8px;
        font-weight: 600;
    }
    #cliente-dashboard-planes-app .plan-data {
        color: var(--xcertus-purple);
        font-size: 2rem;
        font-weight: bold;
        margin: 10px 0;
    }
    #cliente-dashboard-planes-app .plan-price {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 15px 0;
    }
    #cliente-dashboard-planes-app .plan-price.free {
        color: #28a745;
    }
    #cliente-dashboard-planes-app .plan-price.paid {
        color: var(--nomad-navy);
    }
    #cliente-dashboard-planes-app .btn-buy {
        background: linear-gradient(90deg, var(--nomad-navy) 0%, var(--xcertus-purple) 100%);
        border: none;
        color: white;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s;
    }
    #cliente-dashboard-planes-app .btn-buy:hover {
        opacity: 0.9;
        transform: scale(1.02);
        color: white;
    }
    #cliente-dashboard-planes-app .loading-spinner {
        text-align: center;
        padding: 40px;
    }
    #cliente-dashboard-planes-app .loading-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
        color: var(--nomad-blue);
    }
    #cliente-dashboard-planes-app .no-plans {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 1.1rem;
    }

    /* Success modal QR container */
    #successModal-dashboard .qr-code-container {
        padding: 20px;
        background: white;
        border: 3px solid var(--xcertus-yellow);
        border-radius: 10px;
        display: inline-block;
        margin: 20px auto;
    }
    #successModal-dashboard .success-icon {
        font-size: 4rem;
        color: #28a745;
        margin: 20px 0;
    }
    #successModal-dashboard .modal-content {
        border-radius: 15px;
        text-align: center;
    }
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script type="application/json" id="dashboard-country-options-json">{!! json_encode($countryAutocompleteOptions ?? []) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const appElement = document.getElementById('cliente-dashboard-planes-app');
    const countryOptionsElement = document.getElementById('dashboard-country-options-json');
    const initialCountry = appElement ? appElement.dataset.initialCountry || '' : '';
    const countryOptions = countryOptionsElement ? JSON.parse(countryOptionsElement.textContent || '[]') : [];

    new Vue({
        el: '#cliente-dashboard-planes-app',
        data: {
            selectedCountry: '',
            selectedCountryLabel: '',
            countryAutocomplete: '',
            showCountrySuggestions: false,
            activeCountrySuggestionIndex: -1,
            plans: [],
            loading: false,
            selectedPlan: null,
            paymentProcessing: false,
            esimData: null,
            stripe: null,
            cardElement: null,
            paymentIntentId: null,
            errorMessage: ''
        },
        computed: {
            displayedCountryName() {
                return this.selectedCountryLabel || this.countryAutocomplete || '';
            },
            hasPendingCountrySelection() {
                const typedValue = (this.countryAutocomplete || '').trim().toLowerCase();
                const selectedLabel = (this.selectedCountryLabel || '').trim().toLowerCase();
                return !!typedValue && typedValue !== selectedLabel;
            },
            filteredCountryOptions() {
                const typedValue = (this.countryAutocomplete || '').trim().toLowerCase();
                if (!typedValue) {
                    return countryOptions.slice(0, 8);
                }
                return countryOptions.filter(function(option) {
                    return (option.name || '').toLowerCase().includes(typedValue)
                        || (option.code || '').toLowerCase().includes(typedValue);
                }).slice(0, 8);
            }
        },
        mounted() {
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

            if (initialCountry) {
                const initialOption = countryOptions.find(function(o) { return o.code === initialCountry; });
                this.selectedCountry = initialCountry;
                this.selectedCountryLabel = initialOption ? initialOption.name : initialCountry;
                this.countryAutocomplete = this.selectedCountryLabel;
                this.loadPlans();
            }

            document.addEventListener('click', this.handleOutsideCountryClick);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleOutsideCountryClick);
        },
        methods: {
            openCountrySuggestions() {
                this.showCountrySuggestions = true;
                this.activeCountrySuggestionIndex = -1;
            },
            hideCountrySuggestions() {
                this.showCountrySuggestions = false;
                this.activeCountrySuggestionIndex = -1;
            },
            handleCountryAutocompleteInput() {
                this.showCountrySuggestions = true;
                this.activeCountrySuggestionIndex = -1;
            },
            moveCountrySuggestion(direction) {
                if (!this.showCountrySuggestions) {
                    this.showCountrySuggestions = true;
                }
                if (!this.filteredCountryOptions.length) {
                    return;
                }
                const nextIndex = this.activeCountrySuggestionIndex + direction;
                const maxIndex = this.filteredCountryOptions.length - 1;
                if (nextIndex < 0) {
                    this.activeCountrySuggestionIndex = 0;
                    return;
                }
                if (nextIndex > maxIndex) {
                    this.activeCountrySuggestionIndex = maxIndex;
                    return;
                }
                this.activeCountrySuggestionIndex = nextIndex;
            },
            confirmActiveCountrySuggestion() {
                if (!this.showCountrySuggestions || !this.filteredCountryOptions.length) {
                    const exactMatch = countryOptions.find((option) => {
                        return (option.name || '').trim().toLowerCase() === (this.countryAutocomplete || '').trim().toLowerCase();
                    });
                    if (exactMatch) {
                        this.selectCountrySuggestion(exactMatch);
                    }
                    return;
                }
                const option = this.filteredCountryOptions[this.activeCountrySuggestionIndex >= 0 ? this.activeCountrySuggestionIndex : 0];
                this.selectCountrySuggestion(option);
            },
            selectCountrySuggestion(country) {
                if (!country) {
                    return;
                }
                this.countryAutocomplete = country.name;
                this.selectedCountryLabel = country.name;
                if (this.selectedCountry === country.code) {
                    this.hideCountrySuggestions();
                    return;
                }
                this.selectedCountry = country.code;
                this.hideCountrySuggestions();
                this.loadPlans();
            },
            handleOutsideCountryClick(event) {
                if (!event.target.closest('#cliente-dashboard-planes-app .country-autocomplete')) {
                    this.hideCountrySuggestions();
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
                    this.processFreeActivation();
                } else {
                    this.openPaymentModal(plan);
                }
            },
            openPaymentModal(plan) {
                $('#paymentModal-dashboard').modal('show');
                setTimeout(() => {
                    const cardEl = document.querySelector('#card-element-dashboard');
                    if (cardEl && !cardEl.hasChildNodes()) {
                        this.cardElement.mount('#card-element-dashboard');
                    }
                }, 300);
            },
            async processPayment() {
                if (!this.selectedPlan || this.paymentProcessing) return;
                this.paymentProcessing = true;
                try {
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
                    const { error, paymentIntent } = await this.stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: this.cardElement
                        }
                    });
                    if (error) {
                        throw new Error(error.message);
                    }
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
                        $('#paymentModal-dashboard').modal('hide');
                        $('#successModal-dashboard').modal('show');
                    }
                } catch (error) {
                    this.showErrorMessage('Error procesando el pago: ' + (error.message || error));
                    console.error('Payment error:', error);
                } finally {
                    this.paymentProcessing = false;
                }
            },
            async processFreeActivation() {
                try {
                    this.loading = true;
                    const response = await axios.post('/planes/activar-gratis', {
                        product_id: this.selectedPlan.id,
                        plan_name: this.selectedPlan.name,
                        data_amount: this.selectedPlan.amount,
                        duration: this.selectedPlan.duration,
                        original_price: this.selectedPlan.original_price,
                    });
                    if (response.data.success) {
                        this.esimData = response.data.esim_data;
                        $('#successModal-dashboard').modal('show');
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
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(input.value)
                        .then(() => {
                            this.showSuccessMessage('Copiado al portapapeles');
                        })
                        .catch(err => {
                            this.fallbackCopyToClipboard(input);
                        });
                } else {
                    this.fallbackCopyToClipboard(input);
                }
            },
            fallbackCopyToClipboard(input) {
                input.select();
                input.setSelectionRange(0, 99999);
                try {
                    document.execCommand('copy');
                    this.showSuccessMessage('Copiado al portapapeles');
                } catch (err) {
                    console.error('Error copiando:', err);
                }
            },
            showErrorMessage(message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: message });
                } else {
                    alert(message);
                }
            },
            showSuccessMessage(message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Éxito', text: message, timer: 2000, showConfirmButton: false });
                } else {
                    alert(message);
                }
            }
        }
    });
});
</script>
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
