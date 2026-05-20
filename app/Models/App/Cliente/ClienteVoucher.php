<?php

namespace App\Models\App\Cliente;

use App\Models\App\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClienteVoucher extends AppModel
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'numero_voucher', 'numero_personas'];

    protected $casts = [
        'numero_personas' => 'integer',
    ];

    /**
     * Relationship with Cliente model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
