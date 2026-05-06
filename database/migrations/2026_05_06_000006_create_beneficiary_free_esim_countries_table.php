<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryFreeEsimCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiary_free_esim_countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiario_id');
            $table->string('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
            $table->boolean('is_active')->default(true)->comment('Whether free eSIM activation is enabled for this country');
            $table->decimal('price', 10, 2)->nullable()->comment('Amount charged to the beneficiary when a client activates a free eSIM in this country. Null uses default rate.');
            $table->string('plan_capacity', 10)->default('1')->comment('Plan capacity in GB to activate for free (1, 3, 5, 10)');
            $table->timestamps();

            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('cascade');
            $table->unique(['beneficiario_id', 'country_code'], 'bfec_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('beneficiary_free_esim_countries');
    }
}
