<template>
  <div class="content-wrapper">
    <app-breadcrumb 
      page-title="Dashboard de Métricas" 
      :icon="'bar-chart-2'"
    />
    
    <div class="card card-with-shadow mb-4">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <app-input
              type="date"
              v-model="filters.start_date"
              :label="'Fecha Inicio'"
            />
          </div>
          <div class="col-md-4">
            <app-input
              type="date"
              v-model="filters.end_date"
              :label="'Fecha Fin'"
            />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button @click="loadMetrics" class="btn btn-primary" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm mr-2"></span>
              Aplicar Filtros
            </button>
            <button @click="resetFilters" class="btn btn-secondary ml-2" :disabled="loading">
              Resetear
            </button>
          </div>
        </div>
      </div>
    </div>

    <app-overlay-loader v-if="loading && mainWidgets.length === 0"/>
    <div class="row mb-4" v-if="mainWidgets.length > 0">
      <div class="col-md-3" v-for="(widget, index) in mainWidgets" :key="'widget-' + index">
        <app-widget
          class="mb-3"
          type="app-widget-with-icon"
          :label="widget.label"
          :number="widget.number"
          :icon="widget.icon"
        />
      </div>
    </div>

    <div class="row mb-4" v-if="topBeneficiarios.rows && topBeneficiarios.rows.length > 0">
      <div class="col-md-12">
        <div class="card card-with-shadow">
          <div class="card-header">
            <h5>Top 5 Beneficiarios con Más Clientes</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th v-for="col in topBeneficiarios.columns" :key="col.key">
                      {{ col.label }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, index) in topBeneficiarios.rows" :key="'beneficiario-' + index">
                    <td>{{ row.nombre }}</td>
                    <td>{{ row.codigo }}</td>
                    <td>{{ row.clientes_count }}</td>
                    <td>{{ row.comisiones }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4" v-if="clientsTrend.labels && clientsTrend.labels.length > 0">
      <div class="col-md-6">
        <div class="card card-with-shadow">
          <div class="card-header">
            <h5>Tendencia de Nuevos Clientes</h5>
          </div>
          <div class="card-body">
            <app-chart
              type="line-chart"
              :height="300"
              :labels="clientsTrend.labels"
              :data-sets="clientsTrend.datasets"
            />
          </div>
        </div>
      </div>

      <div class="col-md-6" v-if="transactionsByStatus.labels && transactionsByStatus.labels.length > 0">
        <div class="card card-with-shadow">
          <div class="card-header">
            <h5>Transacciones por Estado</h5>
          </div>
          <div class="card-body">
            <app-chart
              type="doughnut-chart"
              :height="300"
              :labels="transactionsByStatus.labels"
              :data-sets="transactionsByStatus.datasets"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4" v-if="beneficiariosActivity.labels && beneficiariosActivity.labels.length > 0">
      <div class="col-md-12">
        <div class="card card-with-shadow">
          <div class="card-header">
            <h5>Beneficiarios Activos vs Inactivos</h5>
          </div>
          <div class="card-body">
            <app-chart
              type="bar-chart"
              :height="300"
              :labels="beneficiariosActivity.labels"
              :data-sets="beneficiariosActivity.datasets"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminMetrics',
  data() {
    return {
      filters: {
        start_date: this.getDefaultStartDate(),
        end_date: this.getDefaultEndDate()
      },
      mainWidgets: [],
      topBeneficiarios: {
        columns: [],
        rows: []
      },
      clientsTrend: { labels: [], datasets: [] },
      transactionsByStatus: { labels: [], datasets: [] },
      beneficiariosActivity: { labels: [], datasets: [] },
      loading: false
    }
  },
  mounted() {
    this.loadMetrics();
  },
  methods: {
    loadMetrics() {
      this.loading = true;
      
      axios.get('/admin/metrics/data', {
        params: this.filters
      })
      .then(response => {
        // Axios guarda la respuesta del servidor dentro del objeto 'data'
        const responseData = response.data;
        
        this.mainWidgets = responseData.widgets || [];
        this.topBeneficiarios = responseData.topBeneficiarios || { columns: [], rows: [] };
        this.clientsTrend = responseData.clientsTrend || { labels: [], datasets: [] };
        this.transactionsByStatus = responseData.transactionsByStatus || { labels: [], datasets: [] };
        this.beneficiariosActivity = responseData.beneficiariosActivity || { labels: [], datasets: [] };
      })
      .catch(error => {
        console.error('Error loading metrics:', error);
        // Si tienes configurado toastr de forma global en Vue, puedes mantenerlo así,
        // de lo contrario, cámbialo por un alert() u otro manejador de errores.
        if (this.$toastr) {
          this.$toastr.e('Error al cargar métricas. Por favor intenta de nuevo.');
        } else {
          alert('Error al cargar métricas. Por favor intenta de nuevo.');
        }
      })
      .finally(() => {
        this.loading = false;
      });
    },
    resetFilters() {
      this.filters.start_date = this.getDefaultStartDate();
      this.filters.end_date = this.getDefaultEndDate();
      this.loadMetrics();
    },
    getDefaultStartDate() {
      const date = new Date();
      date.setDate(date.getDate() - 30);
      return date.toISOString().split('T')[0];
    },
    getDefaultEndDate() {
      return new Date().toISOString().split('T')[0];
    }
  }
}
</script>

<style scoped>
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.2em;
}
</style>