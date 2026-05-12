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
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->decimal('sale_commission_latam_pct', 5, 2)
                ->nullable()
                ->default(null)
                ->after('free_esim_rate')
                ->comment('Sale commission percentage for LATAM transactions');

            $table->decimal('sale_commission_usa_ca_eu_pct', 5, 2)
                ->nullable()
                ->default(null)
                ->after('sale_commission_latam_pct')
                ->comment('Sale commission percentage for USA/Canada/Europe transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn(['sale_commission_latam_pct', 'sale_commission_usa_ca_eu_pct']);
        });
    }
};
