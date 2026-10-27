<?php

namespace Tests\Unit\Models;

use App\Models\App\Beneficiario\Beneficiario;
use App\Models\App\SuperPartner\SuperPartner;
use App\Models\App\Transaction\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRegionalCommissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function large_plan_regional_commissions_are_awarded_to_the_super_partner(): void
    {
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner',
            'codigo' => 'SP000001',
            'sale_commission_latam_pct' => 5,
            'sale_commission_usa_ca_eu_pct' => 7,
        ]);
        $partner = Beneficiario::create([
            'nombre' => 'Partner',
            'descripcion' => 'Test partner',
            'codigo' => 'P000001',
            'super_partner_id' => $superPartner->id,
            'sale_commission_latam_pct' => 10,
            'sale_commission_usa_ca_eu_pct' => 12,
        ]);

        $europeCommissions = Transaction::calculateSaleCommissions(100, 'DE', $partner->id, $superPartner->id, 3);
        $latamCommissions = Transaction::calculateSaleCommissions(100, 'CO', $partner->id, $superPartner->id, 3);

        $this->assertSame(0.0, $europeCommissions['partner_sale_commission_amount']);
        $this->assertSame(25.0, $europeCommissions['super_partner_sale_commission_amount']);
        $this->assertSame(0.0, $latamCommissions['partner_sale_commission_amount']);
        $this->assertSame(15.0, $latamCommissions['super_partner_sale_commission_amount']);
    }

    /** @test */
    public function small_plans_keep_the_configured_commissions(): void
    {
        $superPartner = SuperPartner::create([
            'nombre' => 'Super Partner',
            'codigo' => 'SP000001',
            'sale_commission_latam_pct' => 5,
            'sale_commission_usa_ca_eu_pct' => 7,
        ]);
        $partner = Beneficiario::create([
            'nombre' => 'Partner',
            'descripcion' => 'Test partner',
            'codigo' => 'P000001',
            'super_partner_id' => $superPartner->id,
            'sale_commission_latam_pct' => 10,
            'sale_commission_usa_ca_eu_pct' => 12,
        ]);

        $commissions = Transaction::calculateSaleCommissions(100, 'DE', $partner->id, $superPartner->id, 1);

        $this->assertSame(12.0, $commissions['partner_sale_commission_amount']);
        $this->assertSame(7.0, $commissions['super_partner_sale_commission_amount']);
    }

    /** @test */
    public function regional_rates_apply_only_to_plans_above_one_gigabyte(): void
    {
        $this->assertSame(25.0, Transaction::getLargePlanRegionalPercentage('US', 1.5));
        $this->assertSame(25.0, Transaction::getLargePlanRegionalPercentage('ES', 2));
        $this->assertSame(15.0, Transaction::getLargePlanRegionalPercentage('CO', 2));
        $this->assertNull(Transaction::getLargePlanRegionalPercentage('CA', 2));
        $this->assertNull(Transaction::getLargePlanRegionalPercentage('DE', 1));
    }

    /** @test */
    public function free_large_plans_apply_the_regional_discount_without_generating_sale_commissions(): void
    {
        $transaction = Transaction::create([
            'transaction_id' => 'FREE-DISCOUNT-DE',
            'data_amount' => 3,
            'purchase_amount' => 0,
            'reference_purchase_amount' => 100,
            'beneficiary_commission_amount' => 100,
            'country_code' => 'DE',
        ]);

        $this->assertSame('75.00', $transaction->beneficiary_commission_amount);
        $this->assertSame('100.00', $transaction->reference_purchase_amount);
        $this->assertSame(0.0, (float) $transaction->partner_sale_commission_amount);
        $this->assertSame(0.0, (float) $transaction->super_partner_sale_commission_amount);

        $latamTransaction = Transaction::create([
            'transaction_id' => 'FREE-DISCOUNT-CO',
            'data_amount' => 3,
            'purchase_amount' => 0,
            'reference_purchase_amount' => 100,
            'beneficiary_commission_amount' => 100,
            'country_code' => 'CO',
        ]);

        $this->assertSame('85.00', $latamTransaction->beneficiary_commission_amount);
        $this->assertSame(0.0, (float) $latamTransaction->partner_sale_commission_amount);
        $this->assertSame(0.0, (float) $latamTransaction->super_partner_sale_commission_amount);
    }
}
