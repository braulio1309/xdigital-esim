<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreeEsimRateToSuperPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('super_partners', function (Blueprint $table) {
            $table->decimal('free_esim_rate', 5, 2)
                ->default(0.85)
                ->after('total_sales')
                ->comment('Monto a cobrar por cada eSIM gratuita (1GB) asociada a la red del super partner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('super_partners', function (Blueprint $table) {
            $table->dropColumn('free_esim_rate');
        });
    }
}
