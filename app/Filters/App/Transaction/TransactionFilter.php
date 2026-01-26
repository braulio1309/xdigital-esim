<?php

namespace App\Filters\App\Transaction;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\App\Traits\SearchFilter;
use App\Filters\FilterBuilder;

class TransactionFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilter;
}
