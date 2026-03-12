<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreeEsimRateToBeneficiariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->decimal('free_esim_rate', 5, 2)
                ->default(0.85)
                ->after('total_sales')
                ->comment('Monto a cobrar por cada eSIM gratuita (1GB)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn('free_esim_rate');
        });
    }
}
