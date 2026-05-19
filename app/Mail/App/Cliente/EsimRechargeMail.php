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

    public ?string $rechargeUrl;

    public function __construct(
        string $recipientEmail,
        int $gbAmount,
        ?string $iccid = null,
        ?string $planName = null,
        ?string $rechargeUrl = null
    )
    {
        $this->recipientEmail = $recipientEmail;
        $this->gbAmount = $gbAmount;
        $this->iccid = $iccid;
        $this->planName = $planName;
        $this->rechargeUrl = $rechargeUrl;
    }

    public function build()
    {
        $subject = $this->rechargeUrl
            ? 'Recarga tu eSIM'
            : 'Tu eSIM fue recargada con ' . $this->gbAmount . ' GB';

        return $this->subject($subject)
            ->view('mail.esim.recharge')
            ->with([
                'recipientEmail' => $this->recipientEmail,
                'gbAmount' => $this->gbAmount,
                'iccid' => $this->iccid,
                'planName' => $this->planName,
                'rechargeUrl' => $this->rechargeUrl,
            ]);
    }
}
