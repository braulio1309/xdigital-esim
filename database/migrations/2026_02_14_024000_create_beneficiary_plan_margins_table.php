<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryPlanMarginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_plan_margins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiario_id');
            $table->string('plan_capacity')->comment('Plan capacity: 1, 3, 5, 10, 20, 50 (in GB)');
            $table->decimal('margin_percentage', 5, 2)->comment('Profit margin percentage (e.g., 30.00 for 30%)');
            $table->boolean('is_active')->default(true)->comment('Whether this margin configuration is active');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('cascade');
            
            // Unique constraint to ensure one margin per beneficiary per plan
            $table->unique(['beneficiario_id', 'plan_capacity']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiary_plan_margins');
    }
}
