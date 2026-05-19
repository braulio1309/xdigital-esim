<?php

namespace App\Mail\App\Cliente;

use App\Models\App\Transaction\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EsimRechargeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;

    public string $rechargeLink;

    public string $magicToken;

    public function __construct(Transaction $transaction, string $rechargeLink, string $magicToken)
    {
        $this->transaction = $transaction;
        $this->rechargeLink = $rechargeLink;
        $this->magicToken = $magicToken;
    }

    public function build()
    {
        return $this->subject('Recarga tu eSIM antes de quedarte sin datos')
            ->view('mail.esim.recharge-reminder')
            ->with([
                'transaction' => $this->transaction,
                'rechargeLink' => $this->rechargeLink,
                'magicToken' => $this->magicToken,
            ]);
    }
}
