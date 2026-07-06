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
                               :placeholder="`${$t('nombre')} (opcional)`"
                               :required="false"/>

                </div>
                <div class="form-group row align-items-center">
                    <label for="inputs_apellido" class="col-sm-3 mb-0">
                        Apellido
                    </label>
                    <app-input id="inputs_apellido"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.apellido"
                               placeholder="Apellido (opcional)"
                               :required="false"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_identificador" class="col-sm-3 mb-0">
                        DNI / Pasaporte
                    </label>
                    <app-input id="inputs_identificador"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.identificador"
                               placeholder="Número de documento o pasaporte"
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
                <div class="form-group row align-items-center mb-0" v-if="showBeneficiarioSelect">
                    <label for="inputs_beneficiario_id" class="col-sm-3 mb-0">
                        Partner
                    </label>
                    <app-input id="inputs_beneficiario_id"
                               class="col-sm-9"
                               type="search-select"
                               v-model="inputs.beneficiario_id"
                               :list="beneficiarios"
                               list-value-field="value"
                               list-class="partner-select-dropdown"
                               :placeholder="'Seleccionar partner (opcional)'"
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
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_free_esim_capacity" class="col-sm-3 mb-0">
                        Capacidad eSIM Gratuita
                    </label>
                    <div class="col-sm-9">
                        <select id="inputs_free_esim_capacity"
                                class="form-control"
                                v-model="inputs.free_esim_capacity">
                            <option :value="1">1 GB</option>
                            <option :value="3">3 GB</option>
                            <option :value="5">5 GB</option>
                            <option :value="10">10 GB</option>
                        </select>
                        <small class="text-muted">Capacidad de datos de la eSIM gratuita que se asignará al cliente.</small>
                    </div>
                </div>

                <hr class="my-3">
                <p class="mb-2 font-weight-bold text-muted small text-uppercase" style="letter-spacing: .05em;">Voucher de viaje</p>

                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_numero_voucher" class="col-sm-3 mb-0">
                        Nº de voucher
                    </label>
                    <app-input id="inputs_numero_voucher"
                               class="col-sm-9"
                               type="text"
                               v-model="inputs.numero_voucher"
                               placeholder="Número de voucher (opcional)"
                               :required="false"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label for="inputs_numero_personas" class="col-sm-3 mb-0">
                        Nº de viajeros
                    </label>
                    <app-input id="inputs_numero_personas"
                               class="col-sm-9"
                               type="number"
                               v-model="inputs.numero_personas"
                               placeholder="Cantidad de personas en el voucher"
                               :required="false"/>
                </div>

                <!-- Vouchers registrados (solo en modo edición) -->
                <div v-if="selectedUrl && voucherList.length" class="mt-3">
                    <p class="mb-1 font-weight-bold small text-muted">Vouchers anteriores</p>
                    <ul class="list-group list-group-flush">
                        <li v-for="v in voucherList" :key="v.id"
                            class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                            <span>
                                <strong>{{ v.numero_voucher }}</strong>
                                <span class="text-muted ml-2">{{ v.numero_personas }} viajero(s)</span>
                            </span>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    @click="deleteVoucher(v)">
                                <i class="feather icon-trash-2"></i>
                            </button>
                        </li>
                    </ul>
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
                    identificador: '',
                    email: '',
                    password: '',
                    beneficiario_id: null,
                    can_activate_free_esim: false,
                    free_esim_capacity: 1,
                    numero_voucher: '',
                    numero_personas: 1,
                    voucher_edit_id: null,
                },
                beneficiarios: [],
                voucherList: [],
                modalId: 'cliente-add-edit-modal',
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
            showBeneficiarioSelect() {
                // Para crear clientes como beneficiario o super_partner no se pide el select
                return !this.isBeneficiarioUser && !this.isSuperPartnerUser;
            }
        },
        created() {
            if (this.selectedUrl) {
                this.modalTitle = this.$t('edit');
                this.preloader = true;
            }
            if (this.showBeneficiarioSelect) {
                this.loadBeneficiarios();
            }
        },
        methods: {
            defaultInputs() {
                return {
                    nombre: '',
                    apellido: '',
                    identificador: '',
                    email: '',
                    password: '',
                    beneficiario_id: null,
                    can_activate_free_esim: false,
                    free_esim_capacity: 1,
                    numero_voucher: '',
                    numero_personas: 1,
                    voucher_edit_id: null,
                };
            },
            resetCreateFormState() {
                this.inputs = this.defaultInputs();
                this.voucherList = [];
                this.modalTitle = this.$t('add');
                this.preloader = false;
            },
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
                        this.$toastr.e('Error al cargar la lista de partners');
                    });
            },
            loadVouchers(clienteId) {
                this.axiosGet(`/clientes/${clienteId}/vouchers`)
                    .then(response => {
                        this.voucherList = response.data || [];

                        if (!this.inputs.voucher_edit_id && this.voucherList.length) {
                            this.applyVoucherToInputs(this.voucherList[0]);
                        }
                    })
                    .catch(() => {});
            },
            applyVoucherToInputs(voucher = null) {
                this.inputs.numero_voucher = voucher ? voucher.numero_voucher : '';
                this.inputs.numero_personas = voucher ? voucher.numero_personas : 1;
                this.inputs.voucher_edit_id = voucher ? voucher.id : null;
            },
            deleteVoucher(voucher) {
                if (!confirm('¿Eliminar este voucher?')) return;
                const clienteId = this.inputs.id;
                this.axiosDelete(`/clientes/${clienteId}/vouchers/${voucher.id}`)
                    .then(() => {
                        this.voucherList = this.voucherList.filter(v => v.id !== voucher.id);

                        if (this.inputs.voucher_edit_id === voucher.id) {
                            this.applyVoucherToInputs(this.voucherList[0] || null);
                        }

                        this.$toastr.s('Voucher eliminado.');
                    })
                    .catch(() => {
                        this.$toastr.e('No se pudo eliminar el voucher.');
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
                this.inputs = {
                    ...response.data,
                    password: '',
                    numero_voucher: response.data.numero_voucher || '',
                    numero_personas: response.data.numero_personas || 1,
                    voucher_edit_id: response.data.voucher_edit_id || null,
                };
                if (response.data.id) {
                    this.loadVouchers(response.data.id);
                }
                this.preloader = false;
            },
        },
        watch: {
            selectedUrl: {
                immediate: true,
                handler(value) {
                    if (!value) {
                        this.resetCreateFormState();
                    }
                }
            }
        }
    }
</script>

<style>
.partner-select-dropdown .dropdown-search-result-wrapper {
    max-height: 220px;
    overflow-y: auto;
}
</style>
