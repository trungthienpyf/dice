<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToDiceRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dice_rows', function (Blueprint $table) {
            $table->integer('s1')->nullable();
            $table->integer('s2')->nullable();
            $table->integer('s3')->nullable();
            $table->integer('s4')->nullable();
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
            $table->dropColumn('s1');
            $table->dropColumn('s2');
            $table->dropColumn('s3');
            $table->dropColumn('s4');
        });
    }
}
