<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuperPartnerAndReferencePriceToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('super_partner_id')->nullable()->after('beneficiario_id');
            $table->decimal('reference_purchase_amount', 10, 2)->nullable()->after('purchase_amount');

            $table->foreign('super_partner_id')
                ->references('id')
                ->on('super_partners')
                ->onDelete('set null');
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
            $table->dropForeign(['super_partner_id']);
            $table->dropColumn(['super_partner_id', 'reference_purchase_amount']);
        });
    }
}