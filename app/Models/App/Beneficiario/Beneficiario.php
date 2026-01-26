<?php

namespace App\Models\App\Beneficiario;

use App\Models\App\AppModel;
use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiario extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'user_id',
        'commission_percentage',
        'total_earnings',
        'total_sales'
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_sales' => 'integer',
    ];

    /**
     * Relationship with User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
