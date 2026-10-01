<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type', 20);
            $table->unsignedBigInteger('owner_id');
            $table->string('invoice_number', 40)->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->json('items');
            $table->unsignedInteger('transactions_count')->default(0);
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['owner_type', 'owner_id', 'period_start'], 'partner_invoices_owner_period_unique');
            $table->index(['period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_invoices');
    }
};