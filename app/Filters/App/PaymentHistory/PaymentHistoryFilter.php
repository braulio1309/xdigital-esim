<?php

namespace App\Filters\App\PaymentHistory;

use App\Filters\FilterBuilder;

class PaymentHistoryFilter extends FilterBuilder
{
    /**
     * Search across reference and beneficiario name
     */
    public function search($search = null)
    {
        $this->builder->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('beneficiario', function ($bQuery) use ($search) {
                        $bQuery->where('nombre', 'like', "%{$search}%");
                    });
            });
        });
    }

    /**
     * Filter by beneficiario_id
     */
    public function beneficiario_id($beneficiarioId = null)
    {
        $this->builder->when($beneficiarioId, function ($query) use ($beneficiarioId) {
            $query->where('beneficiario_id', $beneficiarioId);
        });
    }
}
