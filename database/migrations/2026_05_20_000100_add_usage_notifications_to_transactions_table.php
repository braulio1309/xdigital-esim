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
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('usage_75_notified_at')
                ->nullable()
                ->after('terminated_at')
                ->comment('Timestamp for first 75% usage notification sent for this transaction');

            $table->timestamp('usage_90_notified_at')
                ->nullable()
                ->after('usage_75_notified_at')
                ->comment('Timestamp for first 90% usage notification sent for this transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'usage_75_notified_at',
                'usage_90_notified_at',
            ]);
        });
    }
};
