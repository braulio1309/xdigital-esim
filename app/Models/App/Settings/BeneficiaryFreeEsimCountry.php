<?php

namespace App\Models\App\Settings;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryFreeEsimCountry extends Model
{
    protected $table = 'beneficiary_free_esim_countries';

    protected $fillable = [
        'beneficiario_id',
        'country_code',
        'is_active',
        'price',
        'plan_capacity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function beneficiario()
    {
        return $this->belongsTo(\App\Models\App\Beneficiario\Beneficiario::class);
    }
}
