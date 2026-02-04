<?php

namespace Tests\Unit\Services;

use App\Models\App\Settings\PlanMargin;
use App\Services\App\Settings\PlanMarginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test the PlanMarginService price calculation logic
 */
class PlanMarginServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlanMarginService(new PlanMargin());
    }

    /** @test */
    public function it_calculates_final_price_with_30_percent_margin()
    {
        // Create a margin for 1GB plan with 30% margin
        PlanMargin::create([
            'plan_capacity' => '1',
            'margin_percentage' => 30.00,
            'is_active' => true,
        ]);

        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '1');

        // With 30% margin: 100 / (1 - 0.30) = 100 / 0.70 = 142.86
        $this->assertEquals(142.86, $finalPrice);
    }

    /** @test */
    public function it_calculates_final_price_with_25_percent_margin()
    {
        PlanMargin::create([
            'plan_capacity' => '3',
            'margin_percentage' => 25.00,
            'is_active' => true,
        ]);

        $cost = 80;
        $finalPrice = $this->service->calculateFinalPrice($cost, '3');

        // With 25% margin: 80 / (1 - 0.25) = 80 / 0.75 = 106.67
        $this->assertEquals(106.67, $finalPrice);
    }

    /** @test */
    public function it_returns_original_cost_when_no_margin_configured()
    {
        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '5');

        // No margin configured, should return original cost
        $this->assertEquals(100.00, $finalPrice);
    }

    /** @test */
    public function it_returns_original_cost_when_margin_is_zero()
    {
        PlanMargin::create([
            'plan_capacity' => '10',
            'margin_percentage' => 0.00,
            'is_active' => true,
        ]);

        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '10');

        $this->assertEquals(100.00, $finalPrice);
    }

    /** @test */
    public function it_handles_100_percent_margin_safely()
    {
        PlanMargin::create([
            'plan_capacity' => '20',
            'margin_percentage' => 100.00,
            'is_active' => true,
        ]);

        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '20');

        // Should return original cost to avoid division by zero
        $this->assertEquals(100.00, $finalPrice);
    }

    /** @test */
    public function it_updates_multiple_margins()
    {
        // Create initial margins
        PlanMargin::create(['plan_capacity' => '1', 'margin_percentage' => 30.00, 'is_active' => true]);
        PlanMargin::create(['plan_capacity' => '3', 'margin_percentage' => 30.00, 'is_active' => true]);

        // Update margins
        $data = [
            '1' => ['margin_percentage' => 25.00, 'is_active' => true],
            '3' => ['margin_percentage' => 35.00, 'is_active' => true],
        ];

        $result = $this->service->updateMargins($data);

        $this->assertTrue($result);
        $this->assertEquals(25.00, PlanMargin::where('plan_capacity', '1')->first()->margin_percentage);
        $this->assertEquals(35.00, PlanMargin::where('plan_capacity', '3')->first()->margin_percentage);
    }

    /** @test */
    public function it_validates_margin_percentage_is_between_0_and_100()
    {
        $this->expectException(\InvalidArgumentException::class);

        PlanMargin::create([
            'plan_capacity' => '50',
            'margin_percentage' => 150.00,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_validates_negative_margin_percentage()
    {
        $this->expectException(\InvalidArgumentException::class);

        PlanMargin::create([
            'plan_capacity' => '50',
            'margin_percentage' => -10.00,
            'is_active' => true,
        ]);
    }
}
