<?php

namespace App\Filters\App\Cliente;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\App\Traits\SearchFilter;
use App\Filters\FilterBuilder;

class ClienteFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilter;
}
