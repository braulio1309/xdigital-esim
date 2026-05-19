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

    public function __construct(Transaction $transaction, string $rechargeLink)
    {
        $this->transaction = $transaction;
        $this->rechargeLink = $rechargeLink;
    }

    public function build()
    {
        return $this->subject('Recarga tu eSIM antes de quedarte sin datos')
            ->view('mail.esim.recharge-reminder')
            ->with([
                'transaction' => $this->transaction,
                'rechargeLink' => $this->rechargeLink,
            ]);
    }
}
