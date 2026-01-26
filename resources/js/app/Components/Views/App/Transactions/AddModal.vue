<template>
    <modal :modal-id="modalId"
                     :title="modalTitle"
                     :preloader="preloader"
                     @submit="submit"
                     @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form class="mb-0"
                  :class="{'loading-opacity': preloader}"
                  ref="form" :data-url='selectedUrl ? `transactions/${inputs.id}` : `transactions`'>
                <div class="form-group row align-items-center">
                    <label for="inputs_transaction_id" class="col-sm-3 mb-0">
                        {{ $t('transaction_id') }}
                    </label>
                    <app-input id="inputs_transaction_id"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.transaction_id"
                               :placeholder="$t('transaction_id')"
                               :required="true"/>
                </div>
                
                <div class="form-group row align-items-center">
                    <label for="inputs_status" class="col-sm-3 mb-0">
                        {{ $t('status') }}
                    </label>
                    <app-input id="inputs_status"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.status"
                               :placeholder="$t('status')"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="inputs_iccid" class="col-sm-3 mb-0">
                        {{ $t('iccid') }}
                    </label>
                    <app-input id="inputs_iccid"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.iccid"
                               :placeholder="$t('iccid')"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="inputs_esim_qr" class="col-sm-3 mb-0">
                        {{ $t('esim_qr') }}
                    </label>
                    <app-input id="inputs_esim_qr"
                               class="col-sm-9"
                               type="textarea"
                               v-model="inputs.esim_qr"
                               :placeholder="$t('esim_qr')"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="inputs_creation_time" class="col-sm-3 mb-0">
                        {{ $t('creation_time') }}
                    </label>
                    <app-input id="inputs_creation_time"
                               class="col-sm-9"
                               type="datetime-local"
                               v-model="inputs.creation_time"
                               :placeholder="$t('creation_time')"/>
                </div>

                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_cliente_id" class="col-sm-3 mb-0">
                        {{ $t('cliente_id') }}
                    </label>
                    <app-input id="inputs_cliente_id"
                               class="col-sm-9"
                               type="number"
                               v-model="inputs.cliente_id"
                               :placeholder="$t('cliente_id')"/>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin";

    export default {
        name: "TransactionAddModal",
        mixins: [FormMixin, ModalMixin],
        props: {
            tableId: String
        },
        data() {
            return {
                preloader: false,
                inputs: {
                    transaction_id: '',
                    status: '',
                    iccid: '',
                    esim_qr: '',
                    creation_time: '',
                    cliente_id: null,
                },
                modalId: 'transaction-add-edit-modal',
                modalTitle: this.$t('add'),
            }
        },
        created() {
            if (this.selectedUrl) {
                this.modalTitle = this.$t('edit');
                this.preloader = true;
            }
        },
        methods: {
            submit() {
                this.save(this.inputs);
            },
            afterSuccess(response) {
                this.$toastr.s(response.data.message);
                this.$hub.$emit('reload-' + this.tableId);
            },

            afterSuccessFromGetEditData(response) {
                this.inputs = response.data;
                this.preloader = false;
            },
        },
    }
</script>
