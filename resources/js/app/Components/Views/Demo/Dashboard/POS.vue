<template>
    <div class="content-wrapper">
        <app-overlay-loader v-if="loader"></app-overlay-loader>
        <div v-else>
            <h3 class="mb-3">{{ $t('pos') }}</h3>
            <div>
                <!-- highlight card -->
                <div class="card mb-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>{{ $t('highlight') }}</div>
                        <div class="dropdown d-inline-block btn-dropdown">
                            <a class="btn btn-link text-primary shadow-none dropdown-toggle ml-0 mr-2" href="#"
                               role="button"
                               data-toggle="dropdown" aria-expanded="false">
                                <span class="text-capitalize">{{ $t(highlight) }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"
                                   @click="highlight = 'last_week'">{{ $t('last_week') }}</a>
                                <a class="dropdown-item" href="#"
                                   @click="highlight = 'last_month'">{{ $t('last_month') }}</a>
                                <a class="dropdown-item" href="#"
                                   @click="highlight = 'this_month'">{{ $t('this_month') }}</a>
                                <a class="dropdown-item" href="#"
                                   @click="highlight = 'this_year'">{{ $t('this_year') }}</a>
                                <a class="dropdown-item" href="#"
                                   @click="highlight = 'last_year'">{{ $t('last_year') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mb-5">
                        <app-overlay-loader v-if="isHighlightLoading"/>
                        <div v-else>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="grid-card d-flex align-items-center">
                                        <div class="icon text-white">
                                            <app-icon name="dollar-sign"/>
                                        </div>
                                        <div>
                                            <h5>
                                                {{ numberWithCurrencySymbol(highlightsData.totalSales) }}
                                            </h5>
                                            <div class="text-muted">{{ $t('total_sales') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!--  cashier can't view this portion -->
                                <div class="col-12 col-sm-6 col-md-3" v-if="userRole.id !== 5">
                                    <div class="grid-card d-flex align-items-center">
                                        <div class="icon text-white">
                                            <app-icon name="activity"/>
                                        </div>
                                        <div>
                                            <h5>{{ numberWithCurrencySymbol(highlightsData.totalExpense) }}</h5>
                                            <div class="text-muted">{{ $t('total_expenses') }}</div>
                                        </div>
                                    </div>
                                </div>


                                <!--  cashier can't view this portion -->
                                <div class="col-12 col-sm-6 col-md-3" v-if="userRole.id !== 5">
                                    <div class="grid-card d-flex align-items-center">
                                        <div class="icon text-white">
                                            <app-icon name="zap"/>
                                        </div>
                                        <div>
                                            <h5>{{ numberWithCurrencySymbol(highlightsData.netProfit) }}</h5>
                                            <div class="text-muted">{{ $t('net_profit') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!--  cashier can't view this portion -->
                                <div class="col-12 col-sm-6 col-md-3" v-if="userRole.id !== 5">
                                    <div class="grid-card d-flex align-items-center">
                                        <div class="icon text-white">
                                            <app-icon name="credit-card"/>
                                        </div>
                                        <div>
                                            <h5>{{ numberWithCurrencySymbol(highlightsData.totalDue) }}</h5>
                                            <div class="text-muted">{{ $t('due') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- charts -->
                <div class="row mb-primary">
                    <div class="col-md-8">
                        <div class="card card-with-shadow border-0 h-100 t">
                            <div class="card-header bg-transparent px-5 py-4">
                                <div class="d-flex align-items-center text-secondary">
                                    <h4 class="m-0 p-0">{{ $t("sales_and_purchase") }}</h4>

                                    <div class="dropdown d-inline-block btn-dropdown" v-if="!isSalesPurchaseLoading">
                                        <a class="btn btn-link text-primary shadow-none dropdown-toggle ml-0 mr-2"
                                           href="#"
                                           role="button"
                                           data-toggle="dropdown" aria-expanded="false">
                                            <span class="text-capitalize">{{ $t(salesDropdownMenu) }}</span>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'last_week'">{{ $t('last_week') }}</a>
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'this_week'">{{ $t('this_week') }}</a>
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'last_month'">{{ $t('last_month') }}</a>
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'this_month'">{{ $t('this_month') }}</a>
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'this_year'">{{ $t('this_year') }}</a>
                                            <a class="dropdown-item" href="#"
                                               @click="salesDropdownMenu = 'last_year'">{{ $t('last_year') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <app-overlay-loader v-if="isSalesPurchaseLoading"/>
                                <app-chart
                                    v-else
                                    type="bar-chart"
                                    :height="360"
                                    :labels="salesPurchaseData.labels"
                                    :data-sets="salesPurchaseData.dataSet"
                                />
                                <div class="d-flex justify-content-center align-items-center mt-2" style="gap: 15px;"
                                     v-if="!isSalesPurchaseLoading">
                                    <div v-for="(item, index) in salesPurchaseData.dataSet"
                                         :key="`chart-item-${index}`">
                                        <div class="d-flex align-items-center">
                    <span style="width: 10px; height: 10px; border-radius: 50%; margin-right:10px;"
                          :style="{ backgroundColor: item.backgroundColor }"></span>{{ item.label }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-with-shadow border-0 h-100">
                            <div class="card-header bg-transparent px-5 py-4">
                                <div class="d-flex align-items-center text-secondary">
                                    <h4 class="m-0 p-0">{{ $t('top_selling_products') }}</h4>
                                </div>
                            </div>
                            <template v-if="isTopSellingLoading">
                                <div class="card-body">
                                    <app-overlay-loader/>
                                </div>
                            </template>
                            <div class="card-body" v-else>
                                <app-chart class="mb-primary"
                                           type="dough-chart"
                                           :height="360"
                                           :labels="topSellingData.labels"
                                           :data-sets="topSellingData.dataSet"/>
                                <div class="chart-data-list">
                                    <div class="row">
                                        <div class="col d-inline-flex flex-wrap" style="gap: 10px;">
                                            <div v-for="(item, index) in topSellingData.dataSet[0].backgroundColor"
                                                 :key="index"
                                                 class="d-flex align-items-center">
                                                <div
                                                    style="width: 10px; height: 10px; border-radius: 3px; margin-right:10px;"
                                                    :style="{ backgroundColor: item }"></div>
                                                <span>{{
                                                        topSellingData.labels[index]
                                                    }} {{ topSellingData.dataSet[0].data[index] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- sales tables -->
                <div class="row mb-primary">
                    <div class="col">
                        <div class="card">
                            <div class="card-header bg-transparent px-5 py-4">
                                <div class="d-flex align-items-center text-secondary">
                                    <h4 class="m-0 p-0">{{ $t('recent_sales') }}</h4>
                                    <a class="btn btn-link text-primary" href="javascript:void(0)">{{ $t('view_all_invoices') }}</a>
                                </div>
                            </div>
                            <template v-if="isRecentSalesLoading">
                                <div class="card-body datatable">
                                    <app-overlay-loader/>
                                </div>
                            </template>
                            <div class="card-body datatable" v-else>
                                <custom-table :options="recentSalesTable.options" :data="recentSalesTable.data"
                                              @activate-invoice-view-modal="handleInvoiceModalActiveEvent"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header bg-transparent px-5 py-4">
                                <div class="d-flex align-items-center text-secondary">
                                    <h4 class="m-0 p-0">{{ $t('stock_summary') }}</h4> ({{ $t('all_branch') }})
                                    <a class="btn btn-link text-primary" href="javascript:void(0)" >{{ $t('view_all_stocks') }}</a>
                                </div>
                            </div>
                            <template v-if="isStockSummaryLoading">
                                <div class="card-body datatable">
                                    <app-overlay-loader/>
                                </div>
                            </template>
                            <div class="card-body datatable" v-else>
                                <custom-table :options="stockSummaryData.options" :data="stockSummaryData.data"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header bg-transparent px-5 py-4">
                                <div class="d-flex align-items-center text-secondary">
                                    <h4 class="m-0 p-0">{{ $t("top_customer") }}</h4>
                                    <a class="btn btn-link text-primary" href="javascript:void(0)" >{{ $t('view_all_customers') }}</a>
                                </div>
                            </div>
                            <template v-if="isCustomerLoading">
                                <div class="card-body datatable">
                                    <app-overlay-loader/>
                                </div>
                            </template>
                            <div class="card-body datatable" v-else>
                                <custom-table :options="topCustomer.options" :data="topCustomer.data"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CustomTable from "./CustomTable";
import {mapGetters} from "vuex";
import {axiosGet, urlGenerator} from "../../../../Helpers/AxiosHelper";
import {getCurrencySymbol, numberWithCurrencySymbol} from "../../../../Helpers/Helpers";
import {recentSalesTable, salesPurchaseData, topCustomer, stockSummaryData, topSellingData} from './dummy'

export default {
    components: {CustomTable},
    data() {
        return {
            userRole: '',
            numberWithCurrencySymbol,
            invoiceModalActive: false,
            invoiceIdToShow: '',
            getCurrencySymbol,
            isSalesPurchaseLoading: false,
            salesDropdownMenu: 'this_week',
            salesPurchaseData: {
                labels: [],
                dataSet: [
                    {
                        label: this.$t('sales'),
                        barPercentage: 0.5,
                        barThickness: 30,
                        borderWidth: 1,
                        backgroundColor: "#3874ff",
                        data: []
                    },
                    {
                        label: this.$t('purchase'),
                        barPercentage: 0.5,
                        barThickness: 30,
                        borderWidth: 1,
                        backgroundColor: "#5bc5d5",
                        data: []
                    }
                ]
            },
            isCustomerLoading: false,
            topCustomer: {
                options: {
                    cols: [
                        {name: this.$t('customer_name'), key: 'name', class: 'text-primary'},
                        {name: this.$t('total_amount'), key: 'amount', class: '', alignment: 'right'}
                    ]
                },
                data: []
            },
            isStockSummaryLoading: false,
            stockSummaryData: {
                options: {
                    cols: [
                        {name: this.$t('stock_alert'), key: 'name', class: 'text-primary'},
                        {name: this.$t('quantity'), key: 'amount', class: 'text-danger', alignment: 'right'}
                    ]
                },
                data: []
            },
            isHighlightLoading: false,
            highlight: 'this_week',
            highlightsData: {},
            loader: false,
            isTopSellingLoading: false,
            topSellingData: {
                labels: [],
                dataSet: [
                    {
                        barThickness: 30,
                        borderWidth: 0,
                        backgroundColor: [
                            '#00cc4a',
                            '#54caf2',
                            '#bb6bdb',
                            '#f28319',
                            '#808080'
                        ],
                        data: []
                    }
                ]
            },
            isRecentSalesLoading: false,
            recentSalesTable: {
                options: {
                    cols: [
                        {name: this.$t('invoice_id'), key: 'id', class: 'text-primary', type: 'modal'},
                        {name: this.$t('customer'), key: 'customer', class: 'text-primary',},
                        {name: this.$t('grand_total'), key: 'total', alignment: 'right'},
                        {name: this.$t('pain'), key: 'paid', alignment: 'right'},
                        {name: this.$t('due'), key: 'due', alignment: 'right'},
                        {name: this.$t('order_status'), key: 'status'}
                    ]
                },
                data: []
            },
        }
    },
    created() {
    },
    methods: {
        handleInvoiceModalActiveEvent(invoiceId) {
            this.invoiceIdToShow = invoiceId;
            this.invoiceModalActive = true;
        },
        getSalePurchaseStatistics() {
            this.salesPurchaseData = salesPurchaseData
        },
        getTopSellingProducts() {
            this.topSellingData = topSellingData
        },
        getHighlights() {
            this.highlightsData = {"totalSales": 5250, "netProfit": 0, "totalExpense": "6163", "totalDue": 0}
        },
        getRecentSales() {
            this.recentSalesTable = recentSalesTable
        },
        getStockSummary() {
            this.stockSummaryData = stockSummaryData
        },
        getTopCustomers() {
            this.topCustomer = topCustomer
        },
        viewAllInvoice() {
            window.location.replace(urlGenerator(`invoice`))
        },
        viewAllStock() {
            window.location.replace(urlGenerator(`inventory/stock`))
        },
        customerList() {
            window.location.replace(urlGenerator(`customer/lists`))
        }
    },
    mounted() {
        this.loader = true
        this.getHighlights();
        this.getTopSellingProducts();
        this.getRecentSales();
        this.getStockSummary();
        this.getTopCustomers();
        this.getSalePurchaseStatistics();
        this.$nextTick(() => {
            setTimeout(() => {
                this.loader = false
            }, 500)
        })
    },
    watch: {
        highlight(new_value) {
            this.highlight = new_value
            this.getHighlights();
        },
        salesDropdownMenu(new_value) {
            this.salesDropdownMenu = new_value
            this.getSalePurchaseStatistics();
        },
        getBranchOrWarehouseId(new_id) {
            this.getHighlights();
            this.getTopSellingProducts();
            this.getRecentSales();
            this.getStockSummary();
            this.getTopCustomers();
            this.getSalePurchaseStatistics();
        },
    }
}
</script>

<style scoped lang="scss">
.t {
    border: 1px solid red;
}

.grid-card {
    display: flex;
    gap: 15px;

    .icon {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
        width: 72px;
        height: 72px;
        background-color: #4466F2;
    }
}
</style>