<?php

namespace App\Models\App\Settings;

use App\Models\App\Beneficiario\Beneficiario;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryPlanPrice extends Model
{
    protected $table = 'beneficiary_plan_prices';

    protected $fillable = [
        'beneficiario_id',
        'plan_capacity',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }
}
