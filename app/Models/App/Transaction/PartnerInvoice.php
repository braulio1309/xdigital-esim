<?php

namespace App\Models\App\Transaction;

use App\Models\App\AppModel;
use App\Models\App\Transaction\PartnerCommissionNote;

class PartnerInvoice extends AppModel
{
    protected $table = 'partner_invoices';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'invoice_number',
        'period_start',
        'period_end',
        'amount',
        'paid_amount',
        'earned_commissions',
        'applied_commissions',
        'due_amount',
        'currency',
        'items',
        'transactions_count',
        'issued_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'earned_commissions' => 'decimal:2',
        'applied_commissions' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'items' => 'array',
        'issued_at' => 'datetime',
    ];

    public function commissionNote()
    {
        return $this->hasOne(PartnerCommissionNote::class, 'partner_invoice_id');
    }
}