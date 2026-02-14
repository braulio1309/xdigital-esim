<?php

namespace App\Filters\App\Transaction;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\FilterBuilder;

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
     * Filter by beneficiario_id
     */
    public function beneficiario_id($beneficiarioId = null)
    {
        $this->builder->when($beneficiarioId, function ($query) use ($beneficiarioId) {
            $query->whereHas('cliente', function ($q) use ($beneficiarioId) {
                $q->where('beneficiario_id', $beneficiarioId);
            });
        });
    }

    /**
     * Filter by payment status
     * Values: 'paid' (true), 'unpaid' (false), or boolean
     */
    public function payment_status($paymentStatus = null)
    {
        $this->builder->when($paymentStatus !== null, function ($query) use ($paymentStatus) {
            if ($paymentStatus === 'paid' || $paymentStatus === true || $paymentStatus === '1') {
                $query->where('is_paid', true);
            } elseif ($paymentStatus === 'unpaid' || $paymentStatus === false || $paymentStatus === '0') {
                $query->where('is_paid', false);
            }
        });
    }
}
