<template>
    <modal :modal-id="modalId"
           :title="$t('mark_transactions_as_paid')"
           :preloader="preloader"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form class="mb-0"
                  :class="{'loading-opacity': preloader}"
                  ref="form"
                  enctype="multipart/form-data">
                <div class="form-group row align-items-center">
                    <label for="beneficiario_id" class="col-sm-3 mb-0">
                        {{ $t('beneficiary') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <app-input id="beneficiario_id"
                                   type="select"
                                   v-model="inputs.beneficiario_id"
                                   :list="beneficiariosList"
                                   :placeholder="$t('select_beneficiary')"
                                   :required="true"
                                   @input="onFilterChange"/>
                    </div>
                </div>
                
                <div class="form-group row align-items-center">
                    <label for="start_date" class="col-sm-3 mb-0">
                        {{ $t('start_date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <app-input id="start_date"
                                   type="date"
                                   v-model="inputs.start_date"
                                   :placeholder="$t('start_date')"
                                   :required="true"
                                   @input="onFilterChange"/>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="end_date" class="col-sm-3 mb-0">
                        {{ $t('end_date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <app-input id="end_date"
                                   type="date"
                                   v-model="inputs.end_date"
                                   :placeholder="$t('end_date')"
                                   :required="true"
                                   @input="onFilterChange"/>
                    </div>
                </div>

                <!-- Amount owed display -->
                <div class="form-group row align-items-center" v-if="amountLoading || amountResult !== null">
                    <label class="col-sm-3 mb-0">{{ $t('amount_owed') }}</label>
                    <div class="col-sm-9">
                        <span v-if="amountLoading" class="badge badge-secondary p-2" style="font-size: 14px;">
                            {{ $t('loading') }}...
                        </span>
                        <span v-else class="badge badge-info p-2" style="font-size: 14px;">
                            ${{ amountResult.amount }} ({{ amountResult.count }} {{ $t('transactions') }})
                        </span>
                    </div>
                </div>

                <hr/>

                <div class="form-group row align-items-center">
                    <label for="payment_date" class="col-sm-3 mb-0">
                        {{ $t('payment_date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <app-input id="payment_date"
                                   type="date"
                                   v-model="inputs.payment_date"
                                   :placeholder="$t('payment_date')"
                                   :required="true"/>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="reference" class="col-sm-3 mb-0">
                        {{ $t('reference') }}
                    </label>
                    <div class="col-sm-9">
                        <app-input id="reference"
                                   type="text"
                                   v-model="inputs.reference"
                                   :placeholder="$t('payment_reference_placeholder')"/>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label for="support" class="col-sm-3 mb-0">
                        {{ $t('payment_support') }}
                    </label>
                    <div class="col-sm-9">
                        <input id="support"
                               type="file"
                               class="form-control-file"
                               ref="supportFile"
                               accept=".jpg,.jpeg,.png,.pdf,.webp"
                               @change="onFileChange"/>
                        <small class="text-muted">{{ $t('payment_support_hint') }}</small>
                    </div>
                </div>

                <div class="form-group row align-items-center mb-0">
                    <label for="notes" class="col-sm-3 mb-0">
                        {{ $t('notes') }}
                    </label>
                    <div class="col-sm-9">
                        <app-input id="notes"
                                   type="textarea"
                                   v-model="inputs.notes"
                                   :placeholder="$t('notes')"/>
                    </div>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from "../../../../Config/ApiUrl";
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';

    export default {
        name: "MarkAsPaidModal",
        props: {
            tableId: String,
            prefillBeneficiarioId: {
                type: [String, Number],
                default: ''
            },
            prefillStartDate: {
                type: String,
                default: ''
            },
            prefillEndDate: {
                type: String,
                default: ''
            }
        },
        mixins: [FormMixin],

        data() {
            return {
                preloader: false,
                amountLoading: false,
                amountResult: null,
                amountTimer: null,
                inputs: {
                    beneficiario_id: this.prefillBeneficiarioId || '',
                    start_date: this.prefillStartDate || '',
                    end_date: this.prefillEndDate || '',
                    payment_date: '',
                    reference: '',
                    notes: '',
                },
                supportFile: null,
                modalId: 'mark-as-paid-modal',
                beneficiariosList: []
            }
        },
        mounted() {
            this.loadBeneficiarios();
            if (this.inputs.beneficiario_id || this.inputs.start_date || this.inputs.end_date) {
                this.fetchAmount();
            }
        },
        methods: {
            loadBeneficiarios() {
                this.preloader = true;
                axios.get('/beneficiarios?per_page=1000')
                    .then(response => {
                        this.beneficiariosList = response.data.data.map(beneficiario => ({
                            id: beneficiario.id,
                            value: beneficiario.nombre
                        }));
                    })
                    .catch(error => {
                        this.$toastr.e(this.$t('error_loading_beneficiaries'));
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            onFilterChange() {
                if (this.amountTimer) {
                    clearTimeout(this.amountTimer);
                }
                const hasFilters = this.inputs.beneficiario_id || this.inputs.start_date || this.inputs.end_date;
                if (!hasFilters) {
                    this.amountResult = null;
                    return;
                }
                this.amountTimer = setTimeout(() => {
                    this.fetchAmount();
                }, 500);
            },

            fetchAmount() {
                const params = new URLSearchParams();
                if (this.inputs.beneficiario_id) params.append('beneficiario_id', this.inputs.beneficiario_id);
                if (this.inputs.start_date) params.append('start_date', this.inputs.start_date);
                if (this.inputs.end_date) params.append('end_date', this.inputs.end_date);

                this.amountLoading = true;
                this.axiosGet(actions.TRANSACTIONS_CALCULATE_AMOUNT + '?' + params.toString())
                    .then(response => {
                        this.amountResult = response.data;
                    })
                    .catch(error => {
                        console.error('Error calculating amount:', error);
                    })
                    .finally(() => {
                        this.amountLoading = false;
                    });
            },

            onFileChange(event) {
                this.supportFile = event.target.files[0] || null;
            },

            submit() {
                if (!this.inputs.beneficiario_id || !this.inputs.start_date || !this.inputs.end_date || !this.inputs.payment_date) {
                    this.$toastr.e(this.$t('please_fill_all_required_fields'));
                    return;
                }

                this.preloader = true;

                const formData = new FormData();
                formData.append('beneficiario_id', this.inputs.beneficiario_id);
                formData.append('start_date', this.inputs.start_date);
                formData.append('end_date', this.inputs.end_date);
                formData.append('payment_date', this.inputs.payment_date);
                if (this.inputs.reference) {
                    formData.append('reference', this.inputs.reference);
                }
                if (this.inputs.notes) {
                    formData.append('notes', this.inputs.notes);
                }
                if (this.supportFile) {
                    formData.append('support', this.supportFile);
                }

                axios.post('/transactions/mark-as-paid', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                    .then(response => {
                        this.$toastr.s(response.data.message);
                        this.$hub.$emit('reload-' + this.tableId);
                        this.closeModal();
                    })
                    .catch(error => {
                        this.$toastr.e(error.response?.data?.message || this.$t('error_marking_transactions'));
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            closeModal() {
                this.$emit('close-modal');
                this.inputs = {
                    beneficiario_id: '',
                    start_date: '',
                    end_date: '',
                    payment_date: '',
                    reference: '',
                    notes: '',
                };
                this.supportFile = null;
                this.amountResult = null;
                if (this.$refs.supportFile) {
                    this.$refs.supportFile.value = '';
                }
            }
        }
    }
</script>