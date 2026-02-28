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
        // Add status column to payment_histories (active / anulada)
        Schema::table('payment_histories', function (Blueprint $table) {
            $table->string('status')->default('active')->after('notes')
                ->comment('active = normal; anulada = voided/cancelled');
            $table->timestamp('cancelled_at')->nullable()->after('status');
        });

        // Link each transaction back to its payment history record
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_history_id')->nullable()->after('paid_at')
                ->comment('FK to payment_histories when this transaction was marked as paid');
            $table->foreign('payment_history_id')->references('id')->on('payment_histories')->onDelete('set null');
            $table->index('payment_history_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_history_id']);
            $table->dropIndex(['payment_history_id']);
            $table->dropColumn('payment_history_id');
        });

        Schema::table('payment_histories', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancelled_at']);
        });
    }
};
