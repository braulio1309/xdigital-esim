<?php

namespace App\Models\App\Settings;

use App\Models\App\SuperPartner\SuperPartner;
use Illuminate\Database\Eloquent\Model;

class SuperPartnerPlanMargin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'super_partner_plan_margins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'super_partner_id',
        'plan_capacity',
        'margin_percentage',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'margin_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($margin) {
            if ($margin->margin_percentage < 0 || $margin->margin_percentage > 100) {
                throw new \InvalidArgumentException('Margin percentage must be between 0 and 100');
            }
        });
    }

    /**
     * Get margin as decimal (e.g., 30% becomes 0.30)
     *
     * @return float
     */
    public function getMarginDecimalAttribute()
    {
        return $this->margin_percentage / 100;
    }

    /**
     * Relationship with SuperPartner model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function superPartner()
    {
        return $this->belongsTo(SuperPartner::class);
    }
}
