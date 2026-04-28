<?php

namespace App\Services\App\Cliente;

use App\Mail\App\Cliente\ClienteAccessCredentialsMail;
use App\Models\App\Cliente\Cliente;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class ClienteAccessMailService
{
    public function buildPasswordFromIdentifier(string $identifier): string
    {
        return trim($identifier) . '+';
    }

    public function sendAccessCredentials(Cliente $cliente): void
    {
        if (empty($cliente->email)) {
            throw new InvalidArgumentException('El cliente no tiene correo electrónico.');
        }

        if (empty($cliente->identificador)) {
            throw new InvalidArgumentException('El cliente no tiene cédula o identificador.');
        }

        $loginUrl = 'https://esim.xcertus.com';
        $plainPassword = $this->buildPasswordFromIdentifier($cliente->identificador);

        Mail::to($cliente->email)->send(new ClienteAccessCredentialsMail($cliente, $plainPassword, $loginUrl));
    }
}