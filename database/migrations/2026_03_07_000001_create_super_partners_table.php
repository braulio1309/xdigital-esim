<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_partners', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('logo')->nullable();
            $table->string('codigo', 8)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->unsignedInteger('total_sales')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('super_partners');
    }
}
