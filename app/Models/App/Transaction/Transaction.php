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
        'currency',
        'is_paid',
        'paid_at',
        'payment_history_id',
        'terminated_at'
    ];

    protected $casts = [
        'creation_time' => 'datetime',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'terminated_at' => 'datetime',
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

    /**
     * Check if transaction is a free eSIM
     *
     * @return bool
     */
    public function isFreeEsim()
    {
        return $this->purchase_amount === 0 || $this->purchase_amount === 0.0 || $this->purchase_amount === '0';
    }

    /**
     * Get beneficiary commission amount
     * For free eSIMs: $0.85
     * For paid plans: calculated based on beneficiary's commission percentage or plan margin
     *
     * @return float
     */
    public function getCommissionAmount()
    {
        if ($this->isFreeEsim()) {
            return 0.85;
        }

        if (!$this->cliente || !$this->cliente->beneficiario) {
            return 0;
        }

        $beneficiario = $this->cliente->beneficiario;
        $purchaseAmount = (float) $this->purchase_amount;

        // Try to get margin from beneficiary plan margins
        if ($this->data_amount) {
            $planMargin = $beneficiario->planMargins()
                ->where('plan_capacity', $this->data_amount)
                ->where('is_active', true)
                ->first();
            
            if ($planMargin) {
                return $purchaseAmount * ($planMargin->margin_percentage / 100);
            }
        }

        // Fallback to beneficiary's general commission percentage
        if ($beneficiario->commission_percentage) {
            return $purchaseAmount * ($beneficiario->commission_percentage / 100);
        }

        return 0;
    }

    /**
     * Get commission percentage
     *
     * @return float
     */
    public function getCommissionPercentage()
    {
        if ($this->isFreeEsim()) {
            return 0;
        }

        if (!$this->cliente || !$this->cliente->beneficiario) {
            return 0;
        }

        $beneficiario = $this->cliente->beneficiario;

        // Try to get margin from beneficiary plan margins
        if ($this->data_amount) {
            $planMargin = $beneficiario->planMargins()
                ->where('plan_capacity', $this->data_amount)
                ->where('is_active', true)
                ->first();
            
            if ($planMargin) {
                return $planMargin->margin_percentage;
            }
        }

        // Fallback to beneficiary's general commission percentage
        return $beneficiario->commission_percentage ?? 0;
    }
}
