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
                  ref="form">
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
                                   :required="true"/>
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
                                   :required="true"/>
                    </div>
                </div>

                <div class="form-group row align-items-center mb-0">
                    <label for="end_date" class="col-sm-3 mb-0">
                        {{ $t('end_date') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <app-input id="end_date"
                                   type="date"
                                   v-model="inputs.end_date"
                                   :placeholder="$t('end_date')"
                                   :required="true"/>
                    </div>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import * as actions from "../../../../Config/ApiUrl";

    export default {
        name: "MarkAsPaidModal",
        props: {
            tableId: String
        },
        data() {
            return {
                preloader: false,
                inputs: {
                    beneficiario_id: '',
                    start_date: '',
                    end_date: '',
                },
                modalId: 'mark-as-paid-modal',
                beneficiariosList: []
            }
        },
        mounted() {
            this.loadBeneficiarios();
        },
        methods: {
            loadBeneficiarios() {
                this.axiosGet(actions.BENEFICIARIOS + '?per_page=1000')
                    .then(response => {
                        this.beneficiariosList = response.data.data.map(beneficiario => ({
                            id: beneficiario.id,
                            value: beneficiario.nombre
                        }));
                    })
                    .catch(error => {
                        this.$toastr.e(this.$t('error_loading_beneficiaries'));
                    });
            },

            submit() {
                if (!this.inputs.beneficiario_id || !this.inputs.start_date || !this.inputs.end_date) {
                    this.$toastr.e(this.$t('please_fill_all_required_fields'));
                    return;
                }

                this.preloader = true;
                this.axiosPost(actions.TRANSACTIONS_MARK_AS_PAID, this.inputs)
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
                };
            }
        }
    }
</script>
