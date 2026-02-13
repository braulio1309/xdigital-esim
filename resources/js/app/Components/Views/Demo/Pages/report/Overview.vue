<template>
    <div>
        <app-overlay-loader v-if="preloader" />
        <div v-else>
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

            <div class="row">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">Transacciones esta Semana</div>
                                <div class="h1">{{ overview.total_transactions_this_week }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">Ingresos Totales</div>
                                <div class="h1">${{ overview.total_revenue }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">eSIMs Gratuitas (Todo el Tiempo)</div>
                                <div class="h1">{{ overview.free_esims }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('Planes Activos') }}</div>
                                <div class="h1">{{ overview.active_plans }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>Tendencia de Transacciones</h4>
                            <app-chart class="mb-primary" type="line-chart" :height="230" :labels="performance.labels"
                                :data-sets="performance.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col-12 col-md-6">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>Transacciones por Beneficiario</h4>
                            <app-chart class="mb-primary" type="bar-chart" :height="230" :labels="topSources.labels"
                                :data-sets="topSources.dataSet" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>Distribución de Transacciones</h4>
                            <app-chart class="mb-primary" type="dough-chart" :height="230" :labels="distributionSources.labels"
                                :data-sets="distributionSources.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            labels: [],
            preloader: true,
            selectedBeneficiario: null,
            beneficiarios: [],
            overview: {
                total_transactions_this_week: 0,
                total_revenue: 0,
                free_esims: 0,
                active_plans: 0
            },
            performance: {
                labels: [],
                dataSet: []
            },
            topSources: {
                labels: [],
                dataSet: []
            },
            distributionSources: {
                labels: [],
                dataSet: []
            }
        }
    },
    methods: {
        genChartData(data) {
            return [
                {
                    barPercentage: 0.5,
                    barThickness: 15,
                    borderWidth: 1,
                    borderColor: [
                        "#5a86f1",
                        "#5bc5d5",
                        "#eb779e",
                        "#46cc97",
                        "#368cd5"
                    ],
                    backgroundColor: [
                        "#5a86f1",
                        "#5bc5d5",
                        "#eb779e",
                        "#46cc97",
                        "#368cd5"
                    ],
                    data: data.map(i => i.total_transactions)
                }
            ]
        },
        genPerformanceChartData(data) {
            return [
                {
                    label: 'Total',
                    data: data.map(i => i.total),
                    backgroundColor: '#5a86f1',
                    borderColor: '#5a86f1',
                    fill: false,
                    cubicInterpolationMode: 'monotone',
                    tension: 0.4
                }, 
                {
                    label: 'Ingresos',
                    data: data.map(i => i.revenue),
                    backgroundColor: '#eb779e',
                    borderColor: '#eb779e',
                    fill: false,
                    tension: 0.4
                }, 
                {
                    label: 'eSIMs Gratis',
                    data: data.map(i => i.free_esims),
                    backgroundColor: '#46cc97',
                    borderColor: '#46cc97',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        loadBeneficiarios() {
            this.axiosGet('/app/report-transactions/beneficiarios')
                .then(response => {
                    this.beneficiarios = response.data;
                })
                .catch(error => {
                    console.error('Error loading beneficiarios:', error);
                });
        },
        onBeneficiarioChange() {
            this.getOverview();
        },
        getOverview() {
            this.preloader = true;
            const params = this.selectedBeneficiario ? { beneficiario_id: this.selectedBeneficiario } : {};
            
            return this.axiosGet('/app/report-transactions/overview', { params })
                .then(res => {
                    this.overview = res.data;

                    let topSources = structuredClone(res.data.transaction_sources || []).slice(0, 5);
                    this.topSources.labels = topSources.map(i => i.name);
                    this.topSources.dataSet = this.genChartData(topSources);

                    this.distributionSources.labels = (res.data.transaction_sources || []).map(i => i.name);
                    this.distributionSources.dataSet = this.genChartData(res.data.transaction_sources || []);

                    this.performance.labels = (res.data.transaction_trends || []).map(i => `${moment(i.start).format('DD MMM')} - ${moment(i.end).format('DD MMM')}`);
                    this.performance.dataSet = this.genPerformanceChartData(res.data.transaction_trends || []);
                })
                .finally(() => {
                    this.preloader = false;
                });
        },
    },
    mounted() {
        this.preloader = true;
        this.loadBeneficiarios();
        this.getOverview();
    }
}
</script>
