<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanMarginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_margins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plan_capacity')->unique()->comment('Plan capacity in GB: 1, 3, 5, 10, 20, 50');
            $table->decimal('margin_percentage', 5, 2)->comment('Profit margin percentage (0-100)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_margins');
    }
}
