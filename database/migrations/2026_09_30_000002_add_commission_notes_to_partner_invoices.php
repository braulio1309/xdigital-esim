<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_invoices', function (Blueprint $table) {
            $table->decimal('earned_commissions', 10, 2)->default(0)->after('paid_amount');
            $table->decimal('applied_commissions', 10, 2)->default(0)->after('earned_commissions');
        });

        Schema::create('partner_commission_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_invoice_id')->unique();
            $table->string('note_number', 40)->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('applied_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->json('items');
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->foreign('partner_invoice_id')->references('id')->on('partner_invoices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_commission_notes');

        Schema::table('partner_invoices', function (Blueprint $table) {
            $table->dropColumn(['earned_commissions', 'applied_commissions']);
        });
    }
};