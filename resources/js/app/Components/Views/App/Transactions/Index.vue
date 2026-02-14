<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="$t('transactions')" :directory="$t('transactions')" :icon="'list'"/>
            </div>
            <div class="col-sm-12 col-md-6 breadcrumb-side-button">
                <div class="float-md-right mb-3 mb-sm-3 mb-md-0">
                    <!-- Show unpaid transactions stats -->
                    <span v-if="showPaymentStats" class="badge badge-warning mr-2 p-2" style="font-size: 14px;">
                        {{ $t('unpaid_transactions') }}: {{ paymentStats.unpaid_count }} (${{ paymentStats.total_owed }})
                    </span>
                    
                    <!-- Mark as Paid button for admin -->
                    <button v-if="isAdmin" 
                            type="button"
                            class="btn btn-success btn-with-shadow mr-2"
                            data-toggle="modal"
                            @click="openMarkAsPaidModal">
                        {{ $t('mark_as_paid') }}
                    </button>
                    
                    <!-- Add button -->
                    <button type="button"
                            class="btn btn-primary btn-with-shadow"
                            data-toggle="modal"
                            @click="openAddEditModal">
                        {{ $t('add') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="btn-group mr-2" role="group">
                    <button type="button" 
                            class="btn btn-sm"
                            :class="transactionTypeFilter === null ? 'btn-primary' : 'btn-outline-primary'"
                            @click="filterByType(null)">
                        {{ $t('all') }}
                    </button>
                    <button type="button" 
                            class="btn btn-sm"
                            :class="transactionTypeFilter === 'free' ? 'btn-primary' : 'btn-outline-primary'"
                            @click="filterByType('free')">
                        {{ $t('free') }}
                    </button>
                    <button type="button" 
                            class="btn btn-sm"
                            :class="transactionTypeFilter === 'paid' ? 'btn-primary' : 'btn-outline-primary'"
                            @click="filterByType('paid')">
                        {{ $t('payment_plans') }}
                    </button>
                </div>

                <!-- Beneficiary filter for admin -->
                <div v-if="isAdmin" class="d-inline-block" style="min-width: 200px;">
                    <app-input type="select"
                               v-model="beneficiarioFilter"
                               :list="beneficiariosList"
                               :placeholder="$t('filter_by_beneficiary')"
                               @input="filterByBeneficiario"/>
                </div>

                <!-- Payment status filter -->
                <div class="btn-group ml-2" role="group">
                    <button type="button" 
                            class="btn btn-sm"
                            :class="paymentStatusFilter === null ? 'btn-secondary' : 'btn-outline-secondary'"
                            @click="filterByPaymentStatus(null)">
                        {{ $t('all_status') }}
                    </button>
                    <button type="button" 
                            class="btn btn-sm"
                            :class="paymentStatusFilter === 'unpaid' ? 'btn-secondary' : 'btn-outline-secondary'"
                            @click="filterByPaymentStatus('unpaid')">
                        {{ $t('unpaid') }}
                    </button>
                    <button type="button" 
                            class="btn btn-sm"
                            :class="paymentStatusFilter === 'paid' ? 'btn-secondary' : 'btn-outline-secondary'"
                            @click="filterByPaymentStatus('paid')">
                        {{ $t('paid') }}
                    </button>
                </div>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <add-modal v-if="isAddEditModalActive"
                   :table-id="tableId"
                   :selected-url="selectedUrl"
                   @close-modal="closeAddEditModal"/>

        <mark-as-paid-modal v-if="isMarkAsPaidModalActive"
                           :table-id="tableId"
                           @close-modal="closeMarkAsPaidModal"/>

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
    import MarkAsPaidModal from "./MarkAsPaidModal"; 

    export default {
        extends: CoreLibrary,
        name: "TransactionsList",
        components: {
            AddModal,
            MarkAsPaidModal
        },
        data() {
            return {
                deleteLoader: false,
                isAddEditModalActive: false,
                isMarkAsPaidModalActive: false,
                deleteConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'transactions-table',
                rowData: {},
                transactionTypeFilter: null,
                beneficiarioFilter: '',
                paymentStatusFilter: null,
                beneficiariosList: [],
                paymentStats: {
                    unpaid_count: 0,
                    total_owed: 0
                },
                options: {
                    url: actions.TRANSACTIONS,
                    name: this.$t('transactions'),
                    datatableWrapper: false,
                    showHeader: true,
                    filters: [
                        {
                            title: this.$t('type'),
                            type: 'dropdown',
                            key: 'type',
                            option: []
                        },
                        {
                            title: this.$t('beneficiario_id'),
                            type: 'dropdown',
                            key: 'beneficiario_id',
                            option: []
                        },
                        {
                            title: this.$t('payment_status'),
                            type: 'dropdown',
                            key: 'payment_status',
                            option: []
                        }
                    ],
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
                                return value ? `${value} ${this.$t('days')}` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('purchase_amount'),
                            type: 'custom-html',
                            key: 'purchase_amount',
                            modifier: (value, row) => {
                                if (value == 0) {
                                    return `<span class="badge badge-success">${this.$t('free')}</span>`;
                                }
                                return value ? `$${parseFloat(value).toFixed(2)}` : 'N/A';
                            }
                        },
                        {
                            title: this.$t('commission'),
                            type: 'custom-html',
                            key: 'commission_amount',
                            modifier: (value, row) => {
                                const commission = parseFloat(value || 0).toFixed(2);
                                const percentage = row.commission_percentage || 0;
                                if (row.purchase_amount == 0) {
                                    return `$${commission}`;
                                }
                                return `$${commission} (${percentage}%)`;
                            }
                        },
                        {
                            title: this.$t('beneficiary'),
                            type: 'custom-html',
                            key: 'beneficiario',
                            modifier: (value) => {
                                return value ? value.nombre : 'N/A';
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
                            title: this.$t('payment_status'),
                            type: 'custom-html',
                            key: 'is_paid',
                            modifier: (value) => {
                                if (value) {
                                    return `<span class="badge badge-success">${this.$t('paid')}</span>`;
                                }
                                return `<span class="badge badge-warning">${this.$t('unpaid')}</span>`;
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
        computed: {
            isAdmin() {
                return this.$store.state.user && this.$store.state.user.user_type === 'admin';
            },
            showPaymentStats() {
                return this.paymentStats.unpaid_count > 0;
            }
        },
        mounted() {
            this.loadPaymentStats();
            if (this.isAdmin) {
                this.loadBeneficiarios();
            }
        },
        methods: {
            loadPaymentStats() {
                this.axiosGet(actions.TRANSACTIONS_PAYMENT_STATS)
                    .then(response => {
                        this.paymentStats = response.data;
                    })
                    .catch(error => {
                        console.error('Error loading payment stats:', error);
                    });
            },

            loadBeneficiarios() {
                this.axiosGet(actions.BENEFICIARIOS + '?per_page=1000')
                    .then(response => {
                        this.beneficiariosList = [
                            { id: '', value: this.$t('all_beneficiaries') },
                            ...response.data.data.map(beneficiario => ({
                                id: beneficiario.id,
                                value: beneficiario.nombre
                            }))
                        ];
                    })
                    .catch(error => {
                        console.error('Error loading beneficiaries:', error);
                    });
            },

            filterByType(type) {
                this.transactionTypeFilter = type;
                this.$hub.$emit(`reload-${this.tableId}`, {
                    type: type
                });
            },

            filterByBeneficiario() {
                this.$hub.$emit(`reload-${this.tableId}`, {
                    beneficiario_id: this.beneficiarioFilter
                });
            },

            filterByPaymentStatus(status) {
                this.paymentStatusFilter = status;
                this.$hub.$emit(`reload-${this.tableId}`, {
                    payment_status: status
                });
            },

            openAddEditModal() {
                this.isAddEditModalActive = true;
            },

            closeAddEditModal() {
                $("#transaction-add-edit-modal").modal('hide'); 
                this.isAddEditModalActive = false;
                this.reSetData();
            },

            openMarkAsPaidModal() {
                this.isMarkAsPaidModalActive = true;
            },

            closeMarkAsPaidModal() {
                $("#mark-as-paid-modal").modal('hide');
                this.isMarkAsPaidModalActive = false;
                this.loadPaymentStats(); // Reload stats after marking as paid
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
                    this.loadPaymentStats();
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
