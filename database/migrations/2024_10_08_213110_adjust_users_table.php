<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar campos faltantes
            $table->string('avatar')->nullable()->after('email');
            $table->string('address')->nullable()->after('avatar');
            $table->string('phone')->nullable()->after('address');          
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar campos agregados
            $table->dropColumn(['avatar', 'address', 'phone']);        
          
        });
    }
}