<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperPartnerPlanMarginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_partner_plan_margins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('super_partner_id');
            $table->string('plan_capacity'); // e.g. 3, 5, 10 (GB)
            $table->decimal('margin_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('super_partner_id')
                ->references('id')
                ->on('super_partners')
                ->onDelete('cascade');

            $table->unique(['super_partner_id', 'plan_capacity'], 'sp_plan_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('super_partner_plan_margins');
    }
}
