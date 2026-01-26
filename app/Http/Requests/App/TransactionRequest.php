<?php

namespace App\Http\Requests\App;

use App\Models\App\Transaction\Transaction;

class TransactionRequest extends AppRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_id' => 'required|string|max:255|unique:transactions,transaction_id,' . optional($this->transaction)->id,
            'status' => 'nullable|string|max:255',
            'iccid' => 'nullable|string|max:255',
            'esim_qr' => 'nullable|string',
            'creation_time' => 'nullable|date',
            'cliente_id' => 'nullable|exists:clientes,id',
        ];
    }
}
