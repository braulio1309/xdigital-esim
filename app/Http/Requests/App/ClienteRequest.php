<?php

namespace App\Http\Requests\App;

use App\Models\App\Cliente\Cliente;

class ClienteRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $clienteId = $this->route('cliente') ? $this->route('cliente')->id : null;
        
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes,email,' . $clienteId,
        ];
    }
}
