<template>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <app-breadcrumb :page-title="'Facturación Nomad (API)'" :directory="'Transacciones'" :icon="'dollar-sign'"/>
            </div>
        </div>

        <!-- Nomad Debt Summary Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title text-warning mb-1">
                                    <app-icon name="alert-circle" style="width:20px;height:20px;" class="mr-1"/>
                                    Facturación Nomad (Proveedor API)
                                </h5>
                                <p class="text-muted mb-0" style="font-size: 13px;">
                                    Suma del precio de la API (<code>api_price</code>) para el rango de fechas seleccionado.
                                    Por defecto muestra el mes anterior.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                <div v-if="statsLoading" class="text-muted">
                                    <app-icon name="loader" style="width:20px;height:20px;"/> Calculando...
                                </div>
                                <div v-else>
                                    <span class="display-5 font-weight-bold text-danger" style="font-size: 2rem;">
                                        ${{ stats.total_api_price }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ stats.count }} transacciones · {{ stats.start_date }} al {{ stats.end_date }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Row -->
        <div class="row mb-3">
            <div class="col-12 d-flex align-items-center flex-wrap">
                <div class="mr-2 mb-1" style="min-width: 280px;">
                    <app-search @input="getSearchValue"/>
                </div>
                <div class="mr-2 mb-1" style="min-width: 170px;">
                    <app-input type="date"
                               v-model="activeFilters.start_date"
                               :placeholder="$t('start_date')"
                               @input="applyFilters"/>
                </div>
                <div class="mr-2 mb-1" style="min-width: 170px;">
                    <app-input type="date"
                               v-model="activeFilters.end_date"
                               :placeholder="$t('end_date')"
                               @input="applyFilters"/>
                </div>
                <button v-if="hasActiveFilters"
                        type="button"
                        class="btn btn-sm btn-outline-secondary mb-1"
                        @click="clearAllFilters">
                    {{ $t('clear') }}
                </button>
            </div>
        </div>

        <app-table :id="tableId" :options="options" :search="search" @action="getListAction"/>

        <detail-modal v-if="isDetailModalActive"
                      :transaction-id="rowData.id"
                      @close-modal="closeDetailModal"/>
    </div>
</template>

<script>
    import CoreLibrary from "../../../../../core/helpers/CoreLibrary.js";
    import * as actions from "../../../../Config/ApiUrl";
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {TableWithoutWrapperMixin} from '../../../../Mixins/TableWithoutWrapperMixin.js';
    import DetailModal from "./DetailModal";

    export default {
        extends: CoreLibrary,
        name: "NomadTransactionsList",
        mixins: [FormMixin, TableWithoutWrapperMixin],
        components: {
            DetailModal,
        },
        data() {
            return {
                isDetailModalActive: false,
                tableId: 'nomad-transactions-table',
                rowData: {},
                statsLoading: false,
                stats: {
                    total_api_price: '0.00',
                    count: 0,
                    start_date: '',
                    end_date: '',
                },
                activeFilters: {
                    start_date: '',
                    end_date: '',
                },
                options: {
                    url: actions.TRANSACTIONS,
                    name: 'Transacciones Nomad',
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
                            modifier: (value) => value || 'N/A',
                        },
                        {
                            title: this.$t('data_amount'),
                            type: 'custom-html',
                            key: 'data_amount',
                            modifier: (value) => value ? `${value} GB` : 'N/A',
                        },
                        {
                            title: 'Precio API (Nomad)',
                            type: 'custom-html',
                            key: 'api_price',
                            modifier: (value) => {
                                if (value === null || value === undefined || value === '') {
                                    return '<span class="text-muted">N/A</span>';
                                }
                                return `<span class="font-weight-bold text-danger">$${parseFloat(value).toFixed(2)}</span>`;
                            },
                        },
                        {
                            title: this.$t('purchase_amount'),
                            type: 'custom-html',
                            key: 'purchase_amount',
                            modifier: (value) => {
                                if (value === 0 || value === '0' || value === 0.0) {
                                    return `<span class="badge badge-success">${this.$t('free')}</span>`;
                                }
                                return value ? `$${parseFloat(value).toFixed(2)}` : 'N/A';
                            },
                        },
                        {
                            title: 'Partner / Super Partner',
                            type: 'custom-html',
                            key: 'partner_name',
                            modifier: (value) => value || 'N/A',
                        },
                        {
                            title: this.$t('client_name'),
                            type: 'custom-html',
                            key: 'cliente',
                            modifier: (value) => value ? `${value.nombre} ${value.apellido}` : 'N/A',
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
                            isVisible: true,
                        },
                    ],
                    actions: [
                        {
                            title: this.$t('details'),
                            icon: 'eye',
                            type: 'none',
                            component: 'app-detail-modal',
                            modalId: 'transaction-detail-modal',
                        },
                    ],
                    showFilter: false,
                    showSearch: true,
                    paginationType: "pagination",
                    responsive: true,
                    rowLimit: 10,
                    showAction: true,
                    orderBy: 'desc',
                    actionType: "default",
                },
            };
        },
        computed: {
            hasActiveFilters() {
                return this.search || this.activeFilters.start_date || this.activeFilters.end_date;
            },
        },
        mounted() {
            // Default to last month
            const now = new Date();
            const firstOfThisMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const firstOfLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const lastOfLastMonth = new Date(firstOfThisMonth - 1);

            this.activeFilters.start_date = this.toDateString(firstOfLastMonth);
            this.activeFilters.end_date = this.toDateString(lastOfLastMonth);

            this.applyFilters();
        },
        methods: {
            toDateString(date) {
                return date.toISOString().split('T')[0];
            },

            applyFilters() {
                const params = new URLSearchParams();
                if (this.activeFilters.start_date) params.append('start_date', this.activeFilters.start_date);
                if (this.activeFilters.end_date) params.append('end_date', this.activeFilters.end_date);

                const qs = params.toString();
                this.options.url = actions.TRANSACTIONS + (qs ? '?' + qs : '');

                this.$nextTick(() => {
                    this.$hub.$emit('reload-' + this.tableId);
                });

                this.loadStats();
            },

            loadStats() {
                this.statsLoading = true;
                const params = new URLSearchParams();
                if (this.activeFilters.start_date) params.append('start_date', this.activeFilters.start_date);
                if (this.activeFilters.end_date) params.append('end_date', this.activeFilters.end_date);

                this.axiosGet(actions.TRANSACTIONS_NOMAD_DEBT_STATS + (params.toString() ? '?' + params.toString() : ''))
                    .then(response => {
                        this.stats = {
                            total_api_price: parseFloat(response.data.total_api_price || 0).toFixed(2),
                            count: response.data.count || 0,
                            start_date: response.data.start_date || '',
                            end_date: response.data.end_date || '',
                        };
                    })
                    .catch(error => {
                        console.error('Error loading nomad debt stats:', error);
                    })
                    .finally(() => {
                        this.statsLoading = false;
                    });
            },

            clearAllFilters() {
                this.search = '';
                this.activeFilters = { start_date: '', end_date: '' };
                this.options.url = actions.TRANSACTIONS;
                this.$nextTick(() => {
                    this.$hub.$emit('reload-' + this.tableId);
                });
                this.loadStats();
            },

            getListAction(rowData, actionObj) {
                this.rowData = rowData;
                if (actionObj.title === this.$t('details')) {
                    this.openDetailModal();
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

            reSetData() {
                this.rowData = {};
            },
        },
    };
</script>
