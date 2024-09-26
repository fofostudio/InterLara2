<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecondExcelDataTable extends Migration
{
    public function up()
    {
        Schema::create('second_excel_data', function (Blueprint $table) {
            $table->id();
            $table->string('ADM_NumeroGuia')->unique();
            $table->string('ADM_CreadoPor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('second_excel_data');
    }
}
