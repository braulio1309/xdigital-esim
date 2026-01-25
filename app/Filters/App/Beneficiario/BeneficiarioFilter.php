<?php

namespace App\Filters\App\Beneficiario;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\App\Traits\SearchFilter;
use App\Filters\FilterBuilder;

class BeneficiarioFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilter;
}
