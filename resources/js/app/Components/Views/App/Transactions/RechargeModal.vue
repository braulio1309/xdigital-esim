<template>
    <modal :modal-id="modalId"
           :title="$t('recharge_esim')"
           :preloader="preloader"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <div :class="{'loading-opacity': preloader}">
                <div class="form-group row align-items-center">
                    <label class="col-sm-4 mb-0">{{ $t('iccid') }}</label>
                    <div class="col-sm-8">
                        <span class="badge badge-secondary p-2" style="font-size: 13px; word-break: break-all;">
                            {{ transaction.iccid || 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="form-group row align-items-center">
                    <label class="col-sm-4 mb-0">{{ $t('plan') }}</label>
                    <div class="col-sm-8">
                        <span class="text-muted" style="font-size: 14px;">
                            {{ transaction.plan_name || 'N/A' }}
                        </span>
                    </div>
                </div>

                <hr/>

                <div class="form-group row align-items-center">
                    <label class="col-sm-4 mb-0">{{ $t('recharge_amount') }} <span class="text-danger">*</span></label>
                    <div class="col-sm-8">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-for="gb in gbOptions"
                                   :key="gb"
                                   class="btn btn-outline-primary"
                                   :class="{ active: selectedGb === gb }">
                                <input type="radio"
                                       :value="gb"
                                       v-model="selectedGb"
                                       autocomplete="off"/>
                                {{ gb }} GB
                            </label>
                        </div>
                    </div>
                </div>

                <div v-if="selectedGb" class="alert alert-info mt-2">
                    {{ $t('recharge_confirmation_message', { gb: selectedGb, iccid: transaction.iccid }) }}
                </div>
            </div>
        </template>
    </modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from "../../../../Config/ApiUrl";

    export default {
        name: "RechargeModal",
        props: {
            tableId: {
                type: String,
                required: true
            },
            transaction: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                preloader: false,
                selectedGb: null,
                gbOptions: [1, 3, 5, 10],
                modalId: 'transaction-recharge-modal',
            };
        },
        methods: {
            submit() {
                if (!this.selectedGb) {
                    this.$toastr.e(this.$t('please_select_recharge_amount'));
                    return;
                }

                this.preloader = true;

                axios.post(`/${actions.TRANSACTIONS_RECHARGE(this.transaction.id)}`, {
                    gb_amount: this.selectedGb,
                })
                    .then(response => {
                        this.$toastr.s(response.data.message);
                        this.$hub.$emit('reload-' + this.tableId);
                        this.closeModal();
                    })
                    .catch(error => {
                        this.$toastr.e(error.response?.data?.message || this.$t('error_recharging_esim'));
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            closeModal() {
                this.selectedGb = null;
                this.$emit('close-modal');
            }
        }
    }
</script>
