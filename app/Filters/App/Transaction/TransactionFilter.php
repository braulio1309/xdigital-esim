<?php

namespace App\Filters\App\Transaction;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\FilterBuilder;
use App\Models\App\Beneficiario\Beneficiario;

class TransactionFilter extends FilterBuilder
{
    use DateRangeFilter;
    
    /**
     * Search across transaction_id, cliente name, and plan_name
     * Note: Input is already sanitized by FilterBuilder's apply method
     */
    public function search($search = null)
    {
        $this->builder->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('plan_name', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($clienteQuery) use ($search) {
                        $clienteQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        });
    }
    
    /**
     * Filter by status
     * Common values: completed, pending, failed
     */
    public function status($status = null)
    {
        $this->builder->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        });
    }

    /**
     * Filter by transaction type (free or paid)
     * Values: 'free' or 'paid'
     */
    public function type($type = null)
    {
        $this->builder->when($type, function ($query) use ($type) {
            if ($type === 'free') {
                $query->where('purchase_amount', 0);
            } elseif ($type === 'paid') {
                $query->where('purchase_amount', '>', 0);
            }
        });
    }

    /**
     * Filter by beneficiario_id directly on the transactions table.
     * Use 'none' to filter transactions with no beneficiario set on the transaction.
     *
     * NOTE: Method name is camelCase (beneficiarioId) because FilterBuilder
     * converts the request key "beneficiario_id" to camelCase when resolving
     * which filter method to call.
     */
    public function beneficiarioId($beneficiarioId = null)
    {
        $this->builder->when($beneficiarioId !== '' && $beneficiarioId !== null, function ($query) use ($beneficiarioId) {
            if ($beneficiarioId === 'none') {
                // Only transactions where the beneficiario_id column is NULL
                $query->whereNull('beneficiario_id');
            } else {
                // Strictly match by beneficiario_id column on transactions table
                $query->where('beneficiario_id', $beneficiarioId);
            }
        });
    }

    /**
     * Filter by super_partner_id by resolving all beneficiarios that belong to the given super partner
     * and constraining transactions to those beneficiario_ids.
     *
     * Request key: super_partner_id -> method: superPartnerId (camelCase)
     */
    public function superPartnerId($superPartnerId = null)
    {
        $this->builder->when($superPartnerId !== '' && $superPartnerId !== null, function ($query) use ($superPartnerId) {
            $beneficiarioIds = Beneficiario::where('super_partner_id', $superPartnerId)->pluck('id');

            if ($beneficiarioIds->isEmpty()) {
                // No beneficiarios for this super partner; force empty result set
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('beneficiario_id', $beneficiarioIds);
            }
        });
    }

    /**
     * Filter by payment status
     * Values: 'paid' (true), 'unpaid' (false), or boolean
     *
     * Request key: payment_status -> method: paymentStatus (camelCase)
     */
    public function paymentStatus($paymentStatus = null)
    {
        $this->builder->when($paymentStatus !== null && $paymentStatus !== '', function ($query) use ($paymentStatus) {
            // Convert to boolean for strict comparison
            if ($paymentStatus === 'paid' || $paymentStatus === true || $paymentStatus === 1 || $paymentStatus === '1') {
                $query->where('is_paid', true);
            } elseif ($paymentStatus === 'unpaid' || $paymentStatus === false || $paymentStatus === 0 || $paymentStatus === '0') {
                $query->where('is_paid', false);
            }
        });
    }

    /**
     * Filter by start date (creation_time >= start_date)
     * Request key: start_date -> method: startDate (camelCase)
     */
    public function startDate($startDate = null)
    {
        $this->builder->when($startDate, function ($query) use ($startDate) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $startDate);
            $query->where('creation_time', '>=', \Carbon\Carbon::parse($cleanDate)->startOfDay());
        });
    }

    /**
     * Filter by end date (creation_time <= end_date)
     * Request key: end_date -> method: endDate (camelCase)
     */
    public function endDate($endDate = null)
    {
        $this->builder->when($endDate, function ($query) use ($endDate) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $endDate);
            $query->where('creation_time', '<=', \Carbon\Carbon::parse($cleanDate)->endOfDay());
        });
    }
}
