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
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
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
}
