<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreeEsimCapacityToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedTinyInteger('free_esim_capacity')->nullable()->after('can_activate_free_esim');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('free_esim_capacity');
        });
    }
}