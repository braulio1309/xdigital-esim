<?php

namespace Tests\Feature\App;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerInvoice;
use App\Models\Core\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerInvoiceAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function administrator_can_filter_invoices_by_month_and_update_payment_status(): void
    {
        $admin = $this->createAdmin(['user_type' => 'admin']);
        [$superPartner, $partner] = $this->createPartnerNetwork();
        $septemberInvoice = $this->createInvoice('XC-SP-202609-000001', 'super_partner', $superPartner->id, '2026-09-01');
        $octoberInvoice = $this->createInvoice('XC-P-202610-000002', 'partner', $partner->id, '2026-10-01');

        $response = $this->actingAs($admin)
            ->get(route('partner-invoices.index', ['month' => '2026-09']));

        $response->assertOk()
            ->assertSee($septemberInvoice->invoice_number)
            ->assertSee($superPartner->nombre)
            ->assertDontSee($octoberInvoice->invoice_number);

        $this->actingAs($admin)
            ->patch(route('partner-invoices.payment-status', $septemberInvoice), [
                'is_paid' => 1,
                'month' => '2026-09',
            ])
            ->assertRedirect(route('partner-invoices.index', ['month' => '2026-09']));

        $this->assertTrue($septemberInvoice->fresh()->is_paid);
        $this->assertFalse($octoberInvoice->fresh()->is_paid);
    }

    /** @test */
    public function super_partner_cannot_change_invoice_payment_status(): void
    {
        [$superPartner] = $this->createPartnerNetwork();
        $invoice = $this->createInvoice('XC-SP-202609-000003', 'super_partner', $superPartner->id, '2026-09-01');

        $this->actingAs($superPartner->user)
            ->patch(route('partner-invoices.payment-status', $invoice), ['is_paid' => 1])
            ->assertForbidden();

        $this->assertFalse($invoice->fresh()->is_paid);
    }

    private function createPartnerNetwork(): array
    {
        $superPartnerUser = User::factory()->create(['user_type' => 'super_partner']);
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner Uno',
            'descripcion' => 'Prueba',
            'codigo' => 'SP000001',
            'user_id' => $superPartnerUser->id,
        ]);

        $partnerUser = User::factory()->create(['user_type' => 'beneficiario']);
        $partner = Beneficiario::create([
            'nombre' => 'Partner Uno',
            'descripcion' => 'Prueba',
            'codigo' => 'P000001',
            'user_id' => $partnerUser->id,
            'super_partner_id' => $superPartner->id,
        ]);

        return [$superPartner, $partner];
    }

    private function createInvoice(string $number, string $ownerType, int $ownerId, string $periodStart): PartnerInvoice
    {
        return PartnerInvoice::create([
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'invoice_number' => $number,
            'period_start' => $periodStart,
            'period_end' => date('Y-m-t', strtotime($periodStart)),
            'amount' => 100,
            'paid_amount' => 0,
            'earned_commissions' => 0,
            'applied_commissions' => 0,
            'due_amount' => 100,
            'currency' => 'USD',
            'items' => [],
            'transactions_count' => 1,
            'issued_at' => $periodStart,
        ]);
    }
}
