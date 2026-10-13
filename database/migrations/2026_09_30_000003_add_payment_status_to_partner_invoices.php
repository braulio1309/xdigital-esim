<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_invoices', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('due_amount');
        });

        DB::table('partner_invoices')
            ->where('due_amount', '<=', 0)
            ->update(['is_paid' => true]);
    }

    public function down(): void
    {
        Schema::table('partner_invoices', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
};
