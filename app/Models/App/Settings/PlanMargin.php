<?php

namespace App\Models\App\Settings;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanMargin extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'plan_capacity',
        'margin_percentage',
        'is_active',
    ];

    protected $casts = [
        'margin_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Validation rules for the model
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'plan_capacity' => 'required|string|in:1,3,5,10,20,50|unique:plan_margins,plan_capacity',
            'margin_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the margin as a decimal (0.30 for 30%)
     *
     * @return float
     */
    public function getMarginDecimal()
    {
        return $this->margin_percentage / 100;
    }
}
