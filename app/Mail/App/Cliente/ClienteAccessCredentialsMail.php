<?php

namespace App\Mail\App\Cliente;

use App\Models\App\Cliente\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClienteAccessCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cliente $cliente;

    public string $loginUrl;

    public string $plainPassword;

    public function __construct(Cliente $cliente, string $plainPassword, string $loginUrl)
    {
        $this->cliente = $cliente;
        $this->plainPassword = $plainPassword;
        $this->loginUrl = $loginUrl;
    }

    public function build()
    {
        return $this->subject('Acceso a tu cuenta eSIM')
            ->view('mail.cliente.access-credentials')
            ->with([
                'cliente' => $this->cliente,
                'loginUrl' => $this->loginUrl,
                'plainPassword' => $this->plainPassword,
            ]);
    }
}