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
                  ref="form" :data-url='selectedUrl ? `beneficiarios/${inputs.id}` : `beneficiarios`'>
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
                        {{ $t('apellido') || 'Apellido' }}
                    </label>
                    <app-input id="inputs_apellido"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.apellido"
                               :placeholder="'Apellido'"
                               :required="false"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_descripcion" class="col-sm-3 mb-0">
                        {{ $t('descripcion') }}
                    </label>
                    <app-input id="inputs_descripcion"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.descripcion"
                               :placeholder="$t('descripcion')"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_email" class="col-sm-3 mb-0">
                        {{ $t('email') || 'Email' }}
                    </label>
                    <app-input id="inputs_email"
                               class="col-sm-9"
                               type="email"
                               v-model="inputs.email"
                               :placeholder="'Email'"
                               :required="!selectedUrl"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_password" class="col-sm-3 mb-0">
                        {{ $t('password') || 'Contraseña' }}
                        <span v-if="selectedUrl" class="text-muted small d-block">(opcional)</span>
                    </label>
                    <app-input id="inputs_password"
                               class="col-sm-9"
                               type="password"
                               v-model="inputs.password"
                               :placeholder="selectedUrl ? 'Nueva contraseña (opcional)' : 'Contraseña'"
                               :required="!selectedUrl"/>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin";

    export default {
        name: "BeneficiarioAddModal",
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
                    descripcion: '',
                    email: '',
                    password: '',
                },
                modalId: 'beneficiario-add-edit-modal',
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
                const data = response.data;
                this.inputs = {
                    id: data.id,
                    nombre: data.nombre,
                    apellido: data.user ? data.user.last_name : '',
                    descripcion: data.descripcion,
                    email: data.user ? data.user.email : '',
                    password: '',
                };
                this.preloader = false;
            },
        },
    }
</script>
