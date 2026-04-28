<?php

namespace App\Mail\App\Cliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EsimRechargeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientEmail;

    public int $gbAmount;

    public ?string $iccid;

    public ?string $planName;

    public function __construct(string $recipientEmail, int $gbAmount, ?string $iccid = null, ?string $planName = null)
    {
        $this->recipientEmail = $recipientEmail;
        $this->gbAmount = $gbAmount;
        $this->iccid = $iccid;
        $this->planName = $planName;
    }

    public function build()
    {
        return $this->subject('Tu eSIM fue recargada con ' . $this->gbAmount . ' GB')
            ->view('mail.esim.recharge')
            ->with([
                'recipientEmail' => $this->recipientEmail,
                'gbAmount' => $this->gbAmount,
                'iccid' => $this->iccid,
                'planName' => $this->planName,
            ]);
    }
}
