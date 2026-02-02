<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="$t('beneficiarios')" :directory="$t('beneficiarios')" :icon="'users'"/>
            </div>
            <div class="col-sm-12 col-md-6 breadcrumb-side-button">
                <div class="float-md-right mb-3 mb-sm-3 mb-md-0">
                    <button type="button"
                            class="btn btn-primary btn-with-shadow"
                            data-toggle="modal"
                            @click="openAddEditModal">
                        {{ $t('add') }}
                    </button>
                </div>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <add-modal v-if="isAddEditModalActive"
                   :table-id="tableId"
                   :selected-url="selectedUrl"
                   @close-modal="closeAddEditModal"/>

        <app-delete-modal v-if="deleteConfirmationModalActive"
                          :preloader="deleteLoader"
                          modal-id="beneficiario-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    
    // CAMBIO 1: Importamos el modal local desde la misma carpeta
    import AddModal from "./AddModal"; 

    export default {
        extends: CoreLibrary,
        name: "BeneficiariosList",
        // CAMBIO 2: Registramos el componente
        components: {
            AddModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'beneficiarios-table',
                rowData: {},
                options: {
                    url: actions.BENEFICIARIOS,
                    name: this.$t('beneficiarios'),
                    datatableWrapper: false,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('nombre'),
                            type: 'text',
                            key: 'nombre',
                        },
                        {
                            title: this.$t('descripcion'),
                            type: 'text',
                            key: 'descripcion',
                        },
                        {
                            title: 'Link de Referencia',
                            type: 'custom-html',
                            key: 'referral_link',
                            modifier: (value, row) => {
                                if (!row.codigo) {
                                    return '<span class="text-muted">Sin código</span>';
                                }
                                const slug = row.nombre.toLowerCase().replace(/\s+/g, '-');
                                const link = `${window.location.origin}/registro/esim/${slug}-${row.codigo}`;
                                return `
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control form-control-sm mr-2" value="${link}" readonly id="link-${row.id}" style="max-width: 300px;">
                                        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('link-${row.id}')">
                                            <i class="mdi mdi-content-copy"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        },
                        {
                            title: this.$t('action'),
                            type: 'action',
                            key: 'invoice',
                            isVisible: true
                        }
                    ],
                    actions: [
                        {
                            title: this.$t('edit'),
                            icon: 'edit',
                            type: 'none',
                            component: 'app-add-modal',
                            modalId: 'beneficiario-add-edit-modal',
                        }, {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'beneficiario-delete',
                        }
                    ],
                    showFilter: false,
                    showSearch: false,
                    paginationType: "pagination",
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                }
            }
        },
        methods: {
            /**
             * for open add edit modal
             */
            openAddEditModal() {
                this.isAddEditModalActive = true;
                // Nota: Asegúrate de que tu AddModal local abra el modal automáticamente al montarse
                // o usa un nextTick si necesitas disparar el trigger de bootstrap manual.
            },

            /**
             * for close add edit modal
             */
            closeAddEditModal() {
                // Asegúrate que este ID coincida con el ID dentro de tu AddModal.vue local
                $("#beneficiario-add-edit-modal").modal('hide'); 
                this.isAddEditModalActive = false;
                this.reSetData();
            },

            /**
             * $emit Form datatable action
             */
            getListAction(rowData, actionObj, active) {

                this.rowData = rowData;

                if (actionObj.title == this.$t('delete')) {
                    this.openDeleteModal();
                } else if (actionObj.title == this.$t('edit')) {
                    this.selectedUrl = `${actions.BENEFICIARIOS}/${rowData.id}`;
                    this.openAddEditModal();
                }
            },

            /**
             * for open confirmation modal
             */
            openDeleteModal() {
                this.deleteConfirmationModalActive = true;
            },

            /**
             * confirmed $emit Form confirmation modal
             */
            confirmed() {
                let url = `${actions.BENEFICIARIOS}/${this.rowData.id}`;
                this.deleteLoader=true;
                this.axiosDelete(url)
                    .then(response => {
                        this.deleteLoader= false;
                        $("#beneficiario-delete").modal('hide');
                        this.cancelled();
                        this.$toastr.s(response.data.message);
                    }).catch(({error}) => {

                    //trigger after error
                }).finally(() => {

                    this.$hub.$emit('reload-' + this.tableId);
                });
            },

            /**
             * cancelled $emit Form confirmation modal
             */
            cancelled() {
                this.deleteConfirmationModalActive = false;
                this.reSetData();
            },

            reSetData() {
                this.rowData = {};
                this.selectedUrl = '';
            }
        },
        mounted() {
            // Add global function for copying to clipboard
            window.copyToClipboard = (elementId) => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.select();
                    input.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand('copy');
                    this.$toastr.s('Link copiado al portapapeles');
                }
            };
        }
    }
</script>