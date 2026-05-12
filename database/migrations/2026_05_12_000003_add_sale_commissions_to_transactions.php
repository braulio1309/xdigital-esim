<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('country_code', 2)
                ->nullable()
                ->after('currency')
                ->comment('ISO 3166-1 alpha-2 country code for the eSIM destination');

            $table->decimal('partner_sale_commission_amount', 10, 2)
                ->nullable()
                ->after('country_code')
                ->comment('Sale commission amount earned by the beneficiario partner for this transaction');

            $table->decimal('super_partner_sale_commission_amount', 10, 2)
                ->nullable()
                ->after('partner_sale_commission_amount')
                ->comment('Sale commission amount earned by the super partner for this transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'country_code',
                'partner_sale_commission_amount',
                'super_partner_sale_commission_amount',
            ]);
        });
    }
};
