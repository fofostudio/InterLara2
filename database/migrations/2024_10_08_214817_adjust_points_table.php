<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustPointsTable extends Migration
{
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->integer('maxusers')->nullable()->after('phone');
            $table->date('dateStart')->nullable()->after('maxusers');
            $table->date('dateLimit')->nullable()->after('dateStart');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('dateLimit');
            $table->unsignedBigInteger('resp_user')->nullable()->after('status');
            $table->foreign('resp_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['resp_user']);
            $table->dropColumn(['maxusers', 'dateStart', 'dateLimit', 'status', 'resp_user']);
        });
    }
}