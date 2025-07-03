<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiceTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dice_tables', function (Blueprint $table) {
            $table->id();
            $table->date('date_check')->nullable();
            $table->integer('config_id')->nullable();

            $table->boolean('is_turn')->default(false);

            $table->boolean('is_end')->default(false);

            $table->string('u1')->nullable();
            $table->string('u2')->nullable();
            $table->string('u3')->nullable();
            $table->string('u4')->nullable();


            $table->integer('td1')->default(0);
            $table->integer('td2')->default(0);
            $table->integer('td3')->default(0);
            $table->integer('td4')->default(0);

            $table->integer('cc1')->default(0);
            $table->integer('cc2')->default(0);
            $table->integer('cc3')->default(0);
            $table->integer('cc4')->default(0);

            $table->integer('tc1')->default(0);
            $table->integer('tc2')->default(0);
            $table->integer('tc3')->default(0);
            $table->integer('tc4')->default(0);

            $table->integer('tt1')->default(0);
            $table->integer('tt2')->default(0);
            $table->integer('tt3')->default(0);
            $table->integer('tt4')->default(0);

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
        Schema::dropIfExists('dice_tables');
    }
}
