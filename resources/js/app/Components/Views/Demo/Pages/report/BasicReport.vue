<template>
    <div class="--content-wrapper">
        <!-- Beneficiario Filter -->
        <div class="row mb-4">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>{{ $t('Filtrar por Beneficiario') }}</label>
                    <app-input 
                        type="select"
                        v-model="selectedBeneficiario"
                        :list="beneficiarios"
                        list-value-field="id"
                        placeholder="Todos los Beneficiarios"
                        @input="onBeneficiarioChange"/>
                </div>
            </div>
        </div>

        <div class="card card-with-shadow border-0 h-100">
            <div class="card-header bg-transparent d-flex justify-content-end align-items-center p-primary">
                <p class="my-0 mr-2 p-0">{{ $t('Ordenar por') }}</p>
                <app-input type="radio-buttons" v-model="reportUnit" :list="unitList"/>
            </div>
            <div class="card-body pt-primary">
                <div class="chart-container position-relative min-height-380"
                     :class="{'loading-opacity':preloader}">
                    <app-overlay-loader v-if="preloader"/>
                    <app-chart v-else
                               type="horizontal-line-chart"
                               :height="380"
                               :labels="reportChart.labels"
                               :data-sets="reportChart.dataSets"/>
                </div>

                <hr class="mx-minus-primary my-primary">
                <app-table
                    class="remove-datatable-x-padding"
                    :id="reportTableId"
                    :options="options"
                />
            </div>
        </div>
    </div>
</template>

<script>
import {FormMixin} from "../../../../../../core/mixins/form/FormMixin";

export default {
    name: "Report",
    mixins: [FormMixin],
    data() {
        return {
            // Order report by unit
            preloader: false,
            selectedBeneficiario: null,
            beneficiarios: [],
            reportUnit: 'count',
            unitList: [
                {
                    id: 'count',
                    value: this.$t('Cantidad')
                },
                {
                    id: 'value',
                    value: this.$t('Monto')
                }
            ],
            // Chart Static Value
            reportChart: {
                labels: [],
                dataSets: [
                    {
                        barPercentage: 0.5,
                        barThickness: 30,
                        backgroundColor: [],
                        data: []
                    }
                ]
            },

            // Report Table
            reportTableId: 'report-table-transactions',
            options: {
                url: '/app/report-transactions/basic-report',
                tableShadow: false,
                datatableWrapper: false,
                showFilter: false,
                showSearch: false,
                managePagination: false,
                queryParams: {
                    beneficiario_id: null
                },
                afterRequestSuccess: ({data}) => {
                    this.setupChartForTable(data);
                },
                columns: [
                    {
                        title: this.$t('Plan'),
                        type: 'text',
                        key: 'name',
                    },
                    {
                        title: this.$t('Cantidad'),
                        type: 'text',
                        key: 'count'
                    },
                    {
                        title: this.$t('Monto Total'),
                        type: 'text',
                        key: 'value',
                        modifier: (value) => `$${value}`
                    }
                ],
            }
        }
    },
    watch: {
        reportUnit: {
            handler: 'reportByOrder'
        }
    },
    created() {
        this.preloader = true;
        this.loadBeneficiarios();
    },
    methods: {
        loadBeneficiarios() {
            return this.axiosGet('/app/report-transactions/beneficiarios')
                .then(response => {
                    this.beneficiarios = response.data;
                })
                .catch(error => {
                    console.error('Error loading beneficiarios:', error);
                });
        },
        onBeneficiarioChange() {
            this.options.queryParams.beneficiario_id = this.selectedBeneficiario;
            this.$hub.$emit(`reload-${this.reportTableId}`);
        },
        setupChartForTable({data}){
            this.reportChartData = data;
            this.getChartData();
        },
        reportByOrder() {
            this.getChartData()
        },
        getChartData() {
            this.preloader = true;
            this.reportChart.labels = [];
            this.reportChart.dataSets[0].backgroundColor = [];
            this.reportChart.dataSets[0].data = [];
            let midIndex = Math.ceil(this.reportChartData.length / 2);
            this.reportChartData.forEach((item, index) => {
                if (index === midIndex) {
                    this.reportChart.labels.push(this.$t('Promedio'));
                    this.reportChart.dataSets[0].backgroundColor.push('#4FE892');
                    this.reportChart.dataSets[0].data.push(this.getAverageValue());
                }
                this.reportChart.labels.push(item.name);
                this.reportChart.dataSets[0].backgroundColor.push('#2E69FF');
                this.reportChart.dataSets[0].data.push(item[this.reportUnit]);
            });
            setTimeout(() => {
                this.preloader = false
            });
        },
        getAverageValue() {
            let list = _.map(this.reportChartData, this.reportUnit),
                total = list.reduce((result, item) => Number(result) + Number(item));
            return total/this.reportChartData.length;
        }
    }
}
</script>
