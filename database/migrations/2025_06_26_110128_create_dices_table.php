<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('u1')->nullable();
            $table->string('u2')->nullable();
            $table->string('u3')->nullable();
            $table->string('u4')->nullable();
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
        Schema::dropIfExists('dices');
    }
}
