<template>
    <div class="content-wrapper">

        <!-- Search-first screen for Atención al Cliente users -->
        <div v-if="isAtencionCliente && !atencionClienteSearchSubmitted" class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6">
                <div class="card card-with-shadow border-0 p-4">
                    <h5 class="card-title mb-3">
                        <app-icon name="search" class="mr-2" style="width:20px;height:20px;"/>
                        Buscar cliente
                    </h5>
                    <p class="text-muted mb-4">Ingresa el correo del cliente o una fecha para poder ver resultados.</p>
                    <div class="input-group mb-3">
                        <input type="text"
                               class="form-control form-control-lg"
                               v-model="atencionClienteSearchQuery"
                               placeholder="Correo del cliente"
                               @keyup.enter="submitAtencionClienteSearch"/>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" @click="submitAtencionClienteSearch">
                                Buscar
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="date"
                               class="form-control form-control-lg"
                               v-model="atencionClienteSearchDate">
                    </div>
                    <small class="text-muted">Debes buscar para poder ver clientes.</small>
                </div>
            </div>
        </div>

        <!-- Main content: shown for non-atencion_cliente users OR after search submitted -->
        <template v-if="!isAtencionCliente || atencionClienteSearchSubmitted">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="'Clientes'" :directory="'Clientes'" :icon="'users'"/>
            </div>
            <div class="col-sm-12 col-md-6 breadcrumb-side-button">
                <div v-if="!isAtencionCliente" class="float-md-right mb-3 mb-sm-3 mb-md-0">
                    <button type="button"
                            class="btn btn-success btn-with-shadow mr-2"
                            @click="openImportModal">
                        {{ $t('import') }}
                    </button>
                    <button type="button"
                            class="btn btn-primary btn-with-shadow"
                            data-toggle="modal"
                            @click="openAddEditModal">
                        {{ $t('add') }}
                    </button>
                </div>
            </div>
        </div>

        <app-table :id="tableId" :options="tableOptions" :search="search" @action="getListAction"/>

        <add-modal v-if="isAddEditModalActive"
                   :table-id="tableId"
                   :selected-url="selectedUrl"
                   @close-modal="closeAddEditModal"/>

        <import-modal v-if="isImportModalActive"
                      :table-id="tableId"
                      @close-modal="closeImportModal"/>

        <app-delete-modal v-if="deleteConfirmationModalActive"
                          :preloader="deleteLoader"
                          :title="clienteStatusModalTitle"
                          :message="clienteStatusModalMessage"
                          :icon="clienteStatusModalIcon"
                          :modal-class="clienteStatusModalClass"
                          :first-button-name="clienteStatusModalButton"
                          modal-id="cliente-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
        </template>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    import axios from "axios";
    import AddModal from "./AddModal";
    import ImportModal from "./ImportModal";

    export default {
        extends: CoreLibrary,
        name: "ClientesList",
        components: {
            AddModal,
            ImportModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                isImportModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'clientes-table',
                rowData: {},
                // Search-first mode for atencion_cliente users
                atencionClienteSearchQuery: '',
                atencionClienteSearchDate: '',
                atencionClienteSearchSubmitted: false,
                search: '',
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
                            title: 'DNI / Pasaporte',
                            type: 'text',
                            key: 'identificador',
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
                            title: 'Estado',
                            type: 'custom-html',
                            key: 'user',
                            modifier: (value) => {
                                const isInactive = value && value.status && value.status.name === 'status_inactive';
                                const badgeClass = isInactive ? 'badge-secondary' : 'badge-success';
                                const label = isInactive ? 'Inactivo' : 'Activo';

                                return `<span class="badge ${badgeClass}">&#10003; ${label}</span>`;
                            }
                        },
                        {
                            title: 'eSIM Gratuita',
                            type: 'custom-html',
                            key: 'can_activate_free_esim',
                            modifier: (value, row) => {
                                const status = value ? 'Permitido' : 'No permitido';
                                const badgeClass = value ? 'badge-success' : 'badge-secondary';
                                const capacity = row.free_esim_capacity ? `${row.free_esim_capacity}GB` : '1GB';
                                return `<span class="badge ${badgeClass}">${status}</span> <span class="badge badge-info">${capacity}</span>`;
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
                            title: 'Enviar correo',
                            icon: 'mail',
                            type: 'none',
                            component: 'dummy-component',
                            modalId: 'dummy-modal-id',
                        },
                        {
                            title: 'Activar cliente',
                            icon: 'check-circle',
                            type: 'none',
                            component: 'dummy-component',
                            modalId: 'dummy-modal-id',
                            modifier: (row) => {
                                return !this.isClienteInactive(row);
                            }
                        },
                        {
                            title: 'Inactivar cliente',
                            icon: 'x-circle',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'cliente-delete',
                            modifier: (row) => {
                                return this.isClienteInactive(row);
                            }
                        }
                    ],
                    showFilter: false,
                    showSearch: true,
                    paginationType: "pagination",
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                }
            }
        },
        computed: {
            isAtencionCliente() {
                const u = this.$store.state.user && this.$store.state.user.loggedInUser;
                return u && u.user_sub_type === 'atencion_cliente';
            },
            canManageClienteStatus() {
                const u = this.$store.state.user && this.$store.state.user.loggedInUser;

                if (!u) {
                    return false;
                }

                if (u.user_sub_type === 'atencion_cliente') {
                    return false;
                }

                return ['admin', 'super_partner', 'admin_partner', 'beneficiario', 'admin_beneficiario'].includes(u.user_type)
                    && !(u.user_type === 'admin' && u.user_sub_type === 'directivo');
            },
            tableOptions() {
                if (this.isAtencionCliente) {
                    // Atención al cliente: hide edit/delete/toggle actions
                    return {
                        ...this.options,
                        actions: [],
                        showAction: false,
                    };
                }
                return {
                    ...this.options,
                    actions: this.options.actions.filter(action => {
                        if (['Activar cliente', 'Inactivar cliente'].includes(action.title)) {
                            return this.canManageClienteStatus;
                        }

                        return true;
                    }),
                };
            },
            clienteStatusModalTitle() {
                return this.isClienteInactive(this.rowData) ? 'Activar cliente' : 'Inactivar cliente';
            },
            clienteStatusModalMessage() {
                return this.isClienteInactive(this.rowData)
                    ? 'Este cliente volvera a estar activo y podra ingresar nuevamente.'
                    : 'Este cliente quedara marcado como inactivo y ya no podra ingresar.';
            },
            clienteStatusModalIcon() {
                return this.isClienteInactive(this.rowData) ? 'check-circle' : 'x-circle';
            },
            clienteStatusModalClass() {
                return this.isClienteInactive(this.rowData) ? 'success' : 'warning';
            },
            clienteStatusModalButton() {
                return this.isClienteInactive(this.rowData) ? 'Si, activar' : 'Si, inactivar';
            },
        },
        methods: {
            isClienteInactive(rowData) {
                return rowData && rowData.user && rowData.user.status && rowData.user.status.name === 'status_inactive';
            },
            submitAtencionClienteSearch() {
                const query = this.atencionClienteSearchQuery.trim();
                const params = new URLSearchParams();

                if (!query && !this.atencionClienteSearchDate) {
                    return;
                }

                this.search = query;

                if (this.atencionClienteSearchDate) {
                    params.append('start_date', this.atencionClienteSearchDate);
                    params.append('end_date', this.atencionClienteSearchDate);
                }

                this.options.url = actions.CLIENTES + (params.toString() ? '?' + params.toString() : '');
                this.atencionClienteSearchSubmitted = true;
                this.$nextTick(() => {
                    this.$hub.$emit('reload-' + this.tableId);
                });
            },

            /**
             * for open import modal
             */
            openImportModal() {
                this.isImportModalActive = true;
            },

            /**
             * for close import modal
             */
            closeImportModal() {
                $("#cliente-import-modal").modal('hide');
                this.isImportModalActive = false;
            },

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

                if (actionObj.title == 'Inactivar cliente' || actionObj.title == 'Activar cliente') {
                    this.openDeleteModal();
                } else if (actionObj.title == this.$t('edit')) {
                    this.selectedUrl = `${actions.CLIENTES}/${rowData.id}`;
                    this.openAddEditModal();
                } else if (actionObj.title == 'eSIM Gratis') {
                    this.toggleFreeEsim(rowData);
                } else if (actionObj.title == 'Enviar correo') {
                    this.sendAccessEmail(rowData);
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

            sendAccessEmail(rowData) {
                const url = `/clientes/${rowData.id}/send-access-email`;

                axios.post(url, {})
                    .then(response => {
                        this.$toastr.s(response.data.message || 'Correo enviado.');
                    })
                    .catch(error => {
                        const message = error.response && error.response.data && error.response.data.message
                            ? error.response.data.message
                            : 'No fue posible enviar el correo.';
                        this.$toastr.e(message);
                    });
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
                let url = `${actions.CLIENTES}/${this.rowData.id}/toggle-status`;
                this.deleteLoader=true;
                axios.post(url, {})
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
