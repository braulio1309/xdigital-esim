<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('plan_name')->nullable()->after('cliente_id');
            $table->decimal('data_amount', 10, 2)->nullable()->after('plan_name')->comment('Amount of data in GB');
            $table->integer('duration_days')->nullable()->after('data_amount')->comment('Duration in days');
            $table->decimal('purchase_amount', 10, 2)->nullable()->after('duration_days')->comment('Purchase amount');
            $table->string('currency', 3)->default('USD')->after('purchase_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['plan_name', 'data_amount', 'duration_days', 'purchase_amount', 'currency']);
        });
    }
};
