<?php

namespace Tests\Feature\App\Users;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function super_partners_only_see_users_from_their_network(): void
    {
        $this->ensureUserStatusesExist();

        $superPartnerUser = $this->createUserOfType('super_partner');
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner Uno',
            'descripcion' => 'SP de prueba',
            'codigo' => 'SP000001',
            'user_id' => $superPartnerUser->id,
        ]);

        $visibleUser = $this->createUserOfType('admin_partner', [
            'super_partner_id' => $superPartner->id,
        ]);

        $hiddenUser = $this->createUserOfType('admin_partner');

        $response = $this->actingAs($superPartnerUser)
            ->getJson('/admin/auth/users');

        $response->assertOk()
            ->assertJsonFragment(['email' => $superPartnerUser->email])
            ->assertJsonFragment(['email' => $visibleUser->email])
            ->assertJsonMissing(['email' => $hiddenUser->email]);
    }

    /** @test */
    public function partners_only_see_users_from_their_network(): void
    {
        $this->ensureUserStatusesExist();

        $partnerUser = $this->createUserOfType('beneficiario');
        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Partner de prueba',
            'user_id' => $partnerUser->id,
        ]);

        $visibleUser = $this->createUserOfType('admin_beneficiario', [
            'beneficiario_id' => $beneficiario->id,
        ]);

        $hiddenUser = $this->createUserOfType('admin_beneficiario');

        $response = $this->actingAs($partnerUser)
            ->getJson('/admin/auth/users');

        $response->assertOk()
            ->assertJsonFragment(['email' => $partnerUser->email])
            ->assertJsonFragment(['email' => $visibleUser->email])
            ->assertJsonMissing(['email' => $hiddenUser->email]);
    }

    private function createUserOfType(string $userType, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'user_type' => $userType,
            'status_id' => Status::query()->where('name', 'status_active')->where('type', 'user')->value('id'),
        ], $attributes));
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