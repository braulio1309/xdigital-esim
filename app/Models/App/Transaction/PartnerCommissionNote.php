<?php

namespace App\Models\App\Transaction;

use App\Models\App\AppModel;

class PartnerCommissionNote extends AppModel
{
    protected $table = 'partner_commission_notes';

    protected $fillable = [
        'partner_invoice_id',
        'note_number',
        'amount',
        'applied_amount',
        'balance_amount',
        'items',
        'issued_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'items' => 'array',
        'issued_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(PartnerInvoice::class, 'partner_invoice_id');
    }
}