<?php

namespace Tests\Unit\Services;

use App\Models\App\Settings\PlanMargin;
use App\Services\App\Settings\PlanMarginService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
    public function it_calculates_final_price_with_margin_correctly()
    {
        // Create a plan margin with 30%
        PlanMargin::create([
            'plan_capacity' => '1',
            'margin_percentage' => 30.00,
            'is_active' => true,
        ]);

        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '1');

        // Expected: 100 / (1 - 0.30) = 100 / 0.70 = 142.86 (rounded to 142.86)
        $this->assertEquals(142.86, $finalPrice);
    }

    /** @test */
    public function it_returns_original_cost_when_no_margin_is_configured()
    {
        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '1');

        // Should return original cost when no margin is configured
        $this->assertEquals($cost, $finalPrice);
    }

    /** @test */
    public function it_prevents_division_by_zero()
    {
        // Create a plan margin with 100% (edge case)
        PlanMargin::create([
            'plan_capacity' => '1',
            'margin_percentage' => 100.00,
            'is_active' => true,
        ]);

        $cost = 100;
        $finalPrice = $this->service->calculateFinalPrice($cost, '1');

        // Should return original cost to prevent division by zero
        $this->assertEquals($cost, $finalPrice);
    }

    /** @test */
    public function it_calculates_various_margins_correctly()
    {
        $testCases = [
            ['capacity' => '1', 'margin' => 25.00, 'cost' => 100, 'expected' => 133.33],
            ['capacity' => '3', 'margin' => 20.00, 'cost' => 50, 'expected' => 62.5],
            ['capacity' => '5', 'margin' => 15.00, 'cost' => 200, 'expected' => 235.29],
        ];

        foreach ($testCases as $case) {
            PlanMargin::create([
                'plan_capacity' => $case['capacity'],
                'margin_percentage' => $case['margin'],
                'is_active' => true,
            ]);

            $finalPrice = $this->service->calculateFinalPrice($case['cost'], $case['capacity']);
            $this->assertEquals($case['expected'], $finalPrice);
        }
    }

    /** @test */
    public function it_updates_margins_successfully()
    {
        $data = [
            '1' => ['margin_percentage' => 30.00, 'is_active' => true],
            '3' => ['margin_percentage' => 25.00, 'is_active' => true],
        ];

        $result = $this->service->updateMargins($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('plan_margins', [
            'plan_capacity' => '1',
            'margin_percentage' => 30.00,
        ]);
        $this->assertDatabaseHas('plan_margins', [
            'plan_capacity' => '3',
            'margin_percentage' => 25.00,
        ]);
    }

    /** @test */
    public function it_returns_formatted_margins()
    {
        PlanMargin::create([
            'plan_capacity' => '1',
            'margin_percentage' => 30.00,
            'is_active' => true,
        ]);

        $formatted = $this->service->getFormattedMargins();

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('1', $formatted);
        $this->assertEquals(30.00, $formatted['1']['margin_percentage']);
    }
}
