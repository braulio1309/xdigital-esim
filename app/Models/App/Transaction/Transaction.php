<?php

namespace App\Models\App\Transaction;

use App\Models\App\AppModel;
use App\Models\App\Cliente\Cliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'status',
        'iccid',
        'esim_qr',
        'creation_time',
        'cliente_id',
        'order_id',
        'plan_name',
        'data_amount',
        'duration_days',
        'purchase_amount',
        'currency'
    ];

    protected $casts = [
        'creation_time' => 'datetime',
    ];

    /**
     * Relationship with Cliente model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
