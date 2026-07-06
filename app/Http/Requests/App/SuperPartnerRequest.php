<?php

namespace App\Http\Requests\App;

use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use Illuminate\Validation\Rule;

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

        $emailRules = ['required', 'email', 'max:255'];

        if ($isCreate) {
            $emailRules[] = function ($attribute, $value, $fail) {
                $email = mb_strtolower(trim((string) $value));
                $existingUser = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

                if ($existingUser && !$existingUser->hasRole('cliente')) {
                    $fail('El correo ya está en uso por otro tipo de usuario.');
                }
            };
        } else {
            $emailRules[] = Rule::unique('users', 'email')->ignore($userId);
        }

        return [
            'nombre'                => 'required|string|max:255',
            'descripcion'           => 'nullable|string|max:255',
            'apellido'              => 'nullable|string|max:255',
            'email'                 => $emailRules,
            'password'              => $isCreate ? 'required|string|min:8' : 'nullable|string|min:8',
            'logo'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
