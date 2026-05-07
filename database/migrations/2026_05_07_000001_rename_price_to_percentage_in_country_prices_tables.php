<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePriceToPercentageInCountryPricesTables extends Migration
{
    public function up()
    {
        Schema::table('beneficiary_country_prices', function (Blueprint $table) {
            $table->renameColumn('price', 'percentage');
        });

        Schema::table('beneficiary_country_prices', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)
                ->comment('Profit margin percentage for this country+capacity (e.g., 30.00 for 30%)')
                ->change();
        });

        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->renameColumn('price', 'percentage');
        });

        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)
                ->comment('Profit margin percentage for this country+capacity (e.g., 30.00 for 30%)')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('beneficiary_country_prices', function (Blueprint $table) {
            $table->renameColumn('percentage', 'price');
        });

        Schema::table('beneficiary_country_prices', function (Blueprint $table) {
            $table->decimal('price', 10, 2)
                ->comment('Country-specific fixed price in USD (overrides general plan price)')
                ->change();
        });

        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->renameColumn('percentage', 'price');
        });

        Schema::table('super_partner_country_prices', function (Blueprint $table) {
            $table->decimal('price', 10, 2)
                ->comment('Country-specific fixed price in USD (overrides general plan price)')
                ->change();
        });
    }
}
