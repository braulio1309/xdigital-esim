<?php

namespace Tests\Feature\App\Cliente;

use App\Mail\App\Cliente\EsimActivationMail;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use App\Services\EsimFxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class RegistroEsimControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_an_existing_voucher_for_the_cliente_identificador(): void
    {
        $this->createEligibleCliente();

        $response = $this->from('/registro/esim')->post(route('registro.esim.store'), [
            'identificador' => 'ABC123',
            'email' => 'cliente@example.com',
            'country_code' => 'CO',
        ]);

        $response->assertRedirect('/registro/esim');
        $response->assertSessionHas('error', 'No encontramos vouchers registrados para esta cédula. Verifica los datos o contacta soporte.');
    }

    /** @test */
    public function it_rejects_more_companions_than_the_latest_voucher_allows(): void
    {
        $cliente = $this->createEligibleCliente();

        ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-OLD',
            'numero_personas' => 4,
        ]);

        ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-OK',
            'numero_personas' => 2,
        ]);

        $response = $this->from('/registro/esim')->post(route('registro.esim.store'), [
            'identificador' => 'ABC123',
            'email' => 'cliente@example.com',
            'country_code' => 'CO',
            'companion_emails' => [
                'acompanante1@example.com',
                'acompanante2@example.com',
            ],
        ]);

        $response->assertRedirect('/registro/esim');
        $response->assertSessionHas('error', 'El voucher VOUCHER-OK permite 2 viajero(s) en total. Puedes registrar hasta 1 acompañante(s) además del titular.');
    }

    /** @test */
    public function it_creates_a_distinct_esim_for_each_companion_and_sends_it_to_their_email(): void
    {
        Mail::fake();

        $cliente = $this->createEligibleCliente();

        ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-OK',
            'numero_personas' => 2,
        ]);

        $esimService = Mockery::mock(EsimFxService::class);
        $esimService->shouldReceive('getProducts')
            ->once()
            ->with(['countries' => 'CO'])
            ->andReturn([[
                'id' => 'product-1',
                'amount' => 1,
                'amount_unit' => 'GB',
                'duration' => 7,
                'name' => 'Plan 1GB',
                'price' => 4.25,
            ]]);
        $esimService->shouldReceive('createOrder')
            ->twice()
            ->withArgs(function ($productId, $transactionId) {
                return $productId === 'product-1' && str_starts_with($transactionId, 'WEB-');
            })
            ->andReturn(
                ['id' => 'ORDER-1', 'status' => 'completed', 'esim' => ['iccid' => 'ICCID-1', 'esim_qr' => 'LPA:1$smdp-one$code-one']],
                ['id' => 'ORDER-2', 'status' => 'completed', 'esim' => ['iccid' => 'ICCID-2', 'esim_qr' => 'LPA:1$smdp-two$code-two']]
            );
        $esimService->shouldReceive('activateOrder')
            ->twice()
            ->withArgs(function ($orderId) {
                return in_array($orderId, ['ORDER-1', 'ORDER-2'], true);
            })
            ->andReturn([], []);

        $this->app->instance(EsimFxService::class, $esimService);

        $response = $this->post(route('registro.esim.store'), [
            'identificador' => 'ABC123',
            'email' => 'cliente@example.com',
            'country_code' => 'CO',
            'companion_emails' => [
                'acompanante1@example.com',
            ],
        ]);

        $response->assertOk();

        Mail::assertSent(EsimActivationMail::class, 2);
        Mail::assertSent(EsimActivationMail::class, function (EsimActivationMail $mail) {
            return $mail->recipientEmail === 'cliente@example.com'
                && $mail->esimData['iccid'] === 'ICCID-1'
                && $mail->esimData['code'] === 'code-one';
        });
        Mail::assertSent(EsimActivationMail::class, function (EsimActivationMail $mail) {
            return $mail->recipientEmail === 'acompanante1@example.com'
                && $mail->esimData['iccid'] === 'ICCID-2'
                && $mail->esimData['code'] === 'code-two';
        });

        $this->assertDatabaseCount('transactions', 2);
    }

    private function createEligibleCliente(): Cliente
    {
        return Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Demo',
            'email' => 'cliente@example.com',
            'identificador' => 'ABC123',
            'can_activate_free_esim' => true,
            'free_esim_capacity' => 1,
        ]);
    }
}
