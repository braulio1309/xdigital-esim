<?php

namespace App\Mail\App\Cliente;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EsimActivationMail extends Mailable
{
    use Queueable, SerializesModels;

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
        $qrImagePath = null;

        if (!empty($this->esimData['smdp']) && !empty($this->esimData['code'])
            && $this->esimData['smdp'] !== 'N/A' && $this->esimData['code'] !== 'N/A') {
            $activationLink = 'LPA:1$' . $this->esimData['smdp'] . '$' . $this->esimData['code'];
        }

        if ($activationLink) {
            $qrPng = QrCode::format('png')->size(280)->margin(1)->generate($activationLink);
            $tempPath = tempnam(sys_get_temp_dir(), 'esim-qr-');

            if ($tempPath !== false) {
                $qrImagePath = $tempPath . '.png';

                if (@rename($tempPath, $qrImagePath) === false) {
                    $qrImagePath = $tempPath;
                }

                file_put_contents($qrImagePath, $qrPng);

                register_shutdown_function(static function () use ($qrImagePath) {
                    if (is_string($qrImagePath) && is_file($qrImagePath)) {
                        @unlink($qrImagePath);
                    }
                });
            }
        }

        return $this->subject('Tu eSIM ya fue activada')
            ->view('mail.esim.activation')
            ->with([
                'esimData' => $this->esimData,
                'recipientEmail' => $this->recipientEmail,
                'partnerName' => $this->partnerName,
                'activationLink' => $activationLink,
                'companionFormUrl' => $this->companionFormUrl,
                'qrImagePath' => $qrImagePath,
            ]);
    }
}
