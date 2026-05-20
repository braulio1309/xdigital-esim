<?php

namespace App\Http\Requests\App;

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
        $isCreate = $this->isMethod('post');

        return [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'identificador' => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255'],
            'password' => $isCreate ? 'required|string|min:8' : 'nullable|string|min:8',
            'free_esim_capacity' => 'nullable|integer|in:1,3,5,10',
            'numero_voucher'     => 'nullable|string|max:255|required_with:numero_personas',
            'numero_personas'    => 'nullable|integer|min:1|max:9999|required_with:numero_voucher',
            'voucher_edit_id'    => 'nullable|integer',
        ];
    }
}
