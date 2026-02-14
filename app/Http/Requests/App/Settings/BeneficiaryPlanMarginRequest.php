<?php

namespace App\Http\Requests\App\Settings;

use App\Http\Requests\App\AppRequest;

class BeneficiaryPlanMarginRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'beneficiario_id' => 'required|integer|exists:beneficiarios,id',
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
            'beneficiario_id.required' => __('validation.required', ['attribute' => 'beneficiario']),
            'beneficiario_id.exists' => __('validation.exists', ['attribute' => 'beneficiario']),
            'margins.required' => __('validation.required', ['attribute' => 'margins']),
            'margins.array' => __('validation.array', ['attribute' => 'margins']),
            'margins.*.margin_percentage.required' => __('validation.required', ['attribute' => 'margin percentage']),
            'margins.*.margin_percentage.numeric' => __('validation.numeric', ['attribute' => 'margin percentage']),
            'margins.*.margin_percentage.min' => __('validation.min.numeric', ['attribute' => 'margin percentage', 'min' => 0]),
            'margins.*.margin_percentage.max' => __('validation.max.numeric', ['attribute' => 'margin percentage', 'max' => 100]),
            'margins.*.is_active.boolean' => __('validation.boolean', ['attribute' => 'active status']),
        ];
    }
}
