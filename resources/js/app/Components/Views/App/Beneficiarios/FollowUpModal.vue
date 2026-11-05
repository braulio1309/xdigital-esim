<template>
    <app-modal modal-id="beneficiario-follow-up-modal"
               modal-size="large"
               @close-modal="$emit('close')">
        <template slot="header">
            <h5 class="modal-title">Seguimiento de partners</h5>
            <button type="button" class="close outline-none" @click.prevent="$emit('close')">
                <span>×</span>
            </button>
        </template>

        <template slot="body">
            <form class="mb-3" @submit.prevent="loadPartners">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4 mb-2">
                        <label for="partner-follow-up-start">Desde</label>
                        <input id="partner-follow-up-start"
                               v-model="startDate"
                               type="date"
                               class="form-control">
                    </div>
                    <div class="form-group col-md-4 mb-2">
                        <label for="partner-follow-up-end">Hasta</label>
                        <input id="partner-follow-up-end"
                               v-model="endDate"
                               type="date"
                               class="form-control">
                    </div>
                    <div class="form-group col-md-4 mb-2 d-flex">
                        <button type="submit" class="btn btn-primary mr-2" :disabled="loading">
                            Filtrar
                        </button>
                        <button type="button"
                                class="btn btn-outline-secondary mr-2"
                                :disabled="loading || (!startDate && !endDate)"
                                @click="clearFilters">
                            Limpiar
                        </button>
                        <button type="button"
                                class="btn btn-success"
                                :disabled="loading"
                                @click="exportPartners">
                            Exportar
                        </button>
                    </div>
                </div>
            </form>

            <app-overlay-loader v-if="loading"/>
            <div v-else class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Partner</th>
                            <th class="text-right">Clientes registrados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="partner in partners" :key="partner.id">
                            <td>{{ partner.nombre }}</td>
                            <td class="text-right">
                                <span class="badge badge-primary">{{ partner.registered_clients_count }}</span>
                            </td>
                        </tr>
                        <tr v-if="partners.length === 0">
                            <td colspan="2" class="text-center text-muted">No hay partners para mostrar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </app-modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from '../../../../Config/ApiUrl';
    import {urlGenerator} from '../../../../Helpers/AxiosHelper';

    export default {
        name: 'BeneficiaryFollowUpModal',
        data() {
            return {
                loading: false,
                startDate: '',
                endDate: '',
                partners: [],
            };
        },
        mounted() {
            this.loadPartners();
        },
        methods: {
            filters() {
                return {
                    start_date: this.startDate || undefined,
                    end_date: this.endDate || undefined,
                };
            },
            loadPartners() {
                this.loading = true;
                axios.get(urlGenerator(actions.BENEFICIARIOS_FOLLOW_UP), {params: this.filters()})
                    .then(response => {
                        this.partners = response.data.partners || [];
                    })
                    .catch(error => {
                        this.$toastr.e(error.response?.data?.message || 'No fue posible cargar el seguimiento.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            clearFilters() {
                this.startDate = '';
                this.endDate = '';
                this.loadPartners();
            },
            exportPartners() {
                const query = new URLSearchParams();
                if (this.startDate) query.set('start_date', this.startDate);
                if (this.endDate) query.set('end_date', this.endDate);
                const suffix = query.toString();
                const url = urlGenerator(actions.BENEFICIARIOS_FOLLOW_UP_EXPORT);
                window.location.href = suffix ? `${url}?${suffix}` : url;
            },
        },
    };
</script>
