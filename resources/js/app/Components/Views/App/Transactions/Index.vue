<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="$t('transactions')" :directory="$t('transactions')" :icon="'list'"/>
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
                          modal-id="transaction-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    
    import AddModal from "./AddModal"; 

    export default {
        extends: CoreLibrary,
        name: "TransactionsList",
        components: {
            AddModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'transactions-table',
                rowData: {},
                options: {
                    url: actions.TRANSACTIONS,
                    name: this.$t('transactions'),
                    datatableWrapper: false,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('transaction_id'),
                            type: 'text',
                            key: 'transaction_id',
                        },
                        {
                            title: this.$t('date'),
                            type: 'text',
                            key: 'creation_time',
                        },
                        {
                            title: this.$t('plan'),
                            type: 'text',
                            key: 'plan_name',
                            modifier: (value) => {
                                return value || 'N/A';
                            }
                        },
                        {
                            title: this.$t('data_amount'),
                            type: 'custom-html',
                            key: 'data_amount',
                            modifier: (value) => {
                                return value ? `${value} GB` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('duration'),
                            type: 'custom-html',
                            key: 'duration_days',
                            modifier: (value) => {
                                return value ? `${value} días` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('amount'),
                            type: 'custom-html',
                            key: 'purchase_amount',
                            modifier: (value, row) => {
                                if (value == 0) {
                                    return '<span class="badge badge-success">Gratis</span>';
                                }
                                return value ? `$${parseFloat(value).toFixed(2)}` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('client_name'),
                            type: 'custom-html',
                            key: 'cliente',
                            modifier: (value) => {
                                return value ? `${value.nombre} ${value.apellido}` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('status'),
                            type: 'text',
                            key: 'status',
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
                            modalId: 'transaction-add-edit-modal',
                        }, {
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'transaction-delete',
                        }
                    ],
                    showFilter: true,
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
        methods: {
            openAddEditModal() {
                this.isAddEditModalActive = true;
            },

            closeAddEditModal() {
                $("#transaction-add-edit-modal").modal('hide'); 
                this.isAddEditModalActive = false;
                this.reSetData();
            },

            getListAction(rowData, actionObj, active) {
                this.rowData = rowData;

                if (actionObj.title == this.$t('delete')) {
                    this.openDeleteModal();
                } else if (actionObj.title == this.$t('edit')) {
                    this.selectedUrl = `${actions.TRANSACTIONS}/${rowData.id}`;
                    this.openAddEditModal();
                }
            },

            openDeleteModal() {
                this.deleteConfirmationModalActive = true;
            },

            confirmed() {
                let url = `${actions.TRANSACTIONS}/${this.rowData.id}`;
                this.deleteLoader=true;
                this.axiosDelete(url)
                    .then(response => {
                        this.deleteLoader= false;
                        $("#transaction-delete").modal('hide');
                        this.cancelled();
                        this.$toastr.s(response.data.message);
                    }).catch(({error}) => {

                    //trigger after error
                }).finally(() => {

                    this.$hub.$emit('reload-' + this.tableId);
                });
            },

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
