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
                <div class="form-group row align-items-center" v-if="showSuperPartnerSelect">
                    <label for="inputs_super_partner_id" class="col-sm-3 mb-0">
                        Super Partner
                    </label>
                    <div class="col-sm-9">
                        <app-input id="inputs_super_partner_id"
                                   class="w-100"
                                   type="select"
                                   v-model="inputs.super_partner_id"
                                   :list="superPartners"
                                   list-value-field="value"
                                   :required="false"/>
                        <small class="text-muted d-block mt-1">
                            Selecciona el super partner directo o deja "N/A".
                        </small>
                    </div>
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
                <div class="form-group row align-items-center">
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
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_logo" class="col-sm-3 mb-0">
                        Logo
                        <span class="text-muted small d-block">(opcional)</span>
                    </label>
                    <div class="col-sm-9">
                        <div v-if="inputs.logoPreview" class="mb-2">
                            <img :src="inputs.logoPreview" alt="Logo preview" style="max-height:60px; max-width:200px; object-fit:contain;">
                        </div>
                        <input id="inputs_logo"
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
    import {urlGenerator} from "../../../../Helpers/AxiosHelper";

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
                            super_partner_id: '',
                    logoPreview: null,
                },
                        superPartners: [],
                logoFile: null,
                _logoObjectUrl: null,
                modalId: 'beneficiario-add-edit-modal',
                modalTitle: this.$t('add'),
            }
        },
        computed: {
            loggedInUser() {
                return this.$store.state.user && this.$store.state.user.loggedInUser
                    ? this.$store.state.user.loggedInUser
                    : null;
            },
            isBeneficiarioUser() {
                return this.loggedInUser && this.loggedInUser.user_type === 'beneficiario';
            },
            isSuperPartnerUser() {
                return this.loggedInUser && this.loggedInUser.user_type === 'super_partner';
            },
            showSuperPartnerSelect() {
                // Solo usuarios que no son beneficiario ni super_partner pueden elegir el super partner
                return !this.isBeneficiarioUser && !this.isSuperPartnerUser;
            }
        },
        created() {
            if (this.selectedUrl) {
                this.modalTitle = this.$t('edit');
                this.preloader = true;
            }
            if (this.showSuperPartnerSelect) {
                this.loadSuperPartners();
            }
        },
        methods: {
            loadSuperPartners() {
                this.axiosGet('/super-partners')
                    .then(response => {
                        const items = response.data && response.data.data ? response.data.data : [];
                        const mapped = items.map(sp => ({
                            id: sp.id,
                            value: sp.nombre,
                        }));

                        // Añadir opción N/A al inicio
                        this.superPartners = [
                            {id: '', value: 'N/A'},
                            ...mapped,
                        ];
                    })
                    .catch(error => {
                        console.error('Error loading super partners:', error);
                        this.$toastr.e('Error al cargar la lista de super partners');
                    });
            },
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
                if (this.showSuperPartnerSelect && this.inputs.super_partner_id) {
                    formData.append('super_partner_id', this.inputs.super_partner_id);
                }
                if (this.logoFile) {
                    formData.append('logo', this.logoFile);
                }

                // Use method spoofing for PATCH (Laravel supports _method override)
                if (isEdit) {
                    formData.append('_method', 'PATCH');
                }

                this.preloader = true;

                axios.post(urlGenerator(url), formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                }).then(response => {
                    this.$toastr.s(response.data.message);
                    this.$hub.$emit('reload-' + this.tableId);
                }).catch(({response}) => {
                    if (response && response.data && response.data.errors) {
                        const errors = response.data.errors;
                        const firstError = Object.values(errors)[0];
                        this.$toastr.e(Array.isArray(firstError) ? firstError[0] : firstError);
                    } else if (response && response.data && response.data.message) {
                        this.$toastr.e(response.data.message);
                    }
                }).finally(() => {
                    this.preloader = false;
                });
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
                    super_partner_id: data.super_partner_id || '',
                    logoPreview: data.logo_url || null,
                };
                this.preloader = false;
            },
        },
        beforeDestroy() {
            if (this._logoObjectUrl) {
                URL.revokeObjectURL(this._logoObjectUrl);
            }
        },
    }
</script>
