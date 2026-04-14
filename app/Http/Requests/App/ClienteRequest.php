<?php

namespace App\Http\Requests\App;

use App\Models\App\Cliente\Cliente;
use Illuminate\Validation\Rule;

class ClienteRequest extends AppRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('email')) {
            $this->merge([
                'email' => mb_strtolower(trim((string) $this->input('email'))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $clienteId = $this->route('cliente') ? $this->route('cliente')->id : null;
        $isCreate = $this->isMethod('post');
        $user = auth()->user();
        $canAssociateExistingClient = $isCreate
            && $user
            && in_array($user->user_type, ['beneficiario', 'super_partner'], true);

        $emailRules = ['required', 'email', 'max:255'];

        if (!$canAssociateExistingClient) {
            $emailRules[] = Rule::unique('clientes', 'email')->ignore($clienteId);
        }
        
        return [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'identificador' => 'required|string|max:255',
            'email'    => $emailRules,
            'password' => $isCreate ? 'required|string|min:8' : 'nullable|string|min:8',
        ];
    }
}
