<?php

namespace App\Services\App\Transaction;

use App\Models\App\Transaction\Transaction;
use App\Services\App\AppService;

class TransactionService extends AppService
{
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    /**
     * Update Transaction service
     * @param Transaction $transaction
     * @return Transaction
     */
    public function update(Transaction $transaction)
    {
        $transaction->fill(request()->all());

        $this->model = $transaction;

        $transaction->save();

        return $transaction;
    }

    /**
     * Delete Transaction service
     * @param Transaction $transaction
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Transaction $transaction)
    {
        return $transaction->delete();
    }
}
