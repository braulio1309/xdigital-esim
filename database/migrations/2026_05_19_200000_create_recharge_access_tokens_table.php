<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recharge_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('beneficiario_id')->nullable();
            $table->unsignedBigInteger('super_partner_id')->nullable();
            $table->string('token_hash', 64)->unique();
            $table->string('purpose', 40)->default('recharge');
            $table->string('country_code', 2)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['purpose', 'transaction_id']);
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('set null');
            $table->foreign('super_partner_id')->references('id')->on('super_partners')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recharge_access_tokens');
    }
};
