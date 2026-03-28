<?php

namespace App\Models\Core\Auth;

use Illuminate\Database\Eloquent\Model;

class UsuarioClaveHistorial extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'usuarios_claves_historial';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'password'];
}
