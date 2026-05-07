<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPercentageToSuperPartnerCountryPricesTable extends Migration
{
    public function up()
    {
        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('super_partner_country_prices', 'percentage')) {
                $table->decimal('percentage', 5, 2)->default(0)->comment('Percentage margin for this country (replaces general plan margins)')->after('plan_capacity');
            }
        });

        // Make price nullable using raw SQL to avoid requiring doctrine/dbal
        DB::statement('ALTER TABLE super_partner_country_prices MODIFY COLUMN price DECIMAL(10,2) NULL COMMENT \'Country-specific fixed price in USD (legacy, kept for backward compatibility)\'');
    }

    public function down()
    {
        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            if (Schema::hasColumn('super_partner_country_prices', 'percentage')) {
                $table->dropColumn('percentage');
            }
        });

        // Restore price as NOT NULL with default 0
        DB::statement('ALTER TABLE super_partner_country_prices MODIFY COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
}
