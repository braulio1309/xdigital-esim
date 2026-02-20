<?php

namespace App\Services\App\PaymentHistory;

use App\Models\App\PaymentHistory\PaymentHistory;
use App\Services\App\AppService;

class PaymentHistoryService extends AppService
{
    public function __construct(PaymentHistory $paymentHistory)
    {
        $this->model = $paymentHistory;
    }

    /**
     * Delete PaymentHistory service
     * @param PaymentHistory $paymentHistory
     * @return bool|null
     * @throws \Exception
     */
    public function delete(PaymentHistory $paymentHistory)
    {
        return $paymentHistory->delete();
    }
}
