<?php

namespace App\Mail\App\Cliente;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        $qrPng = null;

        if (!empty($this->esimData['smdp']) && !empty($this->esimData['code'])
            && $this->esimData['smdp'] !== 'N/A' && $this->esimData['code'] !== 'N/A') {
            $activationLink = 'LPA:1$' . $this->esimData['smdp'] . '$' . $this->esimData['code'];
        }

        if ($activationLink) {
            $qrPng = $this->generateQrPng($activationLink);
        }

        return $this->subject('Tu eSIM ya fue activada')
            ->view('mail.esim.activation')
            ->with([
                'esimData' => $this->esimData,
                'recipientEmail' => $this->recipientEmail,
                'partnerName' => $this->partnerName,
                'activationLink' => $activationLink,
                'companionFormUrl' => $this->companionFormUrl,
                'qrPng' => $qrPng,
            ]);
    }

    private function generateQrPng(string $content): string
    {
        $matrix = Encoder::encode($content, ErrorCorrectionLevel::M())->getMatrix();
        $quietZone = 4;
        $canvasSize = 280;
        $moduleCount = $matrix->getWidth();
        $scale = max(1, intdiv($canvasSize, $moduleCount + ($quietZone * 2)));
        $renderedSize = ($moduleCount + ($quietZone * 2)) * $scale;
        $offset = intdiv($canvasSize - $renderedSize, 2);
        $imageData = '';

        for ($y = 0; $y < $canvasSize; $y++) {
            $row = '';
            for ($x = 0; $x < $canvasSize; $x++) {
                $matrixX = intdiv($x - $offset, $scale) - $quietZone;
                $matrixY = intdiv($y - $offset, $scale) - $quietZone;
                $isBlack = $matrixX >= 0 && $matrixX < $moduleCount
                    && $matrixY >= 0 && $matrixY < $matrix->getHeight()
                    && $matrix->get($matrixX, $matrixY) === 1;
                $row .= $isBlack ? "\x00" : "\xFF";
            }
            $imageData .= "\x00" . $row;
        }

        return "\x89PNG\r\n\x1A\n"
            . $this->pngChunk('IHDR', pack('NNCCCCC', $canvasSize, $canvasSize, 8, 0, 0, 0, 0))
            . $this->pngChunk('IDAT', gzcompress($imageData, 9))
            . $this->pngChunk('IEND', '');
    }

    private function pngChunk(string $type, string $data): string
    {
        return pack('N', strlen($data)) . $type . $data . pack('N', crc32($type . $data));
    }
}
