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
        $tables = ['guides', 'debts', 'first_excel_data', 'second_excel_data'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('point_id')->nullable()->after('id');
                $table->foreign('point_id')->references('id')->on('points')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        $tables = ['guides', 'debts', 'first_excel_data', 'second_excel_data'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['point_id']);
                $table->dropColumn('point_id');
            });
        }
    }
};
