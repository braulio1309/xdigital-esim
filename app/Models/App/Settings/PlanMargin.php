<?php

namespace App\Models\App\Settings;

use Illuminate\Database\Eloquent\Model;

class PlanMargin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_margins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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

        // Validate margin percentage before saving
        static::saving(function ($planMargin) {
            if ($planMargin->margin_percentage < 0 || $planMargin->margin_percentage > 100) {
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
}
