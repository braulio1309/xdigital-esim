<?php

namespace App\Mail\App\Cliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EsimActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $esimData;

    public string $recipientEmail;

    public ?string $partnerName;

    public function __construct(array $esimData, string $recipientEmail, ?string $partnerName = null)
    {
        $this->esimData = $esimData;
        $this->recipientEmail = $recipientEmail;
        $this->partnerName = $partnerName;
    }

    public function build()
    {
        $activationLink = null;

        if (!empty($this->esimData['smdp']) && !empty($this->esimData['code'])
            && $this->esimData['smdp'] !== 'N/A' && $this->esimData['code'] !== 'N/A') {
            $activationLink = 'LPA:1$' . $this->esimData['smdp'] . '$' . $this->esimData['code'];
        }

        return $this->subject('Tu eSIM ya fue activada')
            ->view('mail.esim.activation')
            ->with([
                'esimData' => $this->esimData,
                'recipientEmail' => $this->recipientEmail,
                'partnerName' => $this->partnerName,
                'activationLink' => $activationLink,
            ]);
    }
}