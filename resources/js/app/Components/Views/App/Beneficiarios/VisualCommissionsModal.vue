<template>
    <app-modal
        modal-id="beneficiary-visual-commissions-modal"
        modal-size="small"
        @close-modal="closeModal">

        <template slot="header">
            <h5 class="modal-title">Comisiones Visuales - {{ beneficiarioName }}</h5>
            <button type="button" class="close outline-none" @click.prevent="closeModal">
                <span>×</span>
            </button>
        </template>

        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form v-else ref="form" class="mb-0" :class="{'loading-opacity': preloader}">

                <div class="form-group mb-4">
                    <div class="alert alert-warning">
                        <strong>Comisiones Visuales del Partner</strong>
                        <p class="mb-0 mt-2">
                            Estas comisiones son <strong>únicamente visuales</strong> para el partner. No afectan el precio de las eSIMs
                            ni el cálculo real de comisiones. El partner verá estos porcentajes en su panel.
                        </p>
                    </div>
                </div>

                <div class="form-group row align-items-center mb-3">
                    <label for="b_commission_latam" class="col-sm-5 mb-0">
                        Comisión LATAM y Europa (%)
                    </label>
                    <div class="col-sm-7">
                        <div class="input-group" style="max-width: 220px;">
                            <app-input
                                id="b_commission_latam"
                                type="number"
                                v-model="saleCommissionLatamPct"
                                :min="0"
                                :max="100"
                                step="0.01"
                                :placeholder="'Sin configurar'"/>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Porcentaje visible para el partner en transacciones de LATAM y Europa.
                        </small>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="b_commission_usa_ca" class="col-sm-5 mb-0">
                        Comisión USA, Canadá y Europa (%)
                    </label>
                    <div class="col-sm-7">
                        <div class="input-group" style="max-width: 220px;">
                            <app-input
                                id="b_commission_usa_ca"
                                type="number"
                                v-model="saleCommissionUsaCaEuPct"
                                :min="0"
                                :max="100"
                                step="0.01"
                                :placeholder="'Sin configurar'"/>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Porcentaje visible para el partner en transacciones de USA, Canadá y Europa.
                        </small>
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
    import * as actions from "../../../../Config/ApiUrl";
    import {urlGenerator} from '../../../../Helpers/AxiosHelper';

    export default {
        name: 'BeneficiaryVisualCommissionsModal',
        mixins: [FormMixin, ModalMixin],
        props: {
            beneficiarioId: {
                type: Number,
                required: true,
            },
            beneficiarioName: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                preloader: false,
                saleCommissionLatamPct: null,
                saleCommissionUsaCaEuPct: null,
            };
        },
        mounted() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.preloader = true;
                axios.get(urlGenerator(actions.BENEFICIARIOS_VISUAL_COMMISSIONS(this.beneficiarioId)))
                    .then(response => {
                        if (response.data) {
                            this.saleCommissionLatamPct = response.data.sale_commission_latam_pct !== null
                                ? parseFloat(response.data.sale_commission_latam_pct)
                                : null;
                            this.saleCommissionUsaCaEuPct = response.data.sale_commission_usa_ca_eu_pct !== null
                                ? parseFloat(response.data.sale_commission_usa_ca_eu_pct)
                                : null;
                        }
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Error al cargar las comisiones.';
                        this.$toastr.e(message);
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            submit() {
                this.preloader = true;
                const payload = {
                    sale_commission_latam_pct: this.parseCommission(this.saleCommissionLatamPct),
                    sale_commission_usa_ca_eu_pct: this.parseCommission(this.saleCommissionUsaCaEuPct),
                };

                axios.post(urlGenerator(actions.BENEFICIARIOS_VISUAL_COMMISSIONS(this.beneficiarioId)), payload)
                    .then(response => {
                        const message = response.data?.message || 'Comisiones actualizadas.';
                        this.$toastr.s(message);
                        if (response.data) {
                            this.saleCommissionLatamPct = response.data.sale_commission_latam_pct !== null
                                ? parseFloat(response.data.sale_commission_latam_pct)
                                : null;
                            this.saleCommissionUsaCaEuPct = response.data.sale_commission_usa_ca_eu_pct !== null
                                ? parseFloat(response.data.sale_commission_usa_ca_eu_pct)
                                : null;
                        }
                        setTimeout(() => { this.closeModal(); }, 800);
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Error al actualizar las comisiones.';
                        this.$toastr.e(message);
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            closeModal() {
                this.$emit('close');
            },
            parseCommission(value) {
                return (value !== null && value !== undefined && value !== '')
                    ? parseFloat(value)
                    : null;
            },
        },
    };
</script>
