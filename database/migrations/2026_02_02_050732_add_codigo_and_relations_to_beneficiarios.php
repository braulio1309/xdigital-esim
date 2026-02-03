<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigoAndRelationsToBeneficiarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add codigo field to beneficiarios table
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->string('codigo', 8)->nullable()->after('descripcion');
        });

        // Add beneficiario_id foreign key to clientes table
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiario_id')->nullable()->after('email');
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['beneficiario_id']);
            $table->dropColumn('beneficiario_id');
        });

        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
}
