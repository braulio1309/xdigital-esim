<?php

namespace App\Models\App\Cliente;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use Illuminate\Database\Eloquent\Model;

class RechargeAccessToken extends Model
{
    protected $fillable = [
        'cliente_id',
        'transaction_id',
        'beneficiario_id',
        'super_partner_id',
        'token_hash',
        'purpose',
        'country_code',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function superPartner()
    {
        return $this->belongsTo(SuperPartner::class);
    }
}
