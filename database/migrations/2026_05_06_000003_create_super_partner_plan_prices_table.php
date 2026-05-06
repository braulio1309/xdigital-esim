<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperPartnerPlanPricesTable extends Migration
{
    public function up()
    {
        Schema::create('super_partner_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('super_partner_id');
            $table->string('plan_capacity')->comment('Plan capacity in GB: 1, 3, 5, 10');
            $table->decimal('price', 10, 2)->comment('Fixed manual price in USD to charge this super partner for a free eSIM activation');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('super_partner_id')->references('id')->on('super_partners')->onDelete('cascade');
            $table->unique(['super_partner_id', 'plan_capacity'], 'sppp_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('super_partner_plan_prices');
    }
}
