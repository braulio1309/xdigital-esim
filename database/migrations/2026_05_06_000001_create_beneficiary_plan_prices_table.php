<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryPlanPricesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiario_id');
            $table->string('plan_capacity')->comment('Plan capacity in GB: 1, 3, 5, 10');
            $table->decimal('price', 10, 2)->comment('Fixed manual price in USD to charge this partner for a free eSIM activation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('cascade');
            $table->unique(['beneficiario_id', 'plan_capacity'], 'bpp_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('beneficiary_plan_prices');
    }
}
