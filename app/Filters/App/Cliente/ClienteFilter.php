<?php

namespace App\Filters\App\Cliente;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\FilterBuilder;
use Carbon\Carbon;

class ClienteFilter extends FilterBuilder
{
    use DateRangeFilter;

    public function search($search = null)
    {
        $this->builder->when($search, function ($query) use ($search) {
            $wildcard = "%{$search}%";

            $query->where(function ($builder) use ($wildcard) {
                $builder->where('nombre', 'like', $wildcard)
                    ->orWhere('apellido', 'like', $wildcard)
                    ->orWhere('email', 'like', $wildcard)
                    ->orWhere('identificador', 'like', $wildcard)
                    ->orWhereRaw("CONCAT(COALESCE(nombre, ''), ' ', COALESCE(apellido, '')) LIKE ?", [$wildcard]);
            });
        });
    }

    public function startDate($startDate = null)
    {
        $this->builder->when($startDate, function ($query) use ($startDate) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $startDate);
            $query->where('created_at', '>=', Carbon::parse($cleanDate)->startOfDay());
        });
    }

    public function endDate($endDate = null)
    {
        $this->builder->when($endDate, function ($query) use ($endDate) {
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $endDate);
            $query->where('created_at', '<=', Carbon::parse($cleanDate)->endOfDay());
        });
    }
}
