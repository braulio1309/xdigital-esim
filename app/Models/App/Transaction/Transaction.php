<?php

namespace App\Models\App\Transaction;

use App\Models\App\AppModel;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\SuperPartner\SuperPartner;
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
        'beneficiario_id',
        'super_partner_id',
        'order_id',
        'plan_name',
        'data_amount',
        'duration_days',
        'purchase_amount',
        'api_price',
        'reference_purchase_amount',
        'currency',
        'country_code',
        'beneficiary_commission_amount',
        'partner_sale_commission_amount',
        'super_partner_sale_commission_amount',
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
        'beneficiary_commission_amount' => 'decimal:2',
        'reference_purchase_amount' => 'decimal:2',
        'api_price' => 'decimal:2',
        'partner_sale_commission_amount' => 'decimal:2',
        'super_partner_sale_commission_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $transaction) {
            if ($transaction->beneficiary_commission_amount === null) {
                $transaction->beneficiary_commission_amount = $transaction->calculateCommissionAmountFromConfig();
            }
        });
    }

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
     * Relationship with Beneficiario model (direct partner for this transaction)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function superPartner()
    {
        return $this->belongsTo(SuperPartner::class);
    }

    /**
     * Resolve the beneficiario for this transaction.
     * Uses direct beneficiario_id first, falls back to cliente's beneficiario.
     *
     * @return Beneficiario|null
     */
    public function resolveBeneficiario()
    {
        if ($this->beneficiario_id && $this->beneficiario) {
            return $this->beneficiario;
        }
        return $this->cliente->beneficiario ?? null;
    }

    public function resolveSuperPartner()
    {
        if ($this->super_partner_id && $this->superPartner) {
            return $this->superPartner;
        }

        $beneficiario = $this->resolveBeneficiario();

        return $beneficiario ? $beneficiario->superPartner : null;
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
        if ($this->beneficiary_commission_amount !== null) {
            return (float) $this->beneficiary_commission_amount;
        }

        return $this->calculateCommissionAmountFromConfig();
    }

    /**
     * Calculate commission amount based on current configuration
     * (used for initial snapshot and as fallback when no snapshot exists).
     *
     * @return float
     */
    protected function calculateCommissionAmountFromConfig(): float
    {
        if ($this->beneficiary_commission_amount !== null) {
            return (float) $this->beneficiary_commission_amount;
        }

        $beneficiario = $this->resolveBeneficiario();
        $capacity = (int) ($this->data_amount ?? 0);

        if ($this->isFreeEsim()) {
            if ($beneficiario) {
                if ($capacity <= 1) {
                    return $this->resolveLegacyFreeEsimPrice($beneficiario, (float) $beneficiario->free_esim_rate);
                }

                return (float) $beneficiario->free_esim_rate;
            }

            $superPartner = $this->resolveSuperPartner();

            if ($superPartner) {
                if ($capacity <= 1) {
                    return $this->resolveLegacyFreeEsimPrice($superPartner, (float) $superPartner->free_esim_rate);
                }

                return (float) $superPartner->free_esim_rate;
            }

            return \App\Models\App\Beneficiario\Beneficiario::DEFAULT_FREE_ESIM_RATE;
        }

        if (!$beneficiario) {
            return 0;
        }

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
        if ($this->beneficiary_commission_amount !== null && (float) $this->reference_purchase_amount > 0) {
            return round(((float) $this->beneficiary_commission_amount / (float) $this->reference_purchase_amount) * 100, 2);
        }

        if ($this->isFreeEsim()) {
            return 0.0;
        }

        $beneficiario = $this->resolveBeneficiario();

        if (!$beneficiario) {
            return 0;
        }

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

    /**
     * Calculate sale commission amounts for a transaction based on country and partner.
     *
     * @param  float       $purchaseAmount   The final amount paid by the customer
     * @param  string|null $countryCode      ISO 3166-1 alpha-2 country code
     * @param  int|null    $beneficiarioId
     * @param  int|null    $superPartnerId
     * @return array{partner_sale_commission_amount: float, super_partner_sale_commission_amount: float}
     */
    public static function calculateSaleCommissions(
        float $purchaseAmount,
        ?string $countryCode,
        ?int $beneficiarioId,
        ?int $superPartnerId
    ): array {
        $partnerAmount = 0.0;
        $superPartnerAmount = 0.0;

        if ($purchaseAmount <= 0 || !$countryCode) {
            return [
                'partner_sale_commission_amount' => $partnerAmount,
                'super_partner_sale_commission_amount' => $superPartnerAmount,
            ];
        }

        $isLatam = \App\Helpers\CountryTariffHelper::isLatamCountry($countryCode);
        $isUsaCaEu = \App\Helpers\CountryTariffHelper::isUsaCaEuCountry($countryCode);

        if ($beneficiarioId) {
            $beneficiario = \App\Models\App\Beneficiario\Beneficiario::find($beneficiarioId);

            if ($beneficiario) {
                $pct = null;

                if ($isLatam && $beneficiario->sale_commission_latam_pct !== null) {
                    $pct = (float) $beneficiario->sale_commission_latam_pct;
                } elseif ($isUsaCaEu && $beneficiario->sale_commission_usa_ca_eu_pct !== null) {
                    $pct = (float) $beneficiario->sale_commission_usa_ca_eu_pct;
                }

                if ($pct !== null) {
                    $partnerAmount = round($purchaseAmount * $pct / 100, 2);
                }
            }
        }

        if ($superPartnerId) {
            $superPartner = \App\Models\App\SuperPartner\SuperPartner::find($superPartnerId);

            if ($superPartner) {
                $pct = null;

                if ($isLatam && $superPartner->sale_commission_latam_pct !== null) {
                    $pct = (float) $superPartner->sale_commission_latam_pct;
                } elseif ($isUsaCaEu && $superPartner->sale_commission_usa_ca_eu_pct !== null) {
                    $pct = (float) $superPartner->sale_commission_usa_ca_eu_pct;
                }

                if ($pct !== null) {
                    $superPartnerAmount = round($purchaseAmount * $pct / 100, 2);
                }
            }
        }

        return [
            'partner_sale_commission_amount' => $partnerAmount,
            'super_partner_sale_commission_amount' => $superPartnerAmount,
        ];
    }

    protected function resolveLegacyFreeEsimPrice($owner, float $fallback): float
    {
        if (method_exists($owner, 'getAttribute')) {
            $configuredPrice = $owner->getAttribute('free_esim_price');

            if ($configuredPrice !== null && $configuredPrice !== '') {
                return (float) $configuredPrice;
            }
        }

        return $fallback;
    }
}
