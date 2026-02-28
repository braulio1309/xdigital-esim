<?php

namespace App\Models\App\PaymentHistory;

use App\Models\App\AppModel;
use App\Models\App\Beneficiario\Beneficiario;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentHistory extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'beneficiario_id',
        'reference',
        'payment_date',
        'support_path',
        'support_original_name',
        'amount',
        'transactions_count',
        'notes',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationship with Beneficiario model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    /**
     * Transactions that were paid as part of this payment history record
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\App\Transaction\Transaction::class, 'payment_history_id');
    }
}
