<?php

namespace App\Models\App\Settings;

use App\Models\App\Beneficiario\Beneficiario;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryCountryPrice extends Model
{
    protected $table = 'beneficiary_country_prices';

    protected $fillable = [
        'beneficiario_id',
        'country_code',
        'plan_capacity',
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }
}
