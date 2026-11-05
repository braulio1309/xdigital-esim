<?php

namespace Tests\Feature\App;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\Cliente\Cliente;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\Core\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PartnerFollowUpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_filter_registered_client_counts_by_date_and_export_them(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        [$superPartner, $firstPartner, $secondPartner] = $this->createPartnerNetwork();

        $this->createClient($firstPartner, 'first-october@example.test', '2026-10-10 12:00:00');
        $this->createClient($firstPartner, 'first-november@example.test', '2026-11-02 12:00:00');
        $this->createClient($secondPartner, 'second-october@example.test', '2026-10-20 12:00:00');

        $filters = ['start_date' => '2026-10-01', 'end_date' => '2026-10-31'];
        $response = $this->actingAs($admin)
            ->getJson(route('beneficiarios.follow-up', $filters))
            ->assertOk();

        $this->assertSame(
            [$firstPartner->id, $secondPartner->id],
            collect($response->json('partners'))->pluck('id')->all()
        );
        $this->assertSame([1, 1], collect($response->json('partners'))->pluck('registered_clients_count')->all());

        $export = $this->actingAs($admin)
            ->get(route('beneficiarios.follow-up-export', $filters));
        $export->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', $export->getContent());

        $clientExport = $this->actingAs($admin)
            ->get(route('beneficiarios.follow-up-clients-export', [
                'beneficiario' => $firstPartner->id,
            ] + $filters));
        $clientExport->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', $clientExport->getContent());
    }

    /** @test */
    public function super_partner_only_sees_client_counts_for_its_own_partners(): void
    {
        $owner = User::factory()->create(['user_type' => 'super_partner']);
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner Uno',
            'descripcion' => 'Prueba',
            'codigo' => 'SP000001',
            'user_id' => $owner->id,
        ]);
        $ownPartner = $this->createPartner('Partner propio', 'P000001', $superPartner->id);

        [, $otherPartner] = $this->createPartnerNetwork('Dos');
        $this->createClient($ownPartner, 'own-client@example.test', '2026-10-10 12:00:00');
        $this->createClient($otherPartner, 'other-client@example.test', '2026-10-10 12:00:00');

        $this->actingAs($owner)
            ->getJson(route('beneficiarios.follow-up'))
            ->assertOk()
            ->assertJsonCount(1, 'partners')
            ->assertJsonPath('partners.0.id', $ownPartner->id)
            ->assertJsonPath('partners.0.registered_clients_count', 1);

        $this->actingAs($owner)
            ->get(route('beneficiarios.follow-up-clients-export', [
                'beneficiario' => $otherPartner->id,
            ]))
            ->assertNotFound();
    }

    private function createPartnerNetwork(string $suffix = 'Uno'): array
    {
        $owner = User::factory()->create(['user_type' => 'super_partner']);
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner ' . $suffix,
            'descripcion' => 'Prueba',
            'codigo' => $suffix === 'Uno' ? 'SP000002' : 'SP000003',
            'user_id' => $owner->id,
        ]);

        $firstPartner = $this->createPartner('Partner A ' . $suffix, 'PA' . ($suffix === 'Uno' ? '00001' : '00002'), $superPartner->id);
        $secondPartner = $this->createPartner('Partner B ' . $suffix, 'PB' . ($suffix === 'Uno' ? '00001' : '00002'), $superPartner->id);

        return [$superPartner, $firstPartner, $secondPartner];
    }

    private function createPartner(string $name, string $code, int $superPartnerId): Beneficiario
    {
        return Beneficiario::create([
            'nombre' => $name,
            'descripcion' => 'Prueba',
            'codigo' => $code,
            'super_partner_id' => $superPartnerId,
        ]);
    }

    private function createClient(Beneficiario $partner, string $email, string $createdAt): Cliente
    {
        $client = Cliente::create([
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'email' => $email,
            'beneficiario_id' => $partner->id,
        ]);
        DB::table('clientes')->where('id', $client->id)->update(['created_at' => $createdAt]);

        return $client;
    }
}
