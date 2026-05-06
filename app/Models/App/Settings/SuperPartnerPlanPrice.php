<?php

namespace App\Models\App\Settings;

use App\Models\App\SuperPartner\SuperPartner;
use Illuminate\Database\Eloquent\Model;

class SuperPartnerPlanPrice extends Model
{
    protected $table = 'super_partner_plan_prices';

    protected $fillable = [
        'super_partner_id',
        'plan_capacity',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function superPartner()
    {
        return $this->belongsTo(SuperPartner::class);
    }
}
