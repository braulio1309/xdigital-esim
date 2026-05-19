<?php

namespace Tests\Unit\Mail;

use App\Mail\App\Cliente\FreeEsimInvitationMail;
use App\Mail\App\Cliente\LowDataUsageMail;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Transaction\Transaction;
use PHPUnit\Framework\TestCase;

class ClienteMailablesTest extends TestCase
{
    public function test_free_esim_invitation_mail_has_expected_subject(): void
    {
        $cliente = new Cliente([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'juan@example.com',
        ]);

        $mail = new FreeEsimInvitationMail($cliente, 'https://example.com/registro/esim/demo');
        $built = $mail->build();

        $this->assertSame('Tienes una eSIM gratuita disponible', $built->subject);
    }

    public function test_low_data_usage_mail_has_expected_subject(): void
    {
        $cliente = new Cliente([
            'nombre' => 'Ana',
            'apellido' => 'Ruiz',
            'email' => 'ana@example.com',
        ]);

        $transaction = new Transaction([
            'iccid' => '1234567890',
        ]);

        $mail = new LowDataUsageMail($cliente, $transaction, 'https://example.com/recarga/token/test', 78);
        $built = $mail->build();

        $this->assertSame('Tu eSIM está cerca de agotarse', $built->subject);
    }
}
