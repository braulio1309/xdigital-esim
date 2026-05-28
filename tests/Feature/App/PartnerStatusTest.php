<?php

namespace Tests\Feature\App;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_inactivate_a_super_partner_without_reactivation_flow(): void
    {
        $admin = $this->createAdmin();
        $statusId = $this->activeStatusId();

        $user = User::factory()->create([
            'user_type' => 'super_partner',
            'status_id' => $statusId,
        ]);

        $superPartner = SuperPartner::create([
            'nombre' => 'SP Uno',
            'descripcion' => 'Demo',
            'codigo' => 'SP000001',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('super-partners.inactivate', $superPartner));

        $response->assertOk();
        $this->assertSame('status_inactive', $user->fresh()->status->name);
    }

    /** @test */
    public function admin_can_reactivate_a_super_partner(): void
    {
        $admin = $this->createAdmin();

        $user = User::factory()->create([
            'user_type' => 'super_partner',
            'status_id' => $this->inactiveStatusId(),
        ]);

        $superPartner = SuperPartner::create([
            'nombre' => 'SP Dos',
            'descripcion' => 'Demo',
            'codigo' => 'SP000002',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('super-partners.activate', $superPartner));

        $response->assertOk();
        $this->assertSame('status_active', $user->fresh()->status->name);
    }

    /** @test */
    public function super_partner_can_inactivate_a_partner_in_their_network(): void
    {
        $this->ensureStatusesAndRoles();

        $superPartnerOwner = User::factory()->create([
            'user_type' => 'super_partner',
            'status_id' => $this->activeStatusId(),
        ]);
        $superPartnerOwner->assignRole('Super Partner');

        $superPartner = SuperPartner::create([
            'nombre' => 'SP Uno',
            'descripcion' => 'Demo',
            'codigo' => 'SP000001',
            'user_id' => $superPartnerOwner->id,
        ]);

        $partnerUser = User::factory()->create([
            'user_type' => 'beneficiario',
            'status_id' => $this->activeStatusId(),
        ]);
        $partnerUser->assignRole('beneficiario');

        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Demo',
            'user_id' => $partnerUser->id,
            'super_partner_id' => $superPartner->id,
        ]);

        $response = $this->actingAs($superPartnerOwner)
            ->postJson(route('beneficiarios.inactivate', $beneficiario));

        $response->assertOk();
        $this->assertSame('status_inactive', $partnerUser->fresh()->status->name);
    }

    /** @test */
    public function super_partner_can_reactivate_a_partner_in_their_network(): void
    {
        $this->ensureStatusesAndRoles();

        $superPartnerOwner = User::factory()->create([
            'user_type' => 'super_partner',
            'status_id' => $this->activeStatusId(),
        ]);
        $superPartnerOwner->assignRole('Super Partner');

        $superPartner = SuperPartner::create([
            'nombre' => 'SP Uno',
            'descripcion' => 'Demo',
            'codigo' => 'SP000001',
            'user_id' => $superPartnerOwner->id,
        ]);

        $partnerUser = User::factory()->create([
            'user_type' => 'beneficiario',
            'status_id' => $this->inactiveStatusId(),
        ]);
        $partnerUser->assignRole('beneficiario');

        $beneficiario = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Demo',
            'user_id' => $partnerUser->id,
            'super_partner_id' => $superPartner->id,
        ]);

        $response = $this->actingAs($superPartnerOwner)
            ->postJson(route('beneficiarios.activate', $beneficiario));

        $response->assertOk();
        $this->assertSame('status_active', $partnerUser->fresh()->status->name);
    }

    private function ensureStatusesAndRoles(): void
    {
        if (!Type::query()->where('alias', 'app')->exists()) {
            Type::create(['name' => 'App', 'alias' => 'app']);
        }

        if (!Role::query()->where('name', 'Super Partner')->exists()) {
            Role::create([
                'name' => 'Super Partner',
                'is_default' => 0,
                'is_admin' => 0,
                'type_id' => Type::query()->where('alias', 'app')->value('id'),
            ]);
        }

        if (!Role::query()->where('name', 'beneficiario')->exists()) {
            Role::create([
                'name' => 'beneficiario',
                'is_default' => 0,
                'is_admin' => 0,
                'type_id' => Type::query()->where('alias', 'app')->value('id'),
            ]);
        }

        if (!Status::query()->where('name', 'status_active')->where('type', 'user')->exists()) {
            Status::create(['name' => 'status_active', 'type' => 'user', 'class' => 'success']);
        }

        if (!Status::query()->where('name', 'status_inactive')->where('type', 'user')->exists()) {
            Status::create(['name' => 'status_inactive', 'type' => 'user', 'class' => 'secondary']);
        }
    }

    private function activeStatusId(): int
    {
        $this->ensureStatusesAndRoles();

        return (int) Status::query()->where('name', 'status_active')->where('type', 'user')->value('id');
    }

    private function inactiveStatusId(): int
    {
        $this->ensureStatusesAndRoles();

        return (int) Status::query()->where('name', 'status_inactive')->where('type', 'user')->value('id');
    }
}