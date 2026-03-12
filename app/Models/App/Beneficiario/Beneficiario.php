<?php

namespace App\Models\App\Beneficiario;

use App\Models\App\AppModel;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Settings\BeneficiaryPlanMargin;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Beneficiario extends AppModel
{
    use HasFactory;

    public const DEFAULT_FREE_ESIM_RATE = 0.85;

    protected $fillable = [
        'nombre', 
        'descripcion',
        'logo',
        'codigo',
        'user_id',
        'super_partner_id',
        'commission_percentage',
        'total_earnings',
        'total_sales',
        'free_esim_rate',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_sales' => 'integer',
        'free_esim_rate' => 'decimal:2',
    ];

    protected $appends = ['logo_url'];

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
     * Relationship with Cliente model (primary hasMany)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Many-to-many relationship with Cliente (all clients associated with this partner)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function clientesPivot()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_beneficiario', 'beneficiario_id', 'cliente_id')
                    ->withTimestamps();
    }

    /**
     * Relationship with BeneficiaryPlanMargin model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planMargins()
    {
        return $this->hasMany(BeneficiaryPlanMargin::class);
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

    /**
     * Get the free eSIM rate attribute, falling back to default when null.
     *
     * @param  mixed $value
     * @return float
     */
    public function getFreeEsimRateAttribute($value)
    {
        if ($value === null) {
            return self::DEFAULT_FREE_ESIM_RATE;
        }

        return (float) $value;
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

    /**
     * Get the logo URL attribute
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($this->logo);
    }
}
