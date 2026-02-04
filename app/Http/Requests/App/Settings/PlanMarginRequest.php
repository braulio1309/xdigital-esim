<?php

namespace App\Http\Requests\App\Settings;

use App\Http\Requests\BaseRequest;

class PlanMarginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Only administrators can update plan margins
        return auth()->check() && auth()->user()->isAppAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'margins' => 'required|array',
            'margins.*.plan_capacity' => 'required|string|in:1,3,5,10,20,50',
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
            'margins.required' => 'Plan margins data is required.',
            'margins.array' => 'Plan margins must be an array.',
            'margins.*.plan_capacity.required' => 'Plan capacity is required.',
            'margins.*.plan_capacity.in' => 'Plan capacity must be one of: 1, 3, 5, 10, 20, 50.',
            'margins.*.margin_percentage.required' => 'Margin percentage is required.',
            'margins.*.margin_percentage.numeric' => 'Margin percentage must be a number.',
            'margins.*.margin_percentage.min' => 'Margin percentage must be at least 0.',
            'margins.*.margin_percentage.max' => 'Margin percentage cannot exceed 100.',
        ];
    }
}
