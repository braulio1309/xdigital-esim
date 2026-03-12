<template>
    <app-modal
        modal-id="super-partner-commissions-modal"
        modal-size="medium"
        @close-modal="closeModal">

        <template slot="header">
            <h5 class="modal-title">Comisiones - {{ superPartnerName }}</h5>
            <button type="button" class="close outline-none" @click.prevent="closeModal">
                <span>×</span>
            </button>
        </template>

        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form v-else ref="form" class="mb-0" :class="{'loading-opacity': preloader}">
                <div class="form-group mb-4">
                    <div class="alert alert-info">
                        <strong>Configuración de Comisiones del Super Partner</strong>
                        <p class="mb-0 mt-2">
                            Ajusta la comisión general y la tarifa de eSIM gratuita para este super partner.
                        </p>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sp_free_esim_rate" class="col-sm-5 mb-0">
                        Tarifa eSIM Gratuita (USD)
                    </label>
                    <div class="col-sm-7">
                        <div class="input-group" style="max-width: 220px;">
                            <app-input
                                id="sp_free_esim_rate"
                                type="number"
                                v-model="freeEsimRate"
                                :min="0"
                                step="0.01"/>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Monto de referencia por cada eSIM gratuita asociada a la red de este super partner.
                        </small>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label class="d-block mb-2">Comisiones por plan (3GB, 5GB, 10GB)</label>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Comisión %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in planCapacities" :key="plan">
                                    <td class="align-middle"><strong>{{ plan }}GB</strong></td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 180px;">
                                            <app-input
                                                type="number"
                                                v-model="margins[plan].margin_percentage"
                                                :min="0"
                                                :max="100"
                                                step="0.01"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button class="btn btn-light mr-2" @click.prevent="closeModal">
                        {{ $t('cancel') }}
                    </button>
                    <button class="btn btn-primary" @click.prevent="submit">
                        {{ $t('save') }}
                    </button>
                </div>
            </form>
        </template>
    </app-modal>
</template>

<script>
    import axios from 'axios';
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin";

    export default {
        name: 'SuperPartnerCommissionsModal',
        mixins: [FormMixin, ModalMixin],
        props: {
            superPartnerId: {
                type: Number,
                required: true,
            },
            superPartnerName: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                preloader: false,
                commissionPercentage: 0.00,
                freeEsimRate: 0.85,
                planCapacities: ['3', '5', '10'],
                margins: {
                    '3': { margin_percentage: 0.00, is_active: true },
                    '5': { margin_percentage: 0.00, is_active: true },
                    '10': { margin_percentage: 0.00, is_active: true },
                },
            };
        },
        mounted() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.preloader = true;
                axios.get(`/super-partners/${this.superPartnerId}/commissions`)
                    .then(response => {
                        if (response.data) {
                            if (typeof response.data.commission_percentage !== 'undefined' && response.data.commission_percentage !== null) {
                                this.commissionPercentage = parseFloat(response.data.commission_percentage) || 0.00;
                            }
                            if (typeof response.data.free_esim_rate !== 'undefined' && response.data.free_esim_rate !== null) {
                                this.freeEsimRate = parseFloat(response.data.free_esim_rate) || 0.85;
                            }
                            if (response.data.margins) {
                                Object.keys(response.data.margins).forEach(plan => {
                                    if (this.margins[plan]) {
                                        this.margins[plan] = {
                                            ...this.margins[plan],
                                            ...response.data.margins[plan]
                                        };
                                    }
                                });
                            }
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
                const payload = {
                    commission_percentage: this.commissionPercentage,
                    free_esim_rate: this.freeEsimRate,
                    margins: this.margins,
                };

                axios.post(`/super-partners/${this.superPartnerId}/commissions`, payload)
                    .then(response => {
                        const message = response.data?.message || this.$t('updated_response');
                        this.$toastr.s(message);
                        if (response.data) {
                            if (typeof response.data.commission_percentage !== 'undefined' && response.data.commission_percentage !== null) {
                                this.commissionPercentage = parseFloat(response.data.commission_percentage) || this.commissionPercentage;
                            }
                            if (typeof response.data.free_esim_rate !== 'undefined' && response.data.free_esim_rate !== null) {
                                this.freeEsimRate = parseFloat(response.data.free_esim_rate) || this.freeEsimRate;
                            }
                            if (response.data.margins) {
                                Object.keys(response.data.margins).forEach(plan => {
                                    if (this.margins[plan]) {
                                        this.margins[plan] = {
                                            ...this.margins[plan],
                                            ...response.data.margins[plan]
                                        };
                                    }
                                });
                            }
                        }
                        setTimeout(() => {
                            this.closeModal();
                        }, 800);
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || this.$t('error_updating_margins');
                        this.$toastr.e(message);
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            closeModal() {
                this.$emit('close');
            },
        },
    };
</script>
