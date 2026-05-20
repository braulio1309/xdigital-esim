<?php

namespace Tests\Feature\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_the_latest_voucher_data_when_loading_a_cliente_for_editing(): void
    {
        $this->loginAsAdmin();

        $cliente = $this->createCliente();

        ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-ANTERIOR',
            'numero_personas' => 2,
        ]);

        $latestVoucher = ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-ACTUAL',
            'numero_personas' => 4,
        ]);

        $response = $this->getJson(route('clientes.show', $cliente));

        $response->assertOk();
        $response->assertJsonFragment([
            'numero_voucher' => 'VOUCHER-ACTUAL',
            'numero_personas' => 4,
            'voucher_edit_id' => $latestVoucher->id,
        ]);
    }

    /** @test */
    public function it_updates_the_existing_voucher_when_editing_a_cliente(): void
    {
        $this->loginAsAdmin();

        $cliente = $this->createCliente();
        $voucher = ClienteVoucher::create([
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-OLD',
            'numero_personas' => 2,
        ]);

        $response = $this->patchJson(route('clientes.update', $cliente), [
            'nombre' => 'Cliente',
            'apellido' => 'Actualizado',
            'identificador' => 'ABC123',
            'email' => 'cliente@example.com',
            'numero_voucher' => 'VOUCHER-NEW',
            'numero_personas' => 5,
            'voucher_edit_id' => $voucher->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('cliente_vouchers', [
            'id' => $voucher->id,
            'cliente_id' => $cliente->id,
            'numero_voucher' => 'VOUCHER-NEW',
            'numero_personas' => 5,
        ]);
        $this->assertSame(1, ClienteVoucher::count());
    }

    private function createCliente(): Cliente
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
