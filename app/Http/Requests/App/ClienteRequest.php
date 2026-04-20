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
        ];
    }
}
