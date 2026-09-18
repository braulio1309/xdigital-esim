<template>
    <modal :modal-id="modalId"
           title="Generar eSIM de cortesía"
           :preloader="preloader"
           @submit="submit"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form class="mb-0" :class="{'loading-opacity': preloader}">
                <div class="form-group row align-items-center">
                    <label for="courtesy-esim-name" class="col-sm-4 mb-0">Nombre del cliente</label>
                    <app-input id="courtesy-esim-name"
                               class="col-sm-8"
                               type="text"
                               v-model="inputs.nombre"
                               placeholder="Nombre completo"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="courtesy-esim-email" class="col-sm-4 mb-0">Correo</label>
                    <app-input id="courtesy-esim-email"
                               class="col-sm-8"
                               type="email"
                               v-model="inputs.email"
                               placeholder="correo@ejemplo.com"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center">
                    <label for="courtesy-esim-country" class="col-sm-4 mb-0">País</label>
                    <app-input id="courtesy-esim-country"
                               class="col-sm-8"
                               type="search-select"
                               v-model="inputs.country_code"
                               :list="countryOptions"
                               list-value-field="value"
                               list-class="country-select-dropdown"
                               placeholder="Buscar país"
                               :required="true"/>
                </div>
                <div class="form-group row align-items-center mb-0">
                    <label class="col-sm-4 mb-0">Capacidad</label>
                    <div class="col-sm-8">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label v-for="capacity in capacities"
                                   :key="capacity"
                                   class="btn btn-outline-primary"
                                   :class="{active: inputs.data_amount === capacity}">
                                <input type="radio" :value="capacity" v-model="inputs.data_amount" autocomplete="off"/>
                                {{ capacity }} GB
                            </label>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    La eSIM se registra como cortesía. El monto de compra será $0 y se conservará el precio de la API.
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from '../../../../Config/ApiUrl';

    export default {
        name: 'NomadCourtesyEsimModal',
        props: {
            tableId: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                modalId: 'nomad-courtesy-esim-modal',
                preloader: false,
                capacities: [1, 3, 5, 10],
                countryOptions: [],
                inputs: {
                    nombre: '',
                    email: '',
                    country_code: '',
                    data_amount: 1,
                },
            };
        },
        mounted() {
            axios.get(`/${actions.TRANSACTIONS_NOMAD_COURTESY_COUNTRIES}`)
                .then(response => {
                    this.countryOptions = response.data
                        .map(country => ({
                            id: country.code,
                            value: `${country.name} (${country.code})`,
                        }))
                        .sort((left, right) => left.value.localeCompare(right.value));
                })
                .catch(() => {
                    this.$toastr.e('No fue posible cargar la lista de países.');
                });
        },
        methods: {
            submit() {
                this.preloader = true;
                axios.post(`/${actions.TRANSACTIONS_NOMAD_COURTESY_ESIM}`, {
                    ...this.inputs,
                    country_code: this.inputs.country_code.trim().toUpperCase(),
                })
                    .then(response => {
                        this.$toastr.s(response.data.message);
                        this.$hub.$emit('reload-' + this.tableId);
                        this.$emit('created');
                        this.closeModal();
                    })
                    .catch(error => {
                        this.$toastr.e(error.response?.data?.message || 'No fue posible generar la eSIM de cortesía.');
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            closeModal() {
                this.$emit('close-modal');
            },
        },
    };
</script>