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
                
                <div class="form-group mb-4">
                    <div class="alert alert-info">
                        <strong>{{ $t('beneficiary_profit_margin_configuration') }}</strong>
                        <p class="mb-0 mt-2">{{ $t('configure_beneficiary_margins_explanation') }}</p>
                        <small class="text-muted">{{ $t('formula_explanation') }}: <strong>Precio Final = Precio Base Admin / (1 - Margen Beneficiario)</strong></small>
                    </div>
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
                            <tr v-for="plan in planCapacities" :key="plan">
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
                planCapacities: ['1', '3', '5', '10', '20', '50'],
                margins: {
                    '1': { margin_percentage: 0.00, is_active: true },
                    '3': { margin_percentage: 0.00, is_active: true },
                    '5': { margin_percentage: 0.00, is_active: true },
                    '10': { margin_percentage: 0.00, is_active: true },
                    '20': { margin_percentage: 0.00, is_active: true },
                    '50': { margin_percentage: 0.00, is_active: true },
                },
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
                    params: {
                        beneficiario_id: this.beneficiarioId
                    }
                })
                .then(response => {
                    if (response.data && response.data.margins) {
                        Object.keys(response.data.margins).forEach(plan => {
                            if (this.margins[plan]) {
                                this.margins[plan] = {
                                    ...this.margins[plan],
                                    ...response.data.margins[plan]
                                };
                            }
                        });
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
                    margins: this.margins 
                };

                axios.post(actions.UPDATE_BENEFICIARY_PLAN_MARGINS, data)
                .then(response => {
                    this.$toastr.s(response.data.message || this.$t('margins_updated_successfully'));
                    if (response.data && response.data.margins) {
                        Object.keys(response.data.margins).forEach(plan => {
                            if (this.margins[plan]) {
                                this.margins[plan] = {
                                    ...this.margins[plan],
                                    ...response.data.margins[plan]
                                };
                            }
                        });
                    }
                    setTimeout(() => {
                        this.closeModal();
                    }, 1000);
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
                    this.planCapacities.forEach(plan => {
                        this.margins[plan].margin_percentage = 0.00;
                    });
                    this.$toastr.i(this.$t('margins_reset_to_defaults'));
                }
            },

            calculateExample(marginPercentage) {
                const adminPrice = 100;
                const marginDecimal = parseFloat(marginPercentage) / 100;
                
                if (marginDecimal >= 1) {
                    return 'N/A';
                }
                
                const finalPrice = adminPrice / (1 - marginDecimal);
                return finalPrice.toFixed(2);
            },

            closeModal() {
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>
    .input-group {
        min-width: 150px;
    }
</style>