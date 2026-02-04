<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds default profit margins (30%) for all eSIM plan capacities.
     *
     * @return void
     */
    public function run()
    {
        $plans = ['1', '3', '5', '10', '20', '50']; // GB capacities
        $defaultMargin = 30.00; // 30% default profit margin

        foreach ($plans as $planCapacity) {
            DB::table('plan_margins')->updateOrInsert(
                ['plan_capacity' => $planCapacity],
                [
                    'margin_percentage' => $defaultMargin,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
