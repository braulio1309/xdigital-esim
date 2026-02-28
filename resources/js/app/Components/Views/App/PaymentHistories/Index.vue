<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="$t('payment_histories')" :directory="$t('payment_histories')" :icon="'credit-card'"/>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <!-- Void (anular) confirmation modal -->
        <app-delete-modal v-if="cancelConfirmationModalActive"
                          :preloader="cancelLoader"
                          modal-id="payment-history-cancel"
                          @confirmed="confirmedCancel"
                          @cancelled="cancelledCancel"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";

    export default {
        extends: CoreLibrary,
        name: "PaymentHistoriesList",
        data() {
            return {
                cancelLoader: false,
                cancelConfirmationModalActive: false,
                tableId: 'payment-histories-table',
                rowData: {},
                options: {
                    url: actions.PAYMENT_HISTORIES,
                    name: this.$t('payment_histories'),
                    datatableWrapper: false,
                    showHeader: true,
                    columns: [
                        {
                            title: this.$t('beneficiary'),
                            type: 'custom-html',
                            key: 'beneficiario',
                            modifier: (value) => {
                                return value ? value.nombre : 'N/A';
                            }
                        },
                        {
                            title: this.$t('reference'),
                            type: 'custom-html',
                            key: 'reference',
                            modifier: (value) => {
                                return value || '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            title: this.$t('payment_date'),
                            type: 'text',
                            key: 'payment_date',
                        },
                        {
                            title: this.$t('transactions_count'),
                            type: 'custom-html',
                            key: 'transactions_count',
                            modifier: (value) => {
                                return `<span class="badge badge-info">${value || 0}</span>`;
                            }
                        },
                        {
                            title: this.$t('amount'),
                            type: 'custom-html',
                            key: 'amount',
                            modifier: (value) => {
                                return `$${parseFloat(value || 0).toFixed(2)}`;
                            }
                        },
                        {
                            title: this.$t('support'),
                            type: 'custom-html',
                            key: 'support_original_name',
                            modifier: (value, row) => {
                                if (!value) {
                                    return '<span class="text-muted">-</span>';
                                }
                                const url = `/payment-histories/${row.id}/download-support`;
                                return `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="download" style="width:14px;height:14px;"></i> ${value}
                                </a>`;
                            }
                        },
                        {
                            title: this.$t('notes'),
                            type: 'custom-html',
                            key: 'notes',
                            modifier: (value) => {
                                return value || '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            title: this.$t('status'),
                            type: 'custom-html',
                            key: 'status',
                            modifier: (value) => {
                                if (value === 'anulada') {
                                    return `<span class="badge badge-danger">${this.$t('anulada')}</span>`;
                                }
                                return `<span class="badge badge-success">${this.$t('active_status')}</span>`;
                            }
                        },
                        {
                            title: this.$t('created_at'),
                            type: 'text',
                            key: 'created_at',
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
                            title: this.$t('anular'),
                            icon: 'slash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'payment-history-cancel',
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
        methods: {
            getListAction(rowData, actionObj, active) {
                this.rowData = rowData;
                if (actionObj.title == this.$t('anular')) {
                    this.openCancelModal();
                }
            },

            openCancelModal() {
                this.cancelConfirmationModalActive = true;
            },

            confirmedCancel() {
                let url = `${actions.PAYMENT_HISTORIES_CANCEL(this.rowData.id)}`;
                this.cancelLoader = true;
                this.axiosPost({ url, data: {} })
                    .then(response => {
                        this.cancelLoader = false;
                        $("#payment-history-cancel").modal('hide');
                        this.cancelledCancel();
                        this.$toastr.s(response.data.message);
                    }).catch(({ response }) => {
                        this.cancelLoader = false;
                        this.$toastr.e(response?.data?.message || this.$t('something_went_wrong'));
                    }).finally(() => {
                        this.$hub.$emit('reload-' + this.tableId);
                    });
            },

            cancelledCancel() {
                this.cancelConfirmationModalActive = false;
                this.rowData = {};
            }
        }
    }
</script>

