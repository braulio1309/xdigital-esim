<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserSubTypeAndBeneficiarioIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_sub_type')->nullable()->after('super_partner_id');
            $table->unsignedBigInteger('beneficiario_id')->nullable()->after('user_sub_type');
            $table->foreign('beneficiario_id')->references('id')->on('beneficiarios')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['beneficiario_id']);
            $table->dropColumn(['user_sub_type', 'beneficiario_id']);
        });
    }
}
