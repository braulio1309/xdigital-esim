<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanionOfClienteIdToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('companion_of_cliente_id')->nullable()->after('cliente_id');
            $table->foreign('companion_of_cliente_id')->references('id')->on('clientes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['companion_of_cliente_id']);
            $table->dropColumn('companion_of_cliente_id');
        });
    }
}
