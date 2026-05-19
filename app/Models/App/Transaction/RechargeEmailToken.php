<?php

namespace App\Models\App\Transaction;

use App\Models\App\AppModel;
use App\Models\App\Cliente\Cliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RechargeEmailToken extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'cliente_id',
        'token_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
