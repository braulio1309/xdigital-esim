<?php

namespace App\Models\App\Cliente;

use App\Models\App\AppModel;
use App\Models\App\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends AppModel
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'email'];

    /**
     * Relationship with Transaction model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
