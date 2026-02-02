<?php

namespace App\Models\App\Beneficiario;

use App\Models\App\AppModel;
use App\Models\App\Cliente\Cliente;
use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Beneficiario extends AppModel
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'descripcion', 
        'codigo',
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

    /**
     * Relationship with Cliente model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Get the referral link attribute
     *
     * @return string
     */
    public function getReferralLinkAttribute()
    {
        return url('/registro/esim/' . Str::slug($this->nombre) . '-' . $this->codigo);
    }
}
