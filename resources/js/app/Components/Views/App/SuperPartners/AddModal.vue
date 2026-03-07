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
                  ref="form"
                  :data-url='selectedUrl ? `super-partners/${inputs.id}` : `super-partners`'>

                <div class="form-group row align-items-center">
                    <label for="sp_nombre" class="col-sm-3 mb-0">{{ $t('nombre') }}</label>
                    <app-input id="sp_nombre"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.nombre"
                               :placeholder="$t('nombre')"
                               :required="true"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sp_apellido" class="col-sm-3 mb-0">Apellido</label>
                    <app-input id="sp_apellido"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.apellido"
                               :placeholder="'Apellido'"
                               :required="false"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sp_descripcion" class="col-sm-3 mb-0">{{ $t('descripcion') }}</label>
                    <app-input id="sp_descripcion"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.descripcion"
                               :placeholder="$t('descripcion')"
                               :required="false"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sp_email" class="col-sm-3 mb-0">Email</label>
                    <app-input id="sp_email"
                               class="col-sm-9"
                               type="email"
                               v-model="inputs.email"
                               :placeholder="'Email'"
                               :required="!selectedUrl"/>
                </div>

                <div class="form-group row align-items-center">
                    <label for="sp_password" class="col-sm-3 mb-0">
                        Contraseña
                        <span v-if="selectedUrl" class="text-muted small d-block">(opcional)</span>
                    </label>
                    <app-input id="sp_password"
                               class="col-sm-9"
                               type="password"
                               v-model="inputs.password"
                               :placeholder="selectedUrl ? 'Nueva contraseña (opcional)' : 'Contraseña'"
                               :required="!selectedUrl"/>
                </div>

                <div class="form-group row align-items-center mb-0">
                    <label for="sp_logo" class="col-sm-3 mb-0">
                        Logo
                        <span class="text-muted small d-block">(opcional)</span>
                    </label>
                    <div class="col-sm-9">
                        <div v-if="inputs.logoPreview" class="mb-2">
                            <img :src="inputs.logoPreview" alt="Logo preview"
                                 style="max-height:60px; max-width:200px; object-fit:contain;">
                        </div>
                        <input id="sp_logo"
                               type="file"
                               class="form-control-file"
                               accept="image/*"
                               ref="logoFile"
                               @change="onLogoChange"/>
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
        name: "SuperPartnerAddModal",
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
                    logoPreview: null,
                },
                logoFile: null,
                _logoObjectUrl: null,
                modalId: 'super-partner-add-edit-modal',
                modalTitle: this.$t('add'),
            };
        },
        created() {
            if (this.selectedUrl) {
                this.modalTitle = this.$t('edit');
                this.preloader = true;
            }
        },
        mounted() {
            if (this.selectedUrl) {
                this.axiosGet(this.selectedUrl).then(response => {
                    const data = response.data;
                    this.inputs.id = data.id;
                    this.inputs.nombre = data.nombre || '';
                    this.inputs.apellido = data.user ? data.user.last_name : '';
                    this.inputs.descripcion = data.descripcion || '';
                    this.inputs.email = data.user ? data.user.email : '';
                    if (data.logo_url) {
                        this.inputs.logoPreview = data.logo_url;
                    }
                }).finally(() => {
                    this.preloader = false;
                    this.$nextTick(() => {
                        window.$('#' + this.modalId).modal('show');
                    });
                });
            } else {
                this.$nextTick(() => {
                    window.$('#' + this.modalId).modal('show');
                });
            }
        },
        beforeDestroy() {
            if (this._logoObjectUrl) {
                URL.revokeObjectURL(this._logoObjectUrl);
                this._logoObjectUrl = null;
            }
        },
        methods: {
            onLogoChange(event) {
                const file = event.target.files[0];
                if (file) {
                    if (this._logoObjectUrl) {
                        URL.revokeObjectURL(this._logoObjectUrl);
                    }
                    this.logoFile = file;
                    this._logoObjectUrl = URL.createObjectURL(file);
                    this.inputs.logoPreview = this._logoObjectUrl;
                }
            },
            submit() {
                const url = this.$refs.form.dataset['url'];
                const isEdit = !!this.selectedUrl;

                const formData = new FormData();
                formData.append('nombre', this.inputs.nombre || '');
                formData.append('apellido', this.inputs.apellido || '');
                formData.append('descripcion', this.inputs.descripcion || '');
                formData.append('email', this.inputs.email || '');
                if (this.inputs.password) {
                    formData.append('password', this.inputs.password);
                }
                if (this.logoFile) {
                    formData.append('logo', this.logoFile);
                }
                if (isEdit) {
                    formData.append('_method', 'PATCH');
                }

                this.preloader = true;
                this.axiosPost({url, data: formData}).then(response => {
                    this.$toastr.s(response.data.message);
                    this.$hub.$emit('reload-' + this.tableId);
                    this.closeModal();
                }).catch(error => {
                    const errors = error.response?.data?.errors;
                    if (errors) {
                        Object.values(errors).forEach(msgs => {
                            msgs.forEach(msg => this.$toastr.e(msg));
                        });
                    } else {
                        this.$toastr.e(error.response?.data?.message || 'Error al guardar');
                    }
                }).finally(() => {
                    this.preloader = false;
                });
            },
            closeModal() {
                window.$('#' + this.modalId).modal('hide');
                this.$emit('close-modal');
            },
        }
    };
</script>
