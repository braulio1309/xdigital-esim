<template>
    <modal :modal-id="modalId"
           :title="$t('esim_details')"
           :preloader="preloader"
           @submit="closeModal"
           @close-modal="closeModal">
        <template slot="body">
            <app-overlay-loader v-if="preloader"/>
            <div v-if="!preloader && esimData" class="esim-details">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <th>{{ $t('iccid') }}</th>
                            <td>{{ esimData.iccid || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>MSISDN</th>
                            <td>{{ esimData.msisdn || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>IMSI</th>
                            <td>{{ esimData.imsi || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('status') }}</th>
                            <td>
                                <span :class="statusBadgeClass(esimData.status)">
                                    {{ esimData.status || 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="esimData.esim_qr">
                            <th>{{ $t('esim_qr') }}</th>
                            <td>
                                <small class="text-muted" style="word-break:break-all;">{{ esimData.esim_qr }}</small>
                            </td>
                        </tr>
                        <tr v-if="esimData.customer_service_number">
                            <th>{{ $t('customer_service_number') }}</th>
                            <td>{{ esimData.customer_service_number }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="!preloader && errorMessage" class="alert alert-danger">
                {{ errorMessage }}
            </div>
        </template>
    </modal>
</template>

<script>
    import axios from 'axios';
    import * as actions from "../../../../Config/ApiUrl";

    export default {
        name: "TransactionDetailModal",
        props: {
            transactionId: {
                type: Number,
                required: true
            }
        },
        data() {
            return {
                preloader: false,
                esimData: null,
                errorMessage: null,
                modalId: 'transaction-detail-modal',
            }
        },
        mounted() {
            this.loadEsimStatus();
        },
        methods: {
            loadEsimStatus() {
                this.preloader = true;
                this.errorMessage = null;
                axios.get(`/transactions/${this.transactionId}/esim-status`)
                    .then(response => {
                        this.esimData = response.data.data;
                    })
                    .catch(error => {
                        this.errorMessage = error.response?.data?.message || this.$t('error_loading_esim_status');
                    })
                    .finally(() => {
                        this.preloader = false;
                    });
            },

            statusBadgeClass(status) {
                const map = {
                    'ACTIVE': 'badge badge-success',
                    'PROVISIONED': 'badge badge-info',
                    'ALLOCATED': 'badge badge-secondary',
                    'INSTALLED': 'badge badge-primary',
                    'BLOCKED': 'badge badge-danger',
                    'DEACTIVATED': 'badge badge-dark',
                    'EXPIRED': 'badge badge-warning',
                };
                return map[status] || 'badge badge-secondary';
            },

            closeModal() {
                this.$emit('close-modal');
            }
        }
    }
</script>
