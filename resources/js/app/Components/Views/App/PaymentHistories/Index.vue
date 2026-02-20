<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="$t('payment_histories')" :directory="$t('payment_histories')" :icon="'credit-card'"/>
            </div>
        </div>

        <app-table :id="tableId" :options="options" @action="getListAction"/>

        <app-delete-modal v-if="deleteConfirmationModalActive"
                          :preloader="deleteLoader"
                          modal-id="payment-history-delete"
                          @confirmed="confirmed"
                          @cancelled="cancelled"/>
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
                deleteLoader: false,
                deleteConfirmationModalActive: false,
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
                            title: this.$t('delete'),
                            icon: 'trash',
                            type: 'none',
                            component: 'app-confirmation-modal',
                            modalId: 'payment-history-delete',
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
            isAdmin() {
                return this.$store.state.user &&
                       this.$store.state.user.loggedInUser &&
                       (this.$store.state.user.loggedInUser.role === 'Admin' ||
                        this.$store.state.user.loggedInUser.user_type === 'admin');
            }
        },
        methods: {
            getListAction(rowData, actionObj, active) {
                this.rowData = rowData;
                if (actionObj.title == this.$t('delete')) {
                    this.openDeleteModal();
                }
            },

            openDeleteModal() {
                this.deleteConfirmationModalActive = true;
            },

            confirmed() {
                let url = `${actions.PAYMENT_HISTORIES}/${this.rowData.id}`;
                this.deleteLoader = true;
                this.axiosDelete(url)
                    .then(response => {
                        this.deleteLoader = false;
                        $("#payment-history-delete").modal('hide');
                        this.cancelled();
                        this.$toastr.s(response.data.message);
                    }).catch(({error}) => {
                    }).finally(() => {
                        this.$hub.$emit('reload-' + this.tableId);
                    });
            },

            cancelled() {
                this.deleteConfirmationModalActive = false;
                this.rowData = {};
            }
        }
    }
</script>
