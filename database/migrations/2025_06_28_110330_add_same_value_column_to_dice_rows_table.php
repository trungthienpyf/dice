<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSameValueColumnToDiceRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dice_rows', function (Blueprint $table) {
            $table->integer('sv1')->nullable();
            $table->integer('sv2')->nullable();
            $table->integer('sv3')->nullable();
            $table->integer('sv4')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dice_rows', function (Blueprint $table) {
            $table->dropColumn('sv1');
            $table->dropColumn('sv2');
            $table->dropColumn('sv3');
            $table->dropColumn('sv4');
        });
    }
}
