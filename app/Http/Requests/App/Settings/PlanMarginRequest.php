<?php

namespace App\Http\Requests\App\Settings;

use App\Http\Requests\App\AppRequest;

class PlanMarginRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'margins' => 'required|array',
            'margins.*.margin_percentage' => 'required|numeric|min:0|max:100',
            'margins.*.is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'margins.required' => 'Los márgenes son requeridos.',
            'margins.array' => 'Los márgenes deben ser un array.',
            'margins.*.margin_percentage.required' => 'El porcentaje de margen es requerido.',
            'margins.*.margin_percentage.numeric' => 'El porcentaje de margen debe ser un número.',
            'margins.*.margin_percentage.min' => 'El porcentaje de margen debe ser al menos 0.',
            'margins.*.margin_percentage.max' => 'El porcentaje de margen no puede ser mayor a 100.',
            'margins.*.is_active.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}
