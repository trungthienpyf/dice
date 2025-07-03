<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigColumnToDiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dices', function (Blueprint $table) {
            $table->integer('td')->default(500);
            $table->integer('cc')->default(18);
            $table->integer('ts')->default(250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dices', function (Blueprint $table) {
            $table->dropColumn('td');
            $table->dropColumn('cc');
            $table->dropColumn('ts');

        });
    }
}
