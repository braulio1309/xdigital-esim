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
}
