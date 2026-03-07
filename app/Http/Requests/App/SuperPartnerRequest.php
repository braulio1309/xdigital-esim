<?php

namespace App\Http\Requests\App;

use App\Models\App\SuperPartner\SuperPartner;

class SuperPartnerRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $superPartner = $this->route('super_partner');
        $userId = $superPartner ? optional($superPartner->user)->id : null;
        $isCreate = $this->isMethod('post');

        return [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'apellido'    => 'nullable|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email' . ($userId ? ",{$userId}" : ''),
            'password'    => $isCreate ? 'required|string|min:8' : 'nullable|string|min:8',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }
}
