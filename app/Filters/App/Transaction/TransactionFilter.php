<?php

namespace App\Filters\App\Transaction;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\App\Traits\SearchFilter;
use App\Filters\FilterBuilder;

class TransactionFilter extends FilterBuilder
{
    use DateRangeFilter;
    
    /**
     * Search across transaction_id, cliente name, and plan_name
     */
    public function search($search = null)
    {
        if ($search) {
            $this->builder->where(function ($query) use ($search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('plan_name', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
    }
    
    /**
     * Filter by status
     */
    public function status($status = null)
    {
        if ($status) {
            $this->builder->where('status', $status);
        }
    }
}
