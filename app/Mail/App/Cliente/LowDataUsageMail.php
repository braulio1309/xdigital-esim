<?php

namespace App\Mail\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowDataUsageMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cliente $cliente;
    public Transaction $transaction;
    public string $rechargeUrl;
    public float $usagePercentage;
    public int $threshold;

    public function __construct(
        Cliente $cliente,
        Transaction $transaction,
        string $rechargeUrl,
        float $usagePercentage,
        int $threshold = 75
    )
    {
        $this->cliente = $cliente;
        $this->transaction = $transaction;
        $this->rechargeUrl = $rechargeUrl;
        $this->usagePercentage = $usagePercentage;
        $this->threshold = $threshold;
    }

    public function build()
    {
        return $this->subject('Tu eSIM está cerca de agotarse')
            ->view('mail.cliente.low-data-usage');
    }
}
