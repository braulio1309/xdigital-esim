<?php

namespace App\Mail\App\Cliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Mime\Part\DataPart;

class EsimActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    private const QR_CONTENT_ID = 'esim-activation-qr@xcertus.local';

    public array $esimData;

    public string $recipientEmail;

    public ?string $partnerName;

    public ?string $companionFormUrl;

    public function __construct(array $esimData, string $recipientEmail, ?string $partnerName = null, ?string $companionFormUrl = null)
    {
        $this->esimData = $esimData;
        $this->recipientEmail = $recipientEmail;
        $this->partnerName = $partnerName;
        $this->companionFormUrl = $companionFormUrl;
    }

    public function build()
    {
        $activationLink = null;
        $qrPng = null;

        if (!empty($this->esimData['smdp']) && !empty($this->esimData['code'])
            && $this->esimData['smdp'] !== 'N/A' && $this->esimData['code'] !== 'N/A') {
            $activationLink = 'LPA:1$' . $this->esimData['smdp'] . '$' . $this->esimData['code'];
        }

        if ($activationLink) {
            $qrPng = QrCode::format('png')->size(280)->margin(1)->generate($activationLink);

            $this->withSymfonyMessage(function ($message) use ($qrPng) {
                $message->addPart(
                    (new DataPart($qrPng, 'esim-activation-qr.png', 'image/png'))
                        ->asInline()
                        ->setContentId(self::QR_CONTENT_ID)
                );
            });
        }

        return $this->subject('Tu eSIM ya fue activada')
            ->view('mail.esim.activation')
            ->with([
                'esimData' => $this->esimData,
                'recipientEmail' => $this->recipientEmail,
                'partnerName' => $this->partnerName,
                'activationLink' => $activationLink,
                'companionFormUrl' => $this->companionFormUrl,
                'qrImageSrc' => $qrPng ? 'cid:' . self::QR_CONTENT_ID : null,
            ]);
    }
}
