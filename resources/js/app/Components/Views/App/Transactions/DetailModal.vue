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
                            <th>ID</th>
                            <td>{{ esimData.id || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('iccid') }}</th>
                            <td>{{ esimData.iccid || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t('status') }}</th>
                            <td>
                                <span :class="statusBadgeClass(esimData.status)">
                                    {{ esimData.status || 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Consumo</th>
                            <td>
                                {{ subscriptionUsage }}
                            </td>
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
        computed: {
            subscriptionUsage() {
                const usedAmount = this.esimData?.subscription?.used_amount;
                const upperLimitAmount = this.esimData?.subscription?.upper_limit_amount
                    ?? this.esimData?.subscription?.uper_limit_amount;

                if (usedAmount == null && upperLimitAmount == null) {
                    return 'N/A';
                }

                const valuesLookLikeBytes = this.shouldFormatAsBytes(usedAmount, upperLimitAmount);
                const formattedUsedAmount = this.formatUsageAmount(usedAmount, valuesLookLikeBytes);
                const formattedUpperLimitAmount = this.formatUsageAmount(upperLimitAmount, valuesLookLikeBytes);

                if (formattedUsedAmount === 'N/A') {
                    return formattedUpperLimitAmount;
                }

                if (formattedUpperLimitAmount === 'N/A') {
                    return formattedUsedAmount;
                }

                return `${formattedUsedAmount} de ${formattedUpperLimitAmount}`;
            }
        },
        mounted() {
            this.loadEsimStatus();
        },
        methods: {
            shouldFormatAsBytes(usedAmount, upperLimitAmount) {
                return [usedAmount, upperLimitAmount]
                    .map(value => Number(value))
                    .filter(value => Number.isFinite(value))
                    .some(value => value >= 1024 * 1024);
            },

            formatUsageAmount(value, treatAsBytes = false) {
                if (value == null || value === '') {
                    return 'N/A';
                }

                if (typeof value === 'string' && /[a-zA-Z]/.test(value)) {
                    return value.replace(/\s+/g, ' ').trim();
                }

                const numericValue = Number(value);

                if (!Number.isFinite(numericValue)) {
                    return value;
                }

                if (!treatAsBytes) {
                    return `${this.formatUsageNumber(numericValue)} GB`;
                }

                const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                let amount = numericValue;
                let unitIndex = 0;

                while (amount >= 1024 && unitIndex < units.length - 1) {
                    amount /= 1024;
                    unitIndex += 1;
                }

                return `${this.formatUsageNumber(amount)} ${units[unitIndex]}`;
            },

            formatUsageNumber(value) {
                const roundedValue = value >= 10 ? value.toFixed(0) : value.toFixed(1);
                return roundedValue.replace(/\.0$/, '');
            },

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
