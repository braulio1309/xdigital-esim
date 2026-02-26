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

                <!-- Beneficiary indicator for beneficiary users -->
                <div v-else-if="isBeneficiario" class="d-inline-block ml-2">
                    <span class="badge badge-info p-2" style="font-size: 13px;">
                        <app-icon name="filter" class="pr-1" style="width:14px;height:14px;"/>
                        {{ $t('my_transactions') }}
                    </span>
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

        <!-- Date range filter row -->
        <div class="row mb-3">
            <div class="col-12 d-flex align-items-center flex-wrap">
                <div class="mr-2 mb-1" style="min-width: 170px;">
                    <app-input type="date"
                               v-model="filterStartDate"
                               :placeholder="$t('start_date')"
                               @input="onDateFilterChange"/>
                </div>
                <div class="mr-2 mb-1" style="min-width: 170px;">
                    <app-input type="date"
                               v-model="filterEndDate"
                               :placeholder="$t('end_date')"
                               @input="onDateFilterChange"/>
                </div>
                <div v-if="filterAmountLoading" class="mr-2 mb-1">
                    <span class="badge badge-secondary p-2" style="font-size: 13px;">
                        <app-icon name="loader" class="pr-1" style="width:14px;height:14px;"/>
                        {{ $t('loading') }}...
                    </span>
                </div>
                <div v-else-if="filterAmountResult !== null" class="mr-2 mb-1">
                    <span class="badge badge-info p-2" style="font-size: 14px;">
                        {{ $t('amount_owed') }}: ${{ filterAmountResult.amount }} ({{ filterAmountResult.count }} {{ $t('transactions') }})
                    </span>
                </div>
                <button v-if="filterStartDate || filterEndDate || beneficiarioFilter"
                        type="button"
                        class="btn btn-sm btn-outline-secondary mb-1"
                        @click="clearFilters">
                    {{ $t('clear') }}
                </button>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <add-modal v-if="isAddEditModalActive"
                   :table-id="tableId"
                   :selected-url="selectedUrl"
                   @close-modal="closeAddEditModal"/>

        <detail-modal v-if="isDetailModalActive"
                      :transaction-id="rowData.id"
                      @close-modal="closeDetailModal"/>

        <mark-as-paid-modal v-if="isMarkAsPaidModalActive"
                           :table-id="tableId"
                           :prefill-beneficiario-id="beneficiarioFilter"
                           :prefill-start-date="filterStartDate"
                           :prefill-end-date="filterEndDate"
                           @close-modal="closeMarkAsPaidModal"/>

        <app-delete-modal v-if="deleteConfirmationModalActive"
                          :preloader="deleteLoader"
                          modal-id="transaction-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>

        <app-delete-modal v-if="terminateConfirmationModalActive"
                          :preloader="terminateLoader"
                          modal-id="transaction-terminate"
                          @confirmed="confirmedTerminate"
                          @cancelled="cancelledTerminate"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';

    import AddModal from "./AddModal"; 
    import DetailModal from "./DetailModal";
    import MarkAsPaidModal from "./MarkAsPaidModal"; 

    export default {
        extends: CoreLibrary,
        name: "TransactionsList",
        mixins: [FormMixin],
        components: {
            AddModal,
            DetailModal,
            MarkAsPaidModal
        },
        data() {
            return {
                deleteLoader: false,
                terminateLoader: false,
                isAddEditModalActive: false,
                isDetailModalActive: false,
                isMarkAsPaidModalActive: false,
                deleteConfirmationModalActive: false,
                terminateConfirmationModalActive: false,
                selectedUrl: '',
                tableId: 'transactions-table',
                rowData: {},
                transactionTypeFilter: null,
                beneficiarioFilter: '',
                paymentStatusFilter: null,
                filterStartDate: '',
                filterEndDate: '',
                filterAmountResult: null,
                filterAmountLoading: false,
                filterAmountTimer: null,
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
                                if (value === 0 || value === '0' || value === 0.0) {
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
                                if (row.purchase_amount === 0 || row.purchase_amount === '0' || row.purchase_amount === 0.0) {
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
                            title: this.$t('details'),
                            icon: 'eye',
                            type: 'none',
                            component: 'app-detail-modal',
                            modalId: 'transaction-detail-modal',
                        }, {
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
                        }, {
                            title: this.$t('terminate_subscription'),
                            icon: 'slash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'transaction-terminate',
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
                // Check if the logged in user is admin by checking their role
                return this.$store.state.user && 
                       this.$store.state.user.loggedInUser && 
                       (this.$store.state.user.loggedInUser.role === 'Admin' || 
                        this.$store.state.user.loggedInUser.user_type === 'admin');
            },
            isBeneficiario() {
                return this.$store.state.user && 
                       this.$store.state.user.loggedInUser && 
                       this.$store.state.user.loggedInUser.user_type === 'beneficiario';
            },
            showPaymentStats() {
                return this.paymentStats.unpaid_count > 0;
            }
        },
        mounted() {
            this.loadPaymentStats();
           // if (this.isAdmin) {
                this.loadBeneficiarios();
           // }
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
                this.fetchFilterAmount();
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

            onDateFilterChange() {
                this.$hub.$emit(`reload-${this.tableId}`, {
                    start_date: this.filterStartDate,
                    end_date: this.filterEndDate
                });
                this.fetchFilterAmount();
            },

            fetchFilterAmount() {
                if (this.filterAmountTimer) {
                    clearTimeout(this.filterAmountTimer);
                }
                const hasFilters = this.beneficiarioFilter || this.filterStartDate || this.filterEndDate;
                if (!hasFilters) {
                    this.filterAmountResult = null;
                    return;
                }
                this.filterAmountTimer = setTimeout(() => {
                    this.filterAmountLoading = true;
                    const params = {};
                    if (this.beneficiarioFilter) params.beneficiario_id = this.beneficiarioFilter;
                    if (this.filterStartDate) params.start_date = this.filterStartDate;
                    if (this.filterEndDate) params.end_date = this.filterEndDate;
                    this.axiosGet(actions.TRANSACTIONS_CALCULATE_AMOUNT + '?' + new URLSearchParams(params).toString())
                        .then(response => {
                            this.filterAmountResult = response.data;
                        })
                        .catch(error => {
                            console.error('Error calculating payment amount:', error);
                        })
                        .finally(() => {
                            this.filterAmountLoading = false;
                        });
                }, 500);
            },

            clearFilters() {
                this.filterStartDate = '';
                this.filterEndDate = '';
                this.beneficiarioFilter = '';
                this.filterAmountResult = null;
                this.$hub.$emit(`reload-${this.tableId}`, {
                    start_date: null,
                    end_date: null,
                    beneficiario_id: null
                });
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
                } else if (actionObj.title == this.$t('details')) {
                    this.openDetailModal();
                } else if (actionObj.title == this.$t('terminate_subscription')) {
                    this.openTerminateModal();
                }
            },

            openDetailModal() {
                this.isDetailModalActive = true;
            },

            closeDetailModal() {
                $("#transaction-detail-modal").modal('hide');
                this.isDetailModalActive = false;
                this.reSetData();
            },

            openTerminateModal() {
                this.terminateConfirmationModalActive = true;
            },

            confirmedTerminate() {
                this.terminateLoader = true;
                this.axiosPost({
                    url: `/transactions/${this.rowData.id}/terminate-subscription`,
                    data: {}
                }).then(response => {
                    this.terminateLoader = false;
                    $("#transaction-terminate").modal('hide');
                    this.cancelledTerminate();
                    this.$toastr.s(response.data.message);
                }).catch(error => {
                    this.terminateLoader = false;
                    this.$toastr.e(error.response?.data?.message || this.$t('error_terminating_subscription'));
                }).finally(() => {
                    this.$hub.$emit('reload-' + this.tableId);
                });
            },

            cancelledTerminate() {
                this.terminateConfirmationModalActive = false;
                this.reSetData();
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
