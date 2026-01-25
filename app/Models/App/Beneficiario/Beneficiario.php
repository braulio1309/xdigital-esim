<?php

namespace App\Models\App\Beneficiario;

use App\Models\App\AppModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiario extends AppModel
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];
}
