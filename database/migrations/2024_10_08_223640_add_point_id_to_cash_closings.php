<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->unsignedBigInteger('point_id')->after('user_id');
            $table->foreign('point_id')->references('id')->on('points')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cash_closings', function (Blueprint $table) {
            $table->dropForeign(['point_id']);
            $table->dropColumn('point_id');
        });
    }
};
