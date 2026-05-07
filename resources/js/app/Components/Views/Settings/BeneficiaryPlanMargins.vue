<template>
    <app-modal 
        modal-id="beneficiary-plan-margins-modal"
        modal-size="large"
        @close-modal="closeModal">
        
        <template slot="header">
            <h5 class="modal-title">{{ $t('beneficiary_plan_margins') }} - {{ beneficiarioName }}</h5>
            <button type="button" class="close outline-none" @click.prevent="closeModal">
                <span>×</span>
            </button>
        </template>

        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form v-else ref="form" class="mb-0" :class="{'loading-opacity': preloader}">

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'margins'}" href="#" @click.prevent="activeTab = 'margins'">
                            Comisiones (%)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'prices'}" href="#" @click.prevent="activeTab = 'prices'">
                            Precios Fijos (USD)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'country'}" href="#" @click.prevent="activeTab = 'country'">
                            Porcentaje por País
                        </a>
                    </li>
                </ul>

                <!-- Tab: Margins -->
                <div v-show="activeTab === 'margins'">
                    <div class="form-group mb-4">
                        <div class="alert alert-info">
                            <strong>{{ $t('beneficiary_profit_margin_configuration') }}</strong>
                            <p class="mb-0 mt-2">{{ $t('configure_beneficiary_margins_explanation') }}</p>
                            <small class="text-muted">{{ $t('formula_explanation') }}: <strong>Precio Final = Precio Base Admin / (1 - Margen Beneficiario)</strong></small>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="d-block mb-1">Tarifa eSIM Gratuita 1GB (USD)</label>
                        <div class="input-group" style="max-width: 200px;">
                            <app-input
                                type="number"
                                v-model="freeEsimRate"
                                :min="0"
                                step="0.01"
                                :required="true"/>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Monto a cobrar por cada eSIM gratuita de 1GB para este beneficiario (cuando no hay precio fijo configurado).
                        </small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>{{ $t('plan_capacity') }}</th>
                                    <th>{{ $t('margin_percentage') }}</th>
                                    <th>{{ $t('example_calculation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in marginCapacities" :key="plan">
                                    <td class="align-middle">
                                        <strong>{{ plan }}GB</strong>
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 200px;">
                                            <app-input 
                                                type="number"
                                                v-model="margins[plan].margin_percentage"
                                                :placeholder="$t('enter_margin')"
                                                :min="0"
                                                :max="100"
                                                step="0.01"
                                                :required="true"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-muted">
                                        <small>
                                            {{ $t('if_admin_price') }} $100, {{ $t('final_price') }}: ${{ calculateExample(margins[plan].margin_percentage) }}
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Fixed Plan Prices -->
                <div v-show="activeTab === 'prices'">
                    <div class="alert alert-warning">
                        <strong>Precios Fijos por Plan</strong>
                        <p class="mb-0 mt-2">
                            Si asignas un precio fijo para un plan, ese monto se usará directamente al cobrar por una eSIM gratuita de esa capacidad, 
                            ignorando el cálculo de porcentaje. Deja en blanco para seguir usando el sistema de porcentajes.
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Precio Fijo (USD)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in allCapacities" :key="plan">
                                    <td class="align-middle"><strong>{{ plan }}GB</strong></td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 180px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <app-input
                                                type="number"
                                                v-model="planPrices[plan].price"
                                                :placeholder="'Sin precio fijo'"
                                                :min="0"
                                                step="0.01"/>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="clearPlanPrice(plan)">
                                            <app-icon name="x" style="width:14px;height:14px;"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Country-Specific Prices -->
                <div v-show="activeTab === 'country'">
                    <div class="alert alert-info">
                        <strong>Porcentaje por País</strong>
                        <p class="mb-0 mt-2">
                            Estos porcentajes tienen la mayor prioridad. Si existe un porcentaje configurado para un país y plan específico,
                            se usará ese porcentaje para calcular el precio final del partner sobre el precio con margen del admin,
                            ignorando el margen general del partner para ese caso.
                        </p>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>País (Código)</th>
                                    <th>Plan</th>
                                    <th>Porcentaje (%)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(entry, idx) in countryPrices" :key="idx">
                                    <td class="align-middle">
                                        <app-input type="text"
                                                   v-model="entry.country_code"
                                                   :placeholder="'US'"
                                                   style="max-width: 80px;"
                                                   :maxlength="2"/>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-control form-control-sm" v-model="entry.plan_capacity" style="max-width: 100px;">
                                            <option v-for="cap in allCapacities" :key="cap" :value="cap">{{ cap }}GB</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 150px;">
                                            <app-input type="number"
                                                       v-model="entry.percentage"
                                                       :min="0"
                                                       :max="100"
                                                       step="0.01"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeCountryPrice(idx)">
                                            <app-icon name="trash-2" style="width:14px;height:14px;"/>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="countryPrices.length === 0">
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No hay porcentajes por país configurados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addCountryPrice">
                        <app-icon name="plus" style="width:14px;height:14px;" class="mr-1"/> Agregar porcentaje por país
                    </button>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-secondary" @click.prevent="resetToDefaults">
                        {{ $t('reset_to_defaults') }}
                    </button>
                    <div>
                        <button class="btn btn-light mr-2" @click.prevent="closeModal">
                            {{ $t('cancel') }}
                        </button>
                        <button class="btn btn-primary" @click.prevent="submit">
                            {{ $t('save') }}
                        </button>
                    </div>
                </div>
            </form>
        </template>
    </app-modal>
</template>

<script>
    import axios from 'axios';
    import {FormMixin} from "../../../../core/mixins/form/FormMixin";
    import {ModalMixin} from "../../../Mixins/ModalMixin";
    import * as actions from "../../../Config/ApiUrl";

    export default {
        name: "BeneficiaryPlanMarginsModal",
        mixins: [FormMixin, ModalMixin],
        props: {
            beneficiarioId: {
                type: Number,
                required: true
            },
            beneficiarioName: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                activeTab: 'margins',
                allCapacities: ['1', '3', '5', '10'],
                marginCapacities: ['3', '5', '10'],
                margins: {
                    '3': { margin_percentage: 0.00, is_active: true },
                    '5': { margin_percentage: 0.00, is_active: true },
                    '10': { margin_percentage: 0.00, is_active: true },
                },
                planPrices: {
                    '1': { price: '', is_active: true },
                    '3': { price: '', is_active: true },
                    '5': { price: '', is_active: true },
                    '10': { price: '', is_active: true },
                },
                countryPrices: [],
                freeEsimRate: 0.85,
                preloader: false,
            }
        },
        mounted() {
            this.getMargins();
        },
        methods: {
            getMargins() {
                this.preloader = true;
                axios.get(actions.GET_BENEFICIARY_PLAN_MARGINS, {
                    params: { beneficiario_id: this.beneficiarioId }
                })
                .then(response => {
                    if (response.data && response.data.margins) {
                        Object.keys(response.data.margins).forEach(plan => {
                            if (this.margins[plan]) {
                                this.margins[plan] = { ...this.margins[plan], ...response.data.margins[plan] };
                            }
                        });
                    }
                    if (response.data && typeof response.data.free_esim_rate !== 'undefined') {
                        this.freeEsimRate = parseFloat(response.data.free_esim_rate) || 0.85;
                    }
                    if (response.data && response.data.plan_prices) {
                        Object.keys(response.data.plan_prices).forEach(plan => {
                            if (this.planPrices[plan]) {
                                this.planPrices[plan] = { ...this.planPrices[plan], ...response.data.plan_prices[plan] };
                            }
                        });
                    }
                    if (response.data && response.data.country_prices) {
                        this.countryPrices = response.data.country_prices.map(item => ({ ...item }));
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.message || this.$t('error_loading_margins');
                    this.$toastr.e(message);
                })
                .finally(() => {
                    this.preloader = false;
                });
            },

            submit() {
                this.preloader = true;
                const data = {
                    beneficiario_id: this.beneficiarioId,
                    margins: this.margins,
                    free_esim_rate: this.freeEsimRate,
                    plan_prices: this.planPrices,
                    country_prices: this.countryPrices.filter(e => e.country_code && e.plan_capacity && e.percentage !== '' && e.percentage !== null),
                };

                axios.post(actions.UPDATE_BENEFICIARY_PLAN_MARGINS, data)
                .then(response => {
                    this.$toastr.s(response.data.message || this.$t('margins_updated_successfully'));
                    if (response.data && response.data.margins) {
                        Object.keys(response.data.margins).forEach(plan => {
                            if (this.margins[plan]) {
                                this.margins[plan] = { ...this.margins[plan], ...response.data.margins[plan] };
                            }
                        });
                    }
                    if (response.data && typeof response.data.free_esim_rate !== 'undefined') {
                        this.freeEsimRate = parseFloat(response.data.free_esim_rate) || this.freeEsimRate;
                    }
                    if (response.data && response.data.plan_prices) {
                        Object.keys(response.data.plan_prices).forEach(plan => {
                            if (this.planPrices[plan]) {
                                this.planPrices[plan] = { ...this.planPrices[plan], ...response.data.plan_prices[plan] };
                            }
                        });
                    }
                    if (response.data && response.data.country_prices) {
                        this.countryPrices = response.data.country_prices.map(item => ({ ...item }));
                    }
                    setTimeout(() => { this.closeModal(); }, 1000);
                })
                .catch(error => {
                    const message = error.response?.data?.message || this.$t('error_updating_margins');
                    this.$toastr.e(message);
                })
                .finally(() => {
                    this.preloader = false;
                });
            },

            resetToDefaults() {
                if (confirm(this.$t('confirm_reset_beneficiary_margins'))) {
                    this.marginCapacities.forEach(plan => {
                        this.margins[plan].margin_percentage = 0.00;
                    });
                    this.allCapacities.forEach(plan => {
                        this.planPrices[plan].price = '';
                    });
                    this.freeEsimRate = 0.85;
                    this.$toastr.i(this.$t('margins_reset_to_defaults'));
                }
            },

            calculateExample(marginPercentage) {
                const adminPrice = 100;
                const marginDecimal = parseFloat(marginPercentage) / 100;
                if (marginDecimal >= 1) return 'N/A';
                return (adminPrice / (1 - marginDecimal)).toFixed(2);
            },

            clearPlanPrice(plan) {
                this.planPrices[plan].price = '';
            },

            addCountryPrice() {
                this.countryPrices.push({ country_code: '', plan_capacity: '1', percentage: '' });
            },

            removeCountryPrice(idx) {
                this.countryPrices.splice(idx, 1);
            },

            closeModal() {
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>
    .input-group {
        min-width: 100px;
    }
</style>
