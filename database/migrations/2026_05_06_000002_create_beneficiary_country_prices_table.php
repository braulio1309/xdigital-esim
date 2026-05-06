<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryCountryPricesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_country_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiario_id');
            $table->string('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
            $table->string('plan_capacity')->comment('Plan capacity in GB: 1, 3, 5, 10');
            $table->decimal('price', 10, 2)->comment('Country-specific fixed price in USD (overrides general plan price)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('cascade');
            $table->unique(['beneficiario_id', 'country_code', 'plan_capacity'], 'bcp_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('beneficiary_country_prices');
    }
}
