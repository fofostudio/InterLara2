<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashClosingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_closings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('total_sales', 12, 2);
            $table->decimal('expenses', 12, 2);
            $table->decimal('cash', 12, 2);
            $table->decimal('cancelled_guides', 12, 2);
            $table->decimal('debt', 12, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_closings');
    }
}
