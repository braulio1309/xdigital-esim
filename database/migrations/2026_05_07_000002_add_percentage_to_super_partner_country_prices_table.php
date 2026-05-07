<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentageToSuperPartnerCountryPricesTable extends Migration
{
    public function up()
    {
        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->default(0)->comment('Percentage margin for this country (replaces general plan margins)')->after('plan_capacity');
            $table->decimal('price', 10, 2)->nullable()->comment('Country-specific fixed price in USD (legacy, kept for backward compatibility)')->change();
        });
    }

    public function down()
    {
        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->dropColumn('percentage');
            $table->decimal('price', 10, 2)->nullable(false)->change();
        });
    }
}
