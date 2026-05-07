<?php

namespace App\Models\App\Settings;

use App\Models\App\SuperPartner\SuperPartner;
use Illuminate\Database\Eloquent\Model;

class SuperPartnerCountryPrice extends Model
{
    protected $table = 'super_partner_country_prices';

    protected $fillable = [
        'super_partner_id',
        'country_code',
        'plan_capacity',
        'percentage',
        'price',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function superPartner()
    {
        return $this->belongsTo(SuperPartner::class);
    }
}
