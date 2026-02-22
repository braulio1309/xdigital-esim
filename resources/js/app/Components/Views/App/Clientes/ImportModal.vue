<template>
    <modal :modal-id="modalId"
           :title="'Importar Clientes'"
           :preloader="preloader"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form class="mb-0"
                  :class="{'loading-opacity': preloader}"
                  ref="form"
                  enctype="multipart/form-data">
                <!-- File input -->
                <div class="form-group row align-items-center">
                    <label class="col-sm-3 mb-0">
                        Archivo Excel/CSV
                    </label>
                    <div class="col-sm-9">
                        <input type="file"
                               class="form-control-file"
                               accept=".xlsx,.xls,.csv"
                               @change="onFileChange"
                               required />
                        <small class="form-text text-muted">
                            El archivo debe tener columnas: <strong>nombre</strong>, <strong>apellido</strong>, <strong>email</strong>.
                        </small>
                    </div>
                </div>

                <!-- Activate free eSIM checkbox -->
                <div class="form-group row align-items-center">
                    <label class="col-sm-3 mb-0">eSIM Gratuita</label>
                    <div class="col-sm-9">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="import_activate_free_esim"
                                   v-model="inputs.activate_free_esim" />
                            <label class="custom-control-label" for="import_activate_free_esim">
                                Activar permiso de eSIM gratuita a cada cliente importado
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Beneficiario/Partner select (only shown for non-beneficiario users) -->
                <div v-if="!isBeneficiario" class="form-group row align-items-center mb-0">
                    <label class="col-sm-3 mb-0">Beneficiario</label>
                    <app-input class="col-sm-9"
                               type="select"
                               v-model="inputs.beneficiario_id"
                               :list="beneficiarios"
                               list-value-field="value"
                               :placeholder="'Seleccionar beneficiario (opcional)'"
                               :required="false"/>
                </div>
                <div v-else class="form-group row align-items-center mb-0">
                    <label class="col-sm-3 mb-0">Beneficiario</label>
                    <div class="col-sm-9">
                        <span class="text-muted">Se asignará automáticamente a tu cuenta</span>
                    </div>
                </div>

                <!-- Results summary after import -->
                <div v-if="result" class="mt-3">
                    <div :class="result.status ? 'alert alert-success' : 'alert alert-danger'">
                        {{ result.message }}
                    </div>
                    <ul v-if="result.errors && result.errors.length" class="list-unstyled small text-danger mb-0">
                        <li v-for="(err, i) in result.errors" :key="i">{{ err }}</li>
                    </ul>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import {ModalMixin} from "../../../../Mixins/ModalMixin";
    import * as actions from "../../../../Config/ApiUrl";
    import axios from "axios";

    export default {
        name: "ClienteImportModal",
        mixins: [ModalMixin],
        props: {
            tableId: String,
            isBeneficiario: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                preloader: false,
                modalId: 'cliente-import-modal',
                inputs: {
                    activate_free_esim: false,
                    beneficiario_id: null,
                },
                file: null,
                beneficiarios: [],
                result: null,
            };
        },
        created() {
            if (!this.isBeneficiario) {
                this.loadBeneficiarios();
            }
        },
        methods: {
            onFileChange(event) {
                this.file = event.target.files[0] || null;
                this.result = null;
            },
            loadBeneficiarios() {
                axios.get('/' + actions.BENEFICIARIOS)
                    .then(response => {
                        this.beneficiarios = (response.data.data || []).map(b => ({
                            id: b.id,
                            value: b.nombre,
                        }));
                    })
                    .catch(() => {
                        this.$toastr.e('Error al cargar la lista de beneficiarios');
                    });
            },
            submit() {
                if (!this.file) {
                    this.$toastr.e('Por favor selecciona un archivo Excel o CSV.');
                    return;
                }

                const formData = new FormData();
                formData.append('file', this.file);
                formData.append('activate_free_esim', this.inputs.activate_free_esim ? '1' : '0');
                if (this.inputs.beneficiario_id) {
                    formData.append('beneficiario_id', this.inputs.beneficiario_id);
                }

                this.preloader = true;
                this.result = null;

                axios.post('/clientes/import', formData, {
                    headers: {'Content-Type': 'multipart/form-data'},
                })
                    .then(response => {
                        this.result = response.data;
                        if (response.data.status) {
                            this.$toastr.s(response.data.message);
                            this.$hub.$emit('reload-' + this.tableId);
                        } else {
                            this.$toastr.e(response.data.message);
                        }
                    })
                    .catch(error => {
                        const msg = error.response && error.response.data && error.response.data.message
                            ? error.response.data.message
                            : 'Error al importar clientes.';
                        this.$toastr.e(msg);
                        this.result = {status: false, message: msg, errors: []};
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
        },
    };
</script>
