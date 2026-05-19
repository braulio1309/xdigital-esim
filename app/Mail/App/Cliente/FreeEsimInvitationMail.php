<?php

namespace App\Mail\App\Cliente;

use App\Models\App\Cliente\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreeEsimInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cliente $cliente;
    public string $activationUrl;
    public ?string $partnerName;

    public function __construct(Cliente $cliente, string $activationUrl, ?string $partnerName = null)
    {
        $this->cliente = $cliente;
        $this->activationUrl = $activationUrl;
        $this->partnerName = $partnerName;
    }

    public function build()
    {
        return $this->subject('Tienes una eSIM gratuita disponible')
            ->view('mail.cliente.free-esim-invitation');
    }
}
