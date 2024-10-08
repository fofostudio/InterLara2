<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id(); // Crea un campo 'id' autoincremental
            $table->integer('number'); // Campo para 'number'
            $table->string('description'); // Campo para 'description'
            $table->string('address'); // Campo para 'address'
            $table->string('phone'); // Campo para 'phone'
            $table->timestamps(); // Crea campos 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points');
    }
}
