<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="'Clientes'" :directory="'Clientes'" :icon="'users'"/>
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
                          modal-id="cliente-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    import axios from "axios";
    import AddModal from "./AddModal"; 

    export default {
        extends: CoreLibrary,
        name: "ClientesList",
        components: {
            AddModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'clientes-table',
                rowData: {},
                options: {
                    url: actions.CLIENTES,
                    name: 'Clientes',
                    datatableWrapper: false,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('nombre'),
                            type: 'text',
                            key: 'nombre',
                        },
                        {
                            title: 'Apellido',
                            type: 'text',
                            key: 'apellido',
                        },
                        {
                            title: 'Email',
                            type: 'text',
                            key: 'email',
                        },
                        {
                            title: 'Beneficiario',
                            type: 'custom-html',
                            key: 'beneficiario',
                            modifier: (value) => {
                                return value && value.nombre ? value.nombre : '<span class="text-muted">N/A</span>';
                            }
                        },
                        {
                            title: 'eSIM Gratuita',
                            type: 'custom-html',
                            key: 'can_activate_free_esim',
                            modifier: (value, row) => {
                                const status = value ? 'Permitido' : 'No permitido';
                                const badgeClass = value ? 'badge-success' : 'badge-secondary';
                                return `<span class="badge ${badgeClass}">${status}</span>`;
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
                            modalId: 'cliente-add-edit-modal',
                        }, 
                        {
                            title: 'eSIM Gratis',
                            icon: 'toggle-right',
                            type: 'none',
                            component: 'dummy-component', 
                            modalId: 'dummy-modal-id',
                        },
                        {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'cliente-delete',
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
            },

            /**
             * for close add edit modal
             */
            closeAddEditModal() {
                $("#cliente-add-edit-modal").modal('hide'); 
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
                    this.selectedUrl = `${actions.CLIENTES}/${rowData.id}`;
                    this.openAddEditModal();
                } else if (actionObj.title == 'eSIM Gratis') {
                    this.toggleFreeEsim(rowData);
                }
            },

            /**
             * Toggle the can_activate_free_esim flag for a client
             */
            toggleFreeEsim(rowData) {
                const url = `/clientes/${rowData.id}/toggle-free-esim`;
                const newStatus = !rowData.can_activate_free_esim;
                const action = newStatus ? 'activar' : 'desactivar';
                
                if (confirm(`¿Está seguro que desea ${action} el permiso de eSIM gratuita para ${rowData.nombre}?`)) {
                    console.log(`Toggling free eSIM for client ID ${rowData.id} to ${newStatus}`);
                    axios.post(url, {}) 
                        .then(response => {
                            this.$toastr.s(response.data.message);
                            this.$hub.$emit('reload-' + this.tableId);
                        })
                        .catch(error => {
                            console.error("EL ERROR REAL ES:", error); // Aquí verás el culpable
                            this.$toastr.e('Error en la petición');
                        });
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
                let url = `${actions.CLIENTES}/${this.rowData.id}`;
                this.deleteLoader=true;
                this.axiosDelete(url)
                    .then(response => {
                        this.deleteLoader= false;
                        $("#cliente-delete").modal('hide');
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
        }
    }
</script>
