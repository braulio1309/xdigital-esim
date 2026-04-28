@extends('layouts.app')

@section('title', 'Panel de Cliente')

@section('contents')
    @php
        $rechargeTransaction = $transactions->first(function ($transaction) {
            return !empty($transaction->order_id);
        });
    @endphp

    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header dashboard-header-actions">
                    <div>
                        <h3 class="page-title">Panel de Cliente</h3>
                        <p class="text-muted mb-0">Bienvenido, {{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                    </div>

                    @if($rechargeTransaction)
                        <button
                            type="button"
                            class="btn btn-primary btn-lg js-recharge-data-btn dashboard-recharge-button"
                            data-transaction-id="{{ $rechargeTransaction->id }}"
                            data-planes-url="{{ route('planes.index') }}"
                        >
                            Recargar datos
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Historial de Transacciones</h4>
                        <p class="card-description">Todas tus transacciones registradas</p>

                        @if($transactions->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID Transacción</th>
                                            <th>Estado</th>
                                            <th>ICCID</th>
                                            <th>Capacidad Usada</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_id }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->iccid ?? 'N/A' }}</td>
                                                <td>
                                                    @if($transaction->order_id)
                                                        <span class="text-muted js-usage-value" data-transaction-id="{{ $transaction->id }}">Cargando...</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->creation_time ? $transaction->creation_time->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td class="actions-cell">
                                                    @if($transaction->order_id)
                                                        <button type="button" class="btn btn-sm btn-outline-info js-transaction-detail-btn" data-transaction-id="{{ $transaction->id }}">
                                                            Ver detalles
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mt-3" role="alert">
                                <p class="mb-0">No tienes transacciones registradas aún.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="transactionDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de la Transacción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="transaction-detail-loading" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div id="transaction-detail-error" class="alert alert-danger d-none mb-0"></div>
                        <table id="transaction-detail-table" class="table table-bordered table-sm mb-0 d-none">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td id="transaction-detail-id">N/A</td>
                                </tr>
                                <tr>
                                    <th>ICCID</th>
                                    <td id="transaction-detail-iccid">N/A</td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td id="transaction-detail-status">N/A</td>
                                </tr>
                                <tr>
                                    <th>Consumo</th>
                                    <td id="transaction-detail-usage">N/A</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
<style>
    .dashboard-header-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dashboard-recharge-button {
        min-width: 220px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.2);
    }

    .actions-cell {
        white-space: nowrap;
    }

    .actions-cell .btn {
        margin-right: 6px;
        margin-bottom: 6px;
    }

    .qr-code-container {
        padding: 20px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        display: inline-block;
    }

    #transaction-detail-table th {
        width: 150px;
    }

    @media (max-width: 767.98px) {
        .dashboard-recharge-button {
            width: 100%;
        }
    }
</style>
@endpush

@push('after-scripts')
<script>
function formatUsageNumber(value) {
    var roundedValue = value >= 10 ? value.toFixed(0) : value.toFixed(1);
    return roundedValue.replace(/\.0$/, '');
}

function shouldFormatAsBytes(usedAmount, upperLimitAmount) {
    return [usedAmount, upperLimitAmount]
        .map(function(value) { return Number(value); })
        .filter(function(value) { return Number.isFinite(value); })
        .some(function(value) { return value >= 1024 * 1024; });
}

function formatUsageAmount(value, treatAsBytes) {
    if (value == null || value === '') {
        return 'N/A';
    }

    if (typeof value === 'string' && /[a-zA-Z]/.test(value)) {
        return value.replace(/\s+/g, ' ').trim();
    }

    var numericValue = Number(value);

    if (!Number.isFinite(numericValue)) {
        return value;
    }

    if (!treatAsBytes) {
        return formatUsageNumber(numericValue) + ' GB';
    }

    var units = ['B', 'KB', 'MB', 'GB', 'TB'];
    var amount = numericValue;
    var unitIndex = 0;

    while (amount >= 1024 && unitIndex < units.length - 1) {
        amount /= 1024;
        unitIndex += 1;
    }

    return formatUsageNumber(amount) + ' ' + units[unitIndex];
}

function formatSubscriptionUsage(data) {
    var usedAmount = data && data.subscription ? data.subscription.used_amount : null;
    var upperLimitAmount = data && data.subscription
        ? (data.subscription.upper_limit_amount != null ? data.subscription.upper_limit_amount : data.subscription.uper_limit_amount)
        : null;

    if (usedAmount == null && upperLimitAmount == null) {
        return 'N/A';
    }

    var valuesLookLikeBytes = shouldFormatAsBytes(usedAmount, upperLimitAmount);
    var formattedUsedAmount = formatUsageAmount(usedAmount, valuesLookLikeBytes);
    var formattedUpperLimitAmount = formatUsageAmount(upperLimitAmount, valuesLookLikeBytes);

    if (formattedUsedAmount === 'N/A') {
        return formattedUpperLimitAmount;
    }

    if (formattedUpperLimitAmount === 'N/A') {
        return formattedUsedAmount;
    }

    return formattedUsedAmount + ' de ' + formattedUpperLimitAmount;
}

function extractCountryCode(value) {
    if (typeof value !== 'string') {
        return '';
    }

    var normalizedValue = value.trim().toUpperCase();
    return /^[A-Z]{2}$/.test(normalizedValue) ? normalizedValue : '';
}

function findCountryCode(source) {
    if (!source || typeof source !== 'object') {
        return '';
    }

    var candidateKeys = ['country_code', 'countryCode', 'country', 'destination_country', 'destinationCountry', 'location_code', 'locationCode'];

    for (var index = 0; index < candidateKeys.length; index += 1) {
        var key = candidateKeys[index];
        var countryCode = extractCountryCode(source[key]);

        if (countryCode) {
            return countryCode;
        }
    }

    var nestedKeys = Object.keys(source);

    for (var nestedIndex = 0; nestedIndex < nestedKeys.length; nestedIndex += 1) {
        var nestedValue = source[nestedKeys[nestedIndex]];

        if (Array.isArray(nestedValue)) {
            for (var arrayIndex = 0; arrayIndex < nestedValue.length; arrayIndex += 1) {
                var arrayCountryCode = findCountryCode(nestedValue[arrayIndex]);

                if (arrayCountryCode) {
                    return arrayCountryCode;
                }
            }
        }

        if (nestedValue && typeof nestedValue === 'object') {
            var nestedCountryCode = findCountryCode(nestedValue);

            if (nestedCountryCode) {
                return nestedCountryCode;
            }
        }
    }

    return '';
}

document.addEventListener('DOMContentLoaded', function() {
    var detailButtons = Array.prototype.slice.call(document.querySelectorAll('.js-transaction-detail-btn'));
    var rechargeButtons = Array.prototype.slice.call(document.querySelectorAll('.js-recharge-data-btn'));
    var usageNodes = Array.prototype.slice.call(document.querySelectorAll('.js-usage-value'));
    var loadingNode = document.getElementById('transaction-detail-loading');
    var errorNode = document.getElementById('transaction-detail-error');
    var tableNode = document.getElementById('transaction-detail-table');
    var detailIdNode = document.getElementById('transaction-detail-id');
    var detailIccidNode = document.getElementById('transaction-detail-iccid');
    var detailStatusNode = document.getElementById('transaction-detail-status');
    var detailUsageNode = document.getElementById('transaction-detail-usage');

    function setDetailState(isLoading, message) {
        if (loadingNode) {
            loadingNode.classList.toggle('d-none', !isLoading);
        }

        if (errorNode) {
            errorNode.classList.toggle('d-none', !message);
            errorNode.textContent = message || '';
        }

        if (tableNode) {
            tableNode.classList.toggle('d-none', isLoading || !!message);
        }
    }

    function fillDetailModal(data) {
        detailIdNode.textContent = data.id || 'N/A';
        detailIccidNode.textContent = data.iccid || 'N/A';
        detailStatusNode.textContent = data.status || 'N/A';
        detailUsageNode.textContent = formatSubscriptionUsage(data);
    }

    function fetchTransactionDetail(transactionId) {
        return axios.get('/cliente/dashboard/transactions/' + transactionId + '/detail');
    }

    function redirectToPlans(button, data) {
        var baseUrl = button.getAttribute('data-planes-url') || '/planes-disponibles';
        var url = new URL(baseUrl, window.location.origin);
        var countryCode = findCountryCode(data);
        var iccid = typeof data.iccid === 'string' ? data.iccid.trim() : '';

        if (countryCode) {
            url.searchParams.set('country', countryCode);
        }

        if (iccid) {
            url.searchParams.set('recharge_iccid', iccid);
        }

        window.location.href = url.toString();
    }

    usageNodes.forEach(function(node) {
        var transactionId = node.getAttribute('data-transaction-id');

        if (!transactionId) {
            node.textContent = 'N/A';
            return;
        }

        fetchTransactionDetail(transactionId)
            .then(function(response) {
                node.textContent = formatSubscriptionUsage(response.data.data);
            })
            .catch(function() {
                node.textContent = 'N/A';
            });
    });

    detailButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var transactionId = button.getAttribute('data-transaction-id');

            if (!transactionId) {
                return;
            }

            setDetailState(true, '');
            $('#transactionDetailModal').modal('show');

            fetchTransactionDetail(transactionId)
                .then(function(response) {
                    fillDetailModal(response.data.data || {});
                    setDetailState(false, '');
                })
                .catch(function(error) {
                    var message = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'No fue posible cargar el detalle de la transacción.';
                    setDetailState(false, message);
                });
        });
    });

    rechargeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var transactionId = button.getAttribute('data-transaction-id');

            if (!transactionId) {
                return;
            }

            button.disabled = true;

            fetchTransactionDetail(transactionId)
                .then(function(response) {
                    redirectToPlans(button, response.data.data || {});
                })
                .catch(function() {
                    redirectToPlans(button, {});
                })
                .finally(function() {
                    button.disabled = false;
                });
        });
    });
});
</script>
@endpush
