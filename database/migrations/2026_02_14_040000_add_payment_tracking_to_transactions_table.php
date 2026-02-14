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
            $table->boolean('is_paid')->default(false)->after('currency')->comment('Indicates if the beneficiary has been paid for this transaction');
            $table->timestamp('paid_at')->nullable()->after('is_paid')->comment('Date when the beneficiary was paid');
            
            // Add indexes for better filtering performance
            $table->index('is_paid');
            $table->index(['is_paid', 'purchase_amount']);
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
            $table->dropIndex(['transactions_is_paid_index']);
            $table->dropIndex(['transactions_is_paid_purchase_amount_index']);
            $table->dropColumn(['is_paid', 'paid_at']);
        });
    }
};
