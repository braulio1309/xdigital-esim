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
                  ref="form" :data-url='selectedUrl ? `clientes/${inputs.id}` : `clientes`'>
                <div class="form-group row align-items-center">
                    <label for="inputs_nombre" class="col-sm-3 mb-0">
                        {{ $t('nombre') }}
                    </label>
                    <app-input id="inputs_nombre"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.nombre"
                               :placeholder="$t('nombre')"
                               :required="true"/>

                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_apellido" class="col-sm-3 mb-0">
                        Apellido
                    </label>
                    <app-input id="inputs_apellido"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.apellido"
                               placeholder="Apellido"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_email" class="col-sm-3 mb-0">
                        Email
                    </label>
                    <app-input id="inputs_email"
                               class="col-sm-9"
                               type="email"
                               v-model="inputs.email"
                               placeholder="Email"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_beneficiario_id" class="col-sm-3 mb-0">
                        Beneficiario
                    </label>
                    <app-input id="inputs_beneficiario_id"
                               class="col-sm-9"
                               type="select"
                               v-model="inputs.beneficiario_id"
                               :list="beneficiarios"
                               list-value-field="value"
                               :placeholder="'Seleccionar beneficiario (opcional)'"
                               :required="false"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_can_activate_free_esim" class="col-sm-3 mb-0">
                        Permitir eSIM Gratuita
                    </label>
                    <div class="col-sm-9">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="inputs_can_activate_free_esim"
                                   v-model="inputs.can_activate_free_esim">
                            <label class="custom-control-label" for="inputs_can_activate_free_esim">
                                Puede activar su eSIM gratuita
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin";

    export default {
        name: "ClienteAddModal",
        mixins: [FormMixin, ModalMixin],
        props: {
            tableId: String
        },
        data() {
            return {
                preloader: false,
                inputs: {
                    nombre: '',
                    apellido: '',
                    email: '',
                    beneficiario_id: null,
                    can_activate_free_esim: false,
                },
                beneficiarios: [],
                modalId: 'cliente-add-edit-modal',
                modalTitle: this.$t('add'),
            }
        },
        created() {
            if (this.selectedUrl) {
                this.modalTitle = this.$t('edit');
                this.preloader = true;
            }
            this.loadBeneficiarios();
        },
        methods: {
            loadBeneficiarios() {
                this.axiosGet('/beneficiarios')
                    .then(response => {
                        this.beneficiarios = response.data.data.map(b => ({
                            id: b.id,
                            value: b.nombre
                        }));
                    })
                    .catch(error => {
                        console.error('Error loading beneficiarios:', error);
                        this.$toastr.e('Error al cargar la lista de beneficiarios');
                    });
            },
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
