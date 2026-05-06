<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiPriceToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('api_price', 10, 2)->nullable()->after('purchase_amount')
                ->comment('Price returned by the Nomad/eSIMfx API for this order (what we owe to the API provider)');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('api_price');
        });
    }
}
