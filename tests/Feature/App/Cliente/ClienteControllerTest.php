<?php

namespace Tests\Feature\App\Cliente;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\Cliente\ClienteVoucher;
use App\Models\App\SuperPartner\SuperPartner;
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
    public function super_partners_cannot_delete_clientes(): void
    {
        $this->actingAs($this->createUserOfType('super_partner'));

        $cliente = $this->createCliente();

        $response = $this->deleteJson(route('clientes.destroy', $cliente));

        $response->assertForbidden();
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
        ]);
    }

    /** @test */
    public function partners_cannot_delete_clientes(): void
    {
        $this->actingAs($this->createUserOfType('beneficiario'));

        $cliente = $this->createCliente();

        $response = $this->deleteJson(route('clientes.destroy', $cliente));

        $response->assertForbidden();
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
        ]);
    }

    /** @test */
    public function partners_can_inactivate_their_clientes(): void
    {
        $partnerUser = $this->createUserOfType('beneficiario');
        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Partner de prueba',
            'user_id' => $partnerUser->id,
        ]);

        $cliente = $this->createClienteWithUser([
            'beneficiario_id' => $beneficiario->id,
        ]);

        $response = $this->actingAs($partnerUser)
            ->postJson(route('clientes.toggle-status', $cliente));

        $response->assertOk();
        $this->assertSame('status_inactive', $cliente->user->fresh()->status->name);
    }

    /** @test */
    public function super_partners_can_inactivate_clientes_in_their_network(): void
    {
        $superPartnerUser = $this->createUserOfType('super_partner');
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner Uno',
            'descripcion' => 'SP de prueba',
            'codigo' => 'SP000001',
            'user_id' => $superPartnerUser->id,
        ]);
        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Dos',
            'descripcion' => 'Partner de red',
            'super_partner_id' => $superPartner->id,
        ]);

        $cliente = $this->createClienteWithUser([
            'beneficiario_id' => $beneficiario->id,
        ]);

        $response = $this->actingAs($superPartnerUser)
            ->postJson(route('clientes.toggle-status', $cliente));

        $response->assertOk();
        $this->assertSame('status_inactive', $cliente->user->fresh()->status->name);
    }

    /** @test */
    public function partners_can_reactivate_an_inactive_cliente(): void
    {
        $partnerUser = $this->createUserOfType('beneficiario');
        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Partner de prueba',
            'user_id' => $partnerUser->id,
        ]);

        $cliente = $this->createClienteWithUser([
            'beneficiario_id' => $beneficiario->id,
        ]);

        $inactiveStatusId = Status::query()
            ->where('name', 'status_inactive')
            ->where('type', 'user')
            ->value('id');

        $cliente->user->update([
            'status_id' => $inactiveStatusId,
        ]);

        $response = $this->actingAs($partnerUser)
            ->postJson(route('clientes.toggle-status', $cliente));

        $response->assertOk();
        $this->assertSame('status_active', $cliente->user->fresh()->status->name);
    }

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

    /** @test */
    public function it_does_not_convert_partner_or_super_partner_accounts_into_clientes(): void
    {
        $this->loginAsAdmin();
        $this->createClienteRoleAndStatus();

        foreach (['beneficiario', 'super_partner'] as $userType) {
            $existingUser = $this->createUserOfType($userType);

            $response = $this->postJson(route('clientes.store'), [
                'nombre' => 'Nuevo',
                'apellido' => 'Cliente',
                'identificador' => 'ID-' . strtoupper($userType),
                'email' => $existingUser->email,
                'password' => 'password123',
            ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors('email');

            $this->assertDatabaseMissing('clientes', [
                'email' => $existingUser->email,
            ]);
        }
    }

    private function createClienteRoleAndStatus(): void
    {
        if (!Type::query()->where('alias', 'app')->exists()) {
            Type::create([
                'name' => 'App',
                'alias' => 'app',
            ]);
        }

        $this->ensureUserStatusesExist();

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

    private function createClienteWithUser(array $clienteAttributes = []): Cliente
    {
        $this->createClienteRoleAndStatus();

        $user = User::factory()->create([
            'user_type' => 'cliente',
            'status_id' => Status::query()->where('name', 'status_active')->where('type', 'user')->value('id'),
        ]);

        return Cliente::create(array_merge([
            'nombre' => 'Cliente',
            'apellido' => 'Demo',
            'email' => $user->email,
            'identificador' => 'ABC123',
            'user_id' => $user->id,
            'can_activate_free_esim' => true,
            'free_esim_capacity' => 1,
        ], $clienteAttributes));
    }

    private function createUserOfType(string $userType): User
    {
        $this->ensureUserStatusesExist();

        return User::factory()->create([
            'user_type' => $userType,
            'status_id' => Status::query()->where('name', 'status_active')->where('type', 'user')->value('id'),
        ]);
    }

    private function ensureUserStatusesExist(): void
    {
        if (!Status::query()->where('name', 'status_active')->where('type', 'user')->exists()) {
            Status::create([
                'name' => 'status_active',
                'type' => 'user',
                'class' => 'success',
            ]);
        }

        if (!Status::query()->where('name', 'status_inactive')->where('type', 'user')->exists()) {
            Status::create([
                'name' => 'status_inactive',
                'type' => 'user',
                'class' => 'secondary',
            ]);
        }
    }
}
