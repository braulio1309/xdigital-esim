<?php

namespace App\Models\App\Cliente;

use App\Models\App\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends AppModel
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'email'];
}
