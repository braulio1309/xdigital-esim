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
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('Total Beneficiarios') }}({{ $t('all_time') }})</div>
                                <div class="h1">{{ total_beneficiarios }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('Beneficiarios Activos') }}</div>
                                <div class="h1">{{ active_beneficiarios }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body d-flex justifycontent-center align-items-center">
                            <div class="text-center w-100">
                                <div class="text-muted">{{ $t('Promedio Ventas por Beneficiario') }}</div>
                                <div class="h1">{{ avg_transactions_per_beneficiario }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>Ventas por Beneficiario</h4>
                            <app-chart class="mb-primary" type="horizontal-line-chart" :height="230"
                                :labels="transactionsByBeneficiario.labels" :data-sets="transactionsByBeneficiario.dataSet" />

                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-primary">
                <div class="col">
                    <div class="card card-with-shadow border-0">
                        <div class="card-body">
                            <h4>{{ $t('Ventas por Plan') }}</h4>
                            <app-chart class="mb-primary" type="bar-chart" :height="230" :labels="salesByPlan.labels"
                                :data-sets="salesByPlan.dataSet" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { colorArray } from '../../../../../../app/Helpers/ColorHelper';
export default {
    data() {
        return {
            preloader: false,
            selectedBeneficiario: null,
            beneficiarios: [],
            total_beneficiarios: 0,
            active_beneficiarios: 0,
            avg_transactions_per_beneficiario: 0,
            transactionsByBeneficiario: {
                labels: [],
                dataSet: []
            },
            salesByPlan: {
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
                    borderColor: colorArray.slice(0, data.length),
                    backgroundColor: colorArray.slice(0, data.length),
                    data: data.map(i => i.value)
                }
            ]
        },
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
            this.getBeneficiaryOverview();
        },
        getBeneficiaryOverview() {
            this.preloader = true;
            const params = this.selectedBeneficiario ? { beneficiario_id: this.selectedBeneficiario } : {};
            
            return this.axiosGet('/app/report-transactions/beneficiary-overview', { params })
                .then(res => {
                    this.total_beneficiarios = res.data.total_beneficiarios;
                    this.active_beneficiarios = res.data.active_beneficiarios;
                    this.avg_transactions_per_beneficiario = res.data.avg_transactions_per_beneficiario;
                    
                    this.transactionsByBeneficiario.labels = res.data.transactions_by_beneficiario.map(i => i.name);
                    this.transactionsByBeneficiario.dataSet = this.genChartData(res.data.transactions_by_beneficiario.map(i => ({ value: i.value })));
                    
                    this.salesByPlan.labels = res.data.sales_by_plan.map(i => i.plan);
                    this.salesByPlan.dataSet = this.genChartData(res.data.sales_by_plan.map(i => ({ value: i.transaction_count })));
                })
                .finally(() => {
                    this.preloader = false;
                });
        },
    },
    mounted() {
        this.preloader = true;
        Promise.all([this.loadBeneficiarios(), this.getBeneficiaryOverview()]).finally(() => {
            this.preloader = false;
        });
    }
}
</script>