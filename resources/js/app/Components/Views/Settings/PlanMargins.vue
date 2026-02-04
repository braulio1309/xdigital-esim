<template>
    <div class="plan-margins-setting">
        <app-overlay-loader v-if="preloader"/>
        <form v-else ref="form" class="mb-0" :class="{'loading-opacity': preloader}">
            <!-- Plan Margins Configuration -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                        {{ $t('plan_margins_configuration') }}
                    </legend>
                    <div class="col-md-12">
                        <p class="text-muted mb-4">
                            {{ $t('plan_margins_description') }}
                        </p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ $t('plan_capacity') }}</th>
                                        <th>{{ $t('margin_percentage') }}</th>
                                        <th>{{ $t('example_calculation') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="plan in planCapacities" :key="plan">
                                        <td>
                                            <strong>{{ plan }}GB</strong>
                                        </td>
                                        <td>
                                            <div class="input-group" style="max-width: 200px;">
                                                <app-input
                                                    :id="'margin-' + plan"
                                                    type="number"
                                                    v-model="margins[plan]"
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
                                        <td class="text-muted">
                                            <small v-if="margins[plan]">
                                                {{ $t('cost') }}: $100 → {{ $t('final_price') }}: ${{ calculateExample(margins[plan]) }}
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fa fa-info-circle"></i>
                            <strong>{{ $t('formula') }}:</strong> {{ $t('formula_description') }}
                            <br>
                            <small>{{ $t('example') }}: {{ $t('cost') }} = $100, {{ $t('margin') }} = 30% → {{ $t('final_price') }} = $100 / (1 - 0.30) = $142.86</small>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="mt-5 action-buttons">
                <button class="btn btn-primary mr-2" @click.prevent="submit">
                    {{ $t('save') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import { FormMixin } from "../../../../core/mixins/form/FormMixin";
    import { SettingsStoreMixin } from './Mixins/SettingsStoreMixin';

    export default {
        name: "PlanMarginsSetting",
        mixins: [FormMixin, SettingsStoreMixin],
        data() {
            return {
                preloader: false,
                planCapacities: ['1', '3', '5', '10', '20', '50'],
                margins: {
                    '1': 30.00,
                    '3': 30.00,
                    '5': 30.00,
                    '10': 30.00,
                    '20': 30.00,
                    '50': 30.00,
                }
            }
        },
        created() {
            this.loadMargins();
        },
        methods: {
            /**
             * Load plan margins from API
             */
            loadMargins() {
                this.preloader = true;
                this.axiosGet('/admin/app/settings/plan-margins')
                    .then(response => {
                        if (response.data.data) {
                            Object.keys(response.data.data).forEach(planCapacity => {
                                this.margins[planCapacity] = response.data.data[planCapacity].margin_percentage;
                            });
                        }
                    })
                    .catch(error => {
                        this.$toastr.e(error.response?.data?.message || this.$t('error_loading_margins'));
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            /**
             * Calculate example final price
             */
            calculateExample(marginPercentage) {
                const cost = 100;
                const margin = parseFloat(marginPercentage) / 100;
                if (margin >= 1) return cost.toFixed(2);
                const finalPrice = cost / (1 - margin);
                return finalPrice.toFixed(2);
            },

            /**
             * Submit form
             */
            submit() {
                this.preloader = true;

                // Prepare data
                const margins = this.planCapacities.map(capacity => ({
                    plan_capacity: capacity,
                    margin_percentage: parseFloat(this.margins[capacity]),
                    is_active: true
                }));

                this.axiosPost({
                    url: '/admin/app/settings/plan-margins',
                    data: { margins }
                })
                .then(response => {
                    this.$toastr.s(response.data.message || this.$t('margins_updated_successfully'));
                    this.loadMargins();
                })
                .catch(error => {
                    this.$toastr.e(error.response?.data?.message || this.$t('error_updating_margins'));
                })
                .finally(() => {
                    this.preloader = false;
                });
            }
        }
    }
</script>

<style scoped>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
