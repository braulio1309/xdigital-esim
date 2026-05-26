<?php

namespace Tests\Feature\App\Cliente;

use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
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

    /** @test */
    public function it_allows_creating_a_cliente_without_nombre_y_apellido(): void
    {
        $this->loginAsAdmin();
        $this->createClienteRoleAndStatus();

        $response = $this->postJson(route('clientes.store'), [
            'nombre' => '',
            'apellido' => '',
            'identificador' => 'ABC999',
            'email' => 'sin-nombre@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('clientes', [
            'identificador' => 'ABC999',
            'email' => 'sin-nombre@example.com',
            'nombre' => '',
            'apellido' => '',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'sin-nombre@example.com',
            'first_name' => '',
            'last_name' => '',
            'user_type' => 'cliente',
        ]);
    }

    private function createClienteRoleAndStatus(): void
    {
        if (!Type::query()->where('alias', 'app')->exists()) {
            Type::create([
                'name' => 'App',
                'alias' => 'app',
            ]);
        }

        if (!Status::query()->where('name', 'status_active')->where('type', 'user')->exists()) {
            Status::create([
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success',
            ]);
        }

        if (!Role::query()->where('name', 'cliente')->exists()) {
            Role::create([
                'name' => 'cliente',
                'type_id' => Type::findByAlias('app')->id,
                'is_admin' => false,
                'is_default' => false,
                'created_by' => User::query()->value('id'),
            ]);
        }
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
