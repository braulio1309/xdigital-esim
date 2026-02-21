<?php

namespace App\Http\Requests\App;

use App\Models\App\Beneficiario\Beneficiario;

class BeneficiarioRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $beneficiario = $this->route('beneficiario');
        $userId = $beneficiario ? optional($beneficiario->user)->id : null;
        $isCreate = $this->isMethod('post');

        return [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'apellido'    => 'nullable|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email' . ($userId ? ",{$userId}" : ''),
            'password'    => $isCreate ? 'required|string|min:8' : 'nullable|string|min:8',
        ];
    }
}
