<template>
    <div class="plan-margins-setting">
        <app-overlay-loader v-if="preloader"/>
        <form v-else ref="form" data-url="/admin/app/settings/plan-margins"
              class="mb-0"
              :class="{'loading-opacity': preloader}">
            
            <!-- Introduction -->
            <div class="form-group mb-4">
                <div class="alert alert-info">
                    <strong>{{ $t('profit_margin_configuration') }}</strong>
                    <p class="mb-0 mt-2">{{ $t('configure_profit_margins_for_esim_plans') }}</p>
                    <small class="text-muted">{{ $t('formula_explanation') }}: <strong>Precio Final = Coste / (1 - Margen)</strong></small>
                </div>
            </div>

            <!-- Plan Margins Table -->
            <div class="table-responsive">
                <table class="table table-hover">
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
                                    {{ $t('if_cost') }} $100, {{ $t('final_price') }}: ${{ calculateExample(margins[plan].margin_percentage) }}
                                </small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="mt-5 action-buttons">
                <button class="btn btn-primary mr-2" @click.prevent="submit">
                    {{ $t('save') }}
                </button>
                <button class="btn btn-secondary" @click.prevent="resetToDefaults">
                    {{ $t('reset_to_defaults') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import {FormMixin} from "../../../../core/mixins/form/FormMixin";
    import * as actions from "../../../Config/ApiUrl";

    export default {
        name: "PlanMargins",
        mixins: [FormMixin],
        data() {
            return {
                planCapacities: ['1', '3', '5', '10', '20', '50'],
                margins: {
                    '1': { margin_percentage: 30.00, is_active: true },
                    '3': { margin_percentage: 30.00, is_active: true },
                    '5': { margin_percentage: 30.00, is_active: true },
                    '10': { margin_percentage: 30.00, is_active: true },
                    '20': { margin_percentage: 30.00, is_active: true },
                    '50': { margin_percentage: 30.00, is_active: true },
                },
                preloader: false,
            }
        },
        created() {
            this.getMargins();
        },
        methods: {
            /**
             * Get current margin configuration from API
             */
            getMargins() {
                this.preloader = true;
                const url = actions.GET_PLAN_MARGINS;

                this.axiosGet(url)
                    .then(response => {
                        if (response.data && response.data.margins) {
                            // Merge API data with default structure
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
                    .catch(({response}) => {
                        this.$toastr.e(response?.data?.message || this.$t('error_loading_margins'));
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            /**
             * Submit form to update margins
             */
            submit() {
                this.preloader = true;
                const url = actions.UPDATE_PLAN_MARGINS;
                const data = { margins: this.margins };

                this.axiosPost({
                    url: url,
                    data: data
                }).then(response => {
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
                }).catch(({response}) => {
                    this.$toastr.e(response?.data?.message || this.$t('error_updating_margins'));
                }).finally(() => {
                    this.preloader = false;
                });
            },

            /**
             * Reset all margins to default 30%
             */
            resetToDefaults() {
                if (confirm(this.$t('confirm_reset_margins'))) {
                    this.planCapacities.forEach(plan => {
                        this.margins[plan].margin_percentage = 30.00;
                    });
                    this.$toastr.i(this.$t('margins_reset_to_defaults'));
                }
            },

            /**
             * Calculate example final price for a given margin
             * Formula: Final Price = Cost / (1 - Margin)
             */
            calculateExample(marginPercentage) {
                const cost = 100;
                const marginDecimal = parseFloat(marginPercentage) / 100;
                
                if (marginDecimal >= 1) {
                    return 'N/A';
                }
                
                const finalPrice = cost / (1 - marginDecimal);
                return finalPrice.toFixed(2);
            }
        }
    }
</script>

<style scoped>
    .plan-margins-setting {
        padding: 20px 0;
    }
    
    .input-group {
        min-width: 150px;
    }
</style>
