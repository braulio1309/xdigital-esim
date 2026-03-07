<?php

namespace App\Models\App\Cliente;

use App\Models\App\AppModel;
use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends AppModel
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'email', 'user_id', 'beneficiario_id', 'can_activate_free_esim'];

    protected $casts = [
        'can_activate_free_esim' => 'boolean',
    ];

    /**
     * Relationship with Transaction model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

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
     * Relationship with Beneficiario model (primary partner)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    /**
     * Many-to-many relationship with Beneficiario (all partners associated with this client)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function partners()
    {
        return $this->belongsToMany(Beneficiario::class, 'cliente_beneficiario', 'cliente_id', 'beneficiario_id')
                    ->withTimestamps();
    }

    /**
     * Get the active plan (latest active transaction)
     *
     * @return Transaction|null
     */
    public function getActivePlanAttribute()
    {
        return $this->transactions()
            ->where('status', 'completed')
            ->latest('creation_time')
            ->first();
    }
}
