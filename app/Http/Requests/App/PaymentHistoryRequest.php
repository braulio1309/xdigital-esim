<?php

namespace App\Http\Requests\App;

class PaymentHistoryRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'amount' => 'nullable|numeric|min:0',
            'transactions_count' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
