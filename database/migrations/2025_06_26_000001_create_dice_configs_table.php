<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dice_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('td')->default(0);
            $table->integer('cc')->default(0);
            $table->integer('ts')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dice_configs');
    }
};
