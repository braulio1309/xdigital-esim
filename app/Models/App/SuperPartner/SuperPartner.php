<?php

namespace App\Models\App\SuperPartner;

use App\Models\App\AppModel;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SuperPartner extends AppModel
{
    use HasFactory;

    protected $table = 'super_partners';

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'codigo',
        'user_id',
        'commission_percentage',
        'total_earnings',
        'total_sales',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'total_earnings'        => 'decimal:2',
        'total_sales'           => 'integer',
    ];

    protected $appends = ['logo_url'];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Beneficiario model (partners created by this super partner)
     */
    public function beneficiarios()
    {
        return $this->hasMany(Beneficiario::class);
    }

    /**
     * Get the referral link attribute
     */
    public function getReferralLinkAttribute()
    {
        return url('/registro/esim/' . Str::slug($this->nombre) . '-' . $this->codigo);
    }

    /**
     * Get the logo URL attribute
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($this->logo);
    }
}
