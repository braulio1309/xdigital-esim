<template>
    <modal :modal-id="modalId"
           :title="$t('import_clients')"
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
                    <label class="col-sm-3 mb-0">
                        {{ $t('file') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="file"
                               class="form-control-file"
                               ref="importFile"
                               accept=".xlsx,.xls,.csv"
                               @change="onFileChange"
                               required/>
                        <small class="text-muted d-block mt-1">
                            {{ $t('import_clients_hint') || 'Archivo Excel o CSV con columnas: nombre, apellido, email (máx. 5MB)' }}
                        </small>
                    </div>
                </div>

                <div v-if="result" class="form-group row">
                    <div class="col-sm-9 offset-sm-3">
                        <div class="alert" :class="result.imported > 0 ? 'alert-success' : 'alert-warning'" role="alert">
                            {{ result.message }}
                        </div>
                        <ul v-if="result.errors && result.errors.length" class="list-unstyled text-danger small">
                            <li v-for="(err, i) in result.errors" :key="i">{{ err }}</li>
                        </ul>
                    </div>
                </div>

            </form>
        </template>
    </modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from "../../../../Config/ApiUrl";

    export default {
        name: "ClienteImportModal",
        props: {
            tableId: String,
        },
        data() {
            return {
                preloader: false,
                file: null,
                result: null,
                modalId: 'cliente-import-modal',
            }
        },
        methods: {
            onFileChange(event) {
                this.file = event.target.files[0] || null;
                this.result = null;
            },

            submit() {
                if (!this.file) {
                    this.$toastr.e(this.$t('please_select_a_file') || 'Por favor seleccione un archivo.');
                    return;
                }

                this.preloader = true;
                const formData = new FormData();
                formData.append('file', this.file);

                axios.post('/clientes/import', formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                }).then(response => {
                    this.result = response.data;
                    this.$toastr.s(response.data.message);
                    this.$hub.$emit('reload-' + this.tableId);
                    if (this.$refs.importFile) {
                        this.$refs.importFile.value = '';
                    }
                    this.file = null;
                }).catch(error => {
                    this.$toastr.e(error.response?.data?.message || this.$t('error_importing_clients') || 'Error al importar clientes.');
                }).finally(() => {
                    this.preloader = false;
                });
            },

            closeModal() {
                this.$emit('close-modal');
                this.file = null;
                this.result = null;
                if (this.$refs.importFile) {
                    this.$refs.importFile.value = '';
                }
            }
        }
    }
</script>
