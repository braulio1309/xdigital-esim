<?php

namespace Database\Seeders\App;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planCapacities = ['1', '3', '5', '10', '20', '50'];
        $defaultMargin = 30.00; // 30% default margin

        foreach ($planCapacities as $capacity) {
            DB::table('plan_margins')->updateOrInsert(
                ['plan_capacity' => $capacity],
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
