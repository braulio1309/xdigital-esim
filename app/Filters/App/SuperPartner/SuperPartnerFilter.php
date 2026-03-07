<?php

namespace App\Filters\App\SuperPartner;

use App\Filters\App\Traits\DateRangeFilter;
use App\Filters\App\Traits\SearchFilter;
use App\Filters\FilterBuilder;

class SuperPartnerFilter extends FilterBuilder
{
    use DateRangeFilter, SearchFilter;
}
