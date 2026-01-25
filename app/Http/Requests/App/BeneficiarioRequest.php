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
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ];
    }
}
