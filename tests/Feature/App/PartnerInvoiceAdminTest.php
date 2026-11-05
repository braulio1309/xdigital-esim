<?php

namespace Tests\Feature\App;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\PartnerCommissionNote;
use App\Models\App\Transaction\PartnerInvoice;
use App\Models\App\Transaction\Transaction;
use App\Models\Core\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerInvoiceAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function administrator_sees_no_invoice_information_until_applying_a_filter(): void
    {
        $admin = $this->createAdmin(['user_type' => 'admin']);
        [$superPartner] = $this->createPartnerNetwork();
        $invoice = $this->createInvoice('XC-SP-202609-000001', 'super_partner', $superPartner->id, '2026-09-01');

        $response = $this->actingAs($admin)->get(route('partner-invoices.index'));

        $response->assertOk()
            ->assertSee('Selecciona al menos un filtro')
            ->assertDontSee($invoice->invoice_number)
            ->assertDontSee('Facturas mensuales de super partners');

        $this->actingAs($admin)
            ->get(route('partner-invoices.index', ['month' => '2026-09']))
            ->assertOk()
            ->assertSee($invoice->invoice_number);
    }

    /** @test */
    public function administrator_can_filter_invoices_by_month_and_update_payment_status(): void
    {
        $admin = $this->createAdmin(['user_type' => 'admin']);
        [$superPartner, $partner] = $this->createPartnerNetwork();
        [$otherSuperPartner, $otherPartner] = $this->createPartnerNetwork('Dos');
        $septemberInvoice = $this->createInvoice('XC-SP-202609-000001', 'super_partner', $superPartner->id, '2026-09-01');
        $septemberPartnerInvoice = $this->createInvoice('XC-P-202609-000002', 'partner', $partner->id, '2026-09-01');
        $otherSuperPartnerInvoice = $this->createInvoice('XC-SP-202609-000003', 'super_partner', $otherSuperPartner->id, '2026-09-01');
        $this->createInvoice('XC-P-202609-000004', 'partner', $otherPartner->id, '2026-09-01');
        $octoberInvoice = $this->createInvoice('XC-P-202610-000002', 'partner', $partner->id, '2026-10-01');

        $response = $this->actingAs($admin)
            ->get(route('partner-invoices.index', ['month' => '2026-09']));

        $response->assertOk()
            ->assertSee($septemberInvoice->invoice_number)
            ->assertSee($superPartner->nombre)
            ->assertSee($septemberPartnerInvoice->invoice_number)
            ->assertSee($otherSuperPartnerInvoice->invoice_number)
            ->assertSee('Facturas mensuales de super partners')
            ->assertSee('Total mensual de partners por super partner')
            ->assertSee(route('partner-invoices.monthly-invoice', [
                'period' => '2026-09',
                'super_partner_id' => $superPartner->id,
            ]))
            ->assertDontSee($octoberInvoice->invoice_number);

        $this->actingAs($admin)
            ->get(route('partner-invoices.monthly-invoice', [
                'period' => '2026-09',
                'super_partner_id' => $superPartner->id,
            ]))
            ->assertOk();

        $filteredResponse = $this->actingAs($admin)->get(route('partner-invoices.index', [
            'month' => '2026-09',
            'super_partner_id' => $superPartner->id,
            'invoice_type' => 'partner',
        ]));

        $filteredResponse->assertOk()
            ->assertSee($septemberPartnerInvoice->invoice_number)
            ->assertDontSee($septemberInvoice->invoice_number)
            ->assertDontSee($otherSuperPartnerInvoice->invoice_number)
            ->assertDontSee($otherSuperPartner->nombre);

        $this->actingAs($admin)
            ->patch(route('partner-invoices.payment-status', $septemberInvoice), [
                'is_paid' => 1,
                'month' => '2026-09',
                'super_partner_id' => $superPartner->id,
                'invoice_type' => 'super_partner',
            ])
            ->assertRedirect(route('partner-invoices.index', [
                'month' => '2026-09',
                'super_partner_id' => $superPartner->id,
                'invoice_type' => 'super_partner',
            ]));

        $this->assertTrue($septemberInvoice->fresh()->is_paid);
        $this->assertFalse($octoberInvoice->fresh()->is_paid);
    }

    /** @test */
    public function administrator_can_search_invoices_by_number(): void
    {
        $admin = $this->createAdmin(['user_type' => 'admin']);
        [$superPartner] = $this->createPartnerNetwork();
        $matchingInvoice = $this->createInvoice('XC-SP-202609-123456', 'super_partner', $superPartner->id, '2026-09-01');
        $otherInvoice = $this->createInvoice('XC-SP-202609-654321', 'super_partner', $superPartner->id, '2026-09-01');

        $this->actingAs($admin)
            ->get(route('partner-invoices.index', ['invoice_number' => '123456']))
            ->assertOk()
            ->assertSee($matchingInvoice->invoice_number)
            ->assertDontSee($otherInvoice->invoice_number);
    }

    /** @test */
    public function monthly_commission_report_lists_a_purchased_esim_only_once(): void
    {
        [$superPartner, $partner] = $this->createPartnerNetwork();
        $superPartner->update(['sale_commission_usa_ca_eu_pct' => 25]);
        $partnerInvoice = $this->createInvoice('XC-P-202610-000001', 'partner', $partner->id, '2026-10-01');
        $superPartnerInvoice = $this->createInvoice('XC-SP-202610-000002', 'super_partner', $superPartner->id, '2026-10-01');
        $commissionItem = [
            'plan_name' => 'Spain_3GB_30DAYS',
            'data_amount' => 3,
            'duration_days' => 30,
            'country_code' => 'ES',
            'commission_percentage' => 25,
            'commission_region' => 'Europa y EE. UU.',
            'quantity' => 1,
            'unit_price' => 25,
            'total' => 25,
        ];
        PartnerCommissionNote::create([
            'partner_invoice_id' => $partnerInvoice->id,
            'note_number' => 'XC-NC-P-202610-000001',
            'amount' => 25,
            'applied_amount' => 25,
            'balance_amount' => 0,
            'items' => [$commissionItem],
            'issued_at' => '2026-10-31',
        ]);
        PartnerCommissionNote::create([
            'partner_invoice_id' => $superPartnerInvoice->id,
            'note_number' => 'XC-NC-SP-202610-000002',
            'amount' => 25,
            'applied_amount' => 25,
            'balance_amount' => 0,
            'items' => [$commissionItem],
            'issued_at' => '2026-10-31',
        ]);
        Transaction::create([
            'transaction_id' => 'PAID-ESIM-ONCE',
            'creation_time' => '2026-10-15 12:00:00',
            'beneficiario_id' => $partner->id,
            'super_partner_id' => $superPartner->id,
            'plan_name' => 'Spain_3GB_30DAYS',
            'data_amount' => 3,
            'duration_days' => 30,
            'purchase_amount' => 100,
            'beneficiary_commission_amount' => 0,
            'country_code' => 'ES',
        ]);

        $pdf = \Mockery::mock();
        $pdf->shouldReceive('loadView')
            ->twice()
            ->withArgs(function ($view, $data) {
                $this->assertContains($view, [
                    'partner-invoices.monthly-commission-report',
                    'partner-invoices.monthly-consolidated-invoice',
                ]);
                $this->assertSame(25.0, $data['earnedCommissions']);

                if ($view === 'partner-invoices.monthly-commission-report') {
                    $this->assertCount(1, $data['commissionRows']);
                    $this->assertSame(1, $data['commissionRows']->first()['quantity']);
                    $this->assertSame(25.0, $data['commissionRows']->first()['total']);
                }

                return true;
            })
            ->andReturnSelf();
        $pdf->shouldReceive('setPaper')->twice()->andReturnSelf();
        $pdf->shouldReceive('download')->twice()->andReturn(response('PDF'));
        app()->instance('dompdf.wrapper', $pdf);

        $this->actingAs($superPartner->user)
            ->get(route('partner-invoices.monthly-commission-report', '2026-10'))
            ->assertOk();

        $this->actingAs($superPartner->user)
            ->get(route('partner-invoices.monthly-invoice', '2026-10'))
            ->assertOk();
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

    private function createPartnerNetwork(string $suffix = 'Uno'): array
    {
        $superPartnerUser = User::factory()->create(['user_type' => 'super_partner']);
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner ' . $suffix,
            'descripcion' => 'Prueba',
            'codigo' => 'SP' . ($suffix === 'Uno' ? '000001' : '000002'),
            'user_id' => $superPartnerUser->id,
        ]);

        $partnerUser = User::factory()->create(['user_type' => 'beneficiario']);
        $partner = Beneficiario::create([
            'nombre' => 'Partner ' . $suffix,
            'descripcion' => 'Prueba',
            'codigo' => 'P' . ($suffix === 'Uno' ? '000001' : '000002'),
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
