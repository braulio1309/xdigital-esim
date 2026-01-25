<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;

class UserRegistrationRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', 'regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i']
        ];
    }
}
