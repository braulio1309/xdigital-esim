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
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiario_id');
            $table->string('reference')->nullable();
            $table->date('payment_date');
            $table->string('support_path')->nullable()->comment('Path to the uploaded payment support file');
            $table->string('support_original_name')->nullable()->comment('Original file name of the payment support');
            $table->decimal('amount', 10, 2)->default(0)->comment('Total amount paid');
            $table->integer('transactions_count')->default(0)->comment('Number of transactions marked as paid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('cascade');
            $table->index('beneficiario_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_histories');
    }
};
