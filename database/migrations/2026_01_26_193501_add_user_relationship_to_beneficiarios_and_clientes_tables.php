<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRelationshipToBeneficiariosAndClientesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add user_id to beneficiarios table
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add columns for commission tracking
            $table->decimal('commission_percentage', 5, 2)->default(0.00)->after('descripcion');
            $table->decimal('total_earnings', 10, 2)->default(0.00)->after('commission_percentage');
            $table->integer('total_sales')->default(0)->after('total_earnings');
        });

        // Add user_id to clientes table
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Add user_type column to users table to differentiate user types
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('admin')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'commission_percentage', 'total_earnings', 'total_sales']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
}
