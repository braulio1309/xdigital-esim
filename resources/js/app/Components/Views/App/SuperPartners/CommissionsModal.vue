<template>
    <app-modal
        modal-id="super-partner-commissions-modal"
        modal-size="medium"
        @close-modal="handleModalClosed">

        <template slot="header">
            <h5 class="modal-title">Comisiones - {{ superPartnerName }}</h5>
            <button type="button" class="close outline-none" @click.prevent="closeModal">
                <span>×</span>
            </button>
        </template>

        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <form v-else ref="form" class="mb-0" :class="{'loading-opacity': preloader}">

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'margins'}" href="#" @click.prevent="activeTab = 'margins'">
                            Comisiones (%)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'prices'}" href="#" @click.prevent="activeTab = 'prices'">
                            Precios Fijos (USD)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: activeTab === 'country'}" href="#" @click.prevent="activeTab = 'country'">
                            Porcentaje por País (%)
                        </a>
                    </li>
                </ul>

                <!-- Tab: Margins -->
                <div v-show="activeTab === 'margins'">
                    <div class="form-group mb-4">
                        <div class="alert alert-info">
                            <strong>Configuración de Comisiones del Super Partner</strong>
                            <p class="mb-0 mt-2">
                                Ajusta la comisión general y la tarifa de eSIM gratuita para este super partner.
                            </p>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label for="sp_free_esim_rate" class="col-sm-5 mb-0">
                            Tarifa eSIM Gratuita 1GB (USD)
                        </label>
                        <div class="col-sm-7">
                            <div class="input-group" style="max-width: 220px;">
                                <app-input
                                    id="sp_free_esim_rate"
                                    type="number"
                                    v-model="freeEsimRate"
                                    :min="0"
                                    step="0.01"/>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Monto de referencia por cada eSIM gratuita 1GB (cuando no hay precio fijo configurado).
                            </small>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="d-block mb-2">Comisiones por plan (3GB, 5GB, 10GB)</label>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Comisión %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="plan in marginCapacities" :key="plan">
                                        <td class="align-middle"><strong>{{ plan }}GB</strong></td>
                                        <td class="align-middle">
                                            <div class="input-group" style="max-width: 180px;">
                                                <app-input
                                                    type="number"
                                                    v-model="margins[plan].margin_percentage"
                                                    :min="0"
                                                    :max="100"
                                                    step="0.01"/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab: Fixed Plan Prices -->
                <div v-show="activeTab === 'prices'">
                    <div class="alert alert-warning">
                        <strong>Precios Fijos por Plan</strong>
                        <p class="mb-0 mt-2">
                            Si asignas un precio fijo para un plan, ese monto se usará directamente al cobrar por una eSIM gratuita de esa capacidad, 
                            ignorando el cálculo de porcentaje. Deja en blanco para seguir usando el sistema de porcentajes.
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Precio Fijo (USD)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="plan in allCapacities" :key="plan">
                                    <td class="align-middle"><strong>{{ plan }}GB</strong></td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 180px;">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <app-input
                                                type="number"
                                                v-model="planPrices[plan].price"
                                                :placeholder="'Sin precio fijo'"
                                                :min="0"
                                                step="0.01"/>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="clearPlanPrice(plan)">
                                            <app-icon name="x" style="width:14px;height:14px;"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Country-Specific Prices -->
                <div v-show="activeTab === 'country'">
                    <div class="alert alert-info">
                        <strong>Configuracion por Pais</strong>
                        <p class="mb-0 mt-2">
                            Al agregar un pais se crean de una vez las capacidades 1GB, 3GB, 5GB y 10GB.
                            Para 1GB se guarda un precio fijo en USD. Para 3GB, 5GB y 10GB se mantiene el porcentaje sobre el precio original.
                        </p>
                    </div>

                    <div class="d-flex align-items-end mb-3">
                        <div class="mr-2" style="max-width: 180px; width: 100%;">
                            <label class="mb-1">Pais</label>
                            <app-input
                                type="text"
                                v-model="newCountryCode"
                                :placeholder="'US'"
                                :maxlength="2"/>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="addCountryPrice">
                            <app-icon name="plus" style="width:14px;height:14px;" class="mr-1"/> Agregar pais
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Pais</th>
                                    <th>1GB</th>
                                    <th>3GB</th>
                                    <th>5GB</th>
                                    <th>10GB</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="countryRow in groupedCountryPrices" :key="countryRow.country_code">
                                    <td class="align-middle">
                                        <strong>{{ countryRow.country_code }}</strong>
                                    </td>
                                    <td class="align-middle">
                                        <div v-if="countryRow.entries['1']" style="min-width: 170px; max-width: 180px;">
                                            <app-input type="number"
                                                       v-model="countryRow.entries['1'].price"
                                                       :min="0"
                                                       step="0.01"
                                                       :placeholder="'Precio fijo USD'"/>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="align-middle">
                                        <div v-if="countryRow.entries['3']" style="min-width: 145px; max-width: 150px;">
                                            <app-input type="number"
                                                       v-model="countryRow.entries['3'].percentage"
                                                       :min="0"
                                                       :max="100"
                                                       step="0.01"
                                                       :placeholder="'Porcentaje %'"/>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="align-middle">
                                        <div v-if="countryRow.entries['5']" style="min-width: 145px; max-width: 150px;">
                                            <app-input type="number"
                                                       v-model="countryRow.entries['5'].percentage"
                                                       :min="0"
                                                       :max="100"
                                                       step="0.01"
                                                       :placeholder="'Porcentaje %'"/>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="align-middle">
                                        <div v-if="countryRow.entries['10']" style="min-width: 145px; max-width: 150px;">
                                            <app-input type="number"
                                                       v-model="countryRow.entries['10'].percentage"
                                                       :min="0"
                                                       :max="100"
                                                       step="0.01"
                                                       :placeholder="'Porcentaje %'"/>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removeCountry(countryRow.country_code)">
                                            <app-icon name="trash-2" style="width:14px;height:14px;"/>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="countryPrices.length === 0">
                                    <td colspan="6" class="text-center text-muted py-3">
                                        No hay porcentajes por país configurados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button class="btn btn-light mr-2" @click.prevent="closeModal">
                        {{ $t('cancel') }}
                    </button>
                    <button class="btn btn-primary" @click.prevent="submit">
                        {{ $t('save') }}
                    </button>
                </div>
            </form>
        </template>
    </app-modal>
</template>

<script>
    import axios from 'axios';
    import {FormMixin} from '../../../../../core/mixins/form/FormMixin.js';
    import {ModalMixin} from "../../../../Mixins/ModalMixin";

    export default {
        name: 'SuperPartnerCommissionsModal',
        mixins: [FormMixin, ModalMixin],
        props: {
            superPartnerId: {
                type: Number,
                required: true,
            },
            superPartnerName: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                preloader: false,
                activeTab: 'margins',
                commissionPercentage: 0.00,
                freeEsimRate: 0.85,
                allCapacities: ['1', '3', '5', '10'],
                marginCapacities: ['3', '5', '10'],
                margins: {
                    '3': { margin_percentage: 0.00, is_active: true },
                    '5': { margin_percentage: 0.00, is_active: true },
                    '10': { margin_percentage: 0.00, is_active: true },
                },
                planPrices: {
                    '1': { price: '', is_active: true },
                    '3': { price: '', is_active: true },
                    '5': { price: '', is_active: true },
                    '10': { price: '', is_active: true },
                },
                countryPrices: [],
                newCountryCode: '',
            };
        },
        mounted() {
            this.fetchData();
        },
        computed: {
            groupedCountryPrices() {
                const groupedEntries = {};

                this.countryPrices.forEach((entry) => {
                    const countryCode = String(entry.country_code || '').trim().toUpperCase();
                    const planCapacity = String(entry.plan_capacity || '').trim();

                    if (!countryCode || !planCapacity) {
                        return;
                    }

                    if (!groupedEntries[countryCode]) {
                        groupedEntries[countryCode] = {
                            country_code: countryCode,
                            entries: {},
                        };
                    }

                    groupedEntries[countryCode].entries[planCapacity] = entry;
                });

                return Object.values(groupedEntries).sort((firstEntry, secondEntry) =>
                    firstEntry.country_code.localeCompare(secondEntry.country_code)
                );
            },
        },
        methods: {
            sortCountryPrices(entries = []) {
                const planOrder = { '1': 0, '3': 1, '5': 2, '10': 3 };

                return [...entries].sort((firstEntry, secondEntry) => {
                    const countryComparison = String(firstEntry.country_code || '').localeCompare(String(secondEntry.country_code || ''));

                    if (countryComparison !== 0) {
                        return countryComparison;
                    }

                    return (planOrder[String(firstEntry.plan_capacity)] ?? 99)
                        - (planOrder[String(secondEntry.plan_capacity)] ?? 99);
                });
            },
            hydrateCountryPrices(entries = []) {
                return this.sortCountryPrices(entries.map((entry) => ({
                    ...entry,
                    country_code: String(entry.country_code || '').trim().toUpperCase(),
                    plan_capacity: String(entry.plan_capacity || '').trim(),
                    price: String(entry.plan_capacity || '') === '1'
                        ? (entry.price === null || typeof entry.price === 'undefined' ? '' : entry.price)
                        : '',
                    percentage: String(entry.plan_capacity || '') === '1'
                        ? ''
                        : (entry.percentage === null || typeof entry.percentage === 'undefined' ? '' : entry.percentage),
                })));
            },
            normalizeCountryPriceEntry(entry) {
                const countryCode = String(entry.country_code || '').trim().toUpperCase();
                const planCapacity = String(entry.plan_capacity || '').trim();

                if (!countryCode || countryCode.length !== 2 || !planCapacity) {
                    return null;
                }

                if (planCapacity === '1') {
                    const price = entry.price === '' || entry.price === null ? null : Number(entry.price);

                    if (price === null || Number.isNaN(price)) {
                        return null;
                    }

                    return {
                        ...entry,
                        country_code: countryCode,
                        plan_capacity: planCapacity,
                        price,
                        percentage: 0,
                    };
                }

                const percentage = entry.percentage === '' || entry.percentage === null ? null : Number(entry.percentage);

                if (percentage === null || Number.isNaN(percentage)) {
                    return null;
                }

                return {
                    ...entry,
                    country_code: countryCode,
                    plan_capacity: planCapacity,
                    price: null,
                    percentage,
                };
            },
            getNormalizedCountryPrices() {
                const entriesByKey = {};

                this.countryPrices.forEach((entry) => {
                    const normalizedEntry = this.normalizeCountryPriceEntry(entry);

                    if (!normalizedEntry) {
                        return;
                    }

                    entriesByKey[`${normalizedEntry.country_code}|${normalizedEntry.plan_capacity}`] = normalizedEntry;
                });

                return this.sortCountryPrices(Object.values(entriesByKey));
            },
            fetchData() {
                this.preloader = true;
                axios.get(`/super-partners/${this.superPartnerId}/commissions`)
                    .then(response => {
                        if (response.data) {
                            if (typeof response.data.commission_percentage !== 'undefined' && response.data.commission_percentage !== null) {
                                this.commissionPercentage = parseFloat(response.data.commission_percentage) || 0.00;
                            }
                            if (typeof response.data.free_esim_rate !== 'undefined' && response.data.free_esim_rate !== null) {
                                this.freeEsimRate = parseFloat(response.data.free_esim_rate) || 0.85;
                            }
                            if (response.data.margins) {
                                Object.keys(response.data.margins).forEach(plan => {
                                    if (this.margins[plan]) {
                                        this.margins[plan] = { ...this.margins[plan], ...response.data.margins[plan] };
                                    }
                                });
                            }
                            if (response.data.plan_prices) {
                                Object.keys(response.data.plan_prices).forEach(plan => {
                                    if (this.planPrices[plan]) {
                                        this.planPrices[plan] = { ...this.planPrices[plan], ...response.data.plan_prices[plan] };
                                    }
                                });
                            }
                            if (response.data.country_prices) {
                                this.countryPrices = this.hydrateCountryPrices(response.data.country_prices);
                            }
                        }
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || this.$t('error_loading_margins');
                        this.$toastr.e(message);
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            submit() {
                this.preloader = true;
                const normalizedCountryPrices = this.getNormalizedCountryPrices();

                const payload = {
                    commission_percentage: this.commissionPercentage,
                    free_esim_rate: this.freeEsimRate,
                    margins: this.margins,
                    plan_prices: this.planPrices,
                    country_prices: normalizedCountryPrices,
                };

                axios.post(`/super-partners/${this.superPartnerId}/commissions`, payload)
                    .then(response => {
                        const message = response.data?.message || this.$t('updated_response');
                        this.$toastr.s(message);
                        if (response.data) {
                            if (typeof response.data.commission_percentage !== 'undefined' && response.data.commission_percentage !== null) {
                                this.commissionPercentage = parseFloat(response.data.commission_percentage) || this.commissionPercentage;
                            }
                            if (typeof response.data.free_esim_rate !== 'undefined' && response.data.free_esim_rate !== null) {
                                this.freeEsimRate = parseFloat(response.data.free_esim_rate) || this.freeEsimRate;
                            }
                            if (response.data.margins) {
                                Object.keys(response.data.margins).forEach(plan => {
                                    if (this.margins[plan]) {
                                        this.margins[plan] = { ...this.margins[plan], ...response.data.margins[plan] };
                                    }
                                });
                            }
                            if (response.data.plan_prices) {
                                Object.keys(response.data.plan_prices).forEach(plan => {
                                    if (this.planPrices[plan]) {
                                        this.planPrices[plan] = { ...this.planPrices[plan], ...response.data.plan_prices[plan] };
                                    }
                                });
                            }
                            if (response.data.country_prices) {
                                this.countryPrices = this.hydrateCountryPrices(response.data.country_prices);
                            } else {
                                this.countryPrices = this.hydrateCountryPrices(normalizedCountryPrices);
                            }
                        }
                        setTimeout(() => { this.closeModal(); }, 800);
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || this.$t('error_updating_margins');
                        this.$toastr.e(message);
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },
            clearPlanPrice(plan) {
                this.planPrices[plan].price = '';
            },
            addCountryPrice() {
                const countryCode = String(this.newCountryCode || '').trim().toUpperCase();

                if (!/^[A-Z]{2}$/.test(countryCode)) {
                    this.$toastr.e('Ingresa un codigo de pais valido de 2 letras.');
                    return;
                }

                const existingCapacities = new Set(
                    this.countryPrices
                        .filter((entry) => String(entry.country_code || '').trim().toUpperCase() === countryCode)
                        .map((entry) => String(entry.plan_capacity || '').trim())
                );

                const entriesToAdd = this.allCapacities
                    .filter((capacity) => !existingCapacities.has(capacity))
                    .map((capacity) => ({
                        country_code: countryCode,
                        plan_capacity: capacity,
                        price: capacity === '1' ? '' : '',
                        percentage: capacity === '1' ? '' : '',
                    }));

                if (!entriesToAdd.length) {
                    this.$toastr.i('Ese pais ya tiene configurados 1GB, 3GB, 5GB y 10GB.');
                    this.newCountryCode = '';
                    return;
                }

                this.countryPrices = this.sortCountryPrices([...this.countryPrices, ...entriesToAdd]);
                this.newCountryCode = '';
            },
            removeCountry(countryCode) {
                const normalizedCountryCode = String(countryCode || '').trim().toUpperCase();
                this.countryPrices = this.countryPrices.filter((entry) => String(entry.country_code || '').trim().toUpperCase() !== normalizedCountryCode);
            },
            closeModal() {
                const modal = $('#super-partner-commissions-modal');

                if (modal.length && modal.hasClass('show')) {
                    modal.modal('hide');
                    return;
                }

                this.handleModalClosed();
            },
            handleModalClosed() {
                this.$emit('close');
            },
        },
    };
</script>
