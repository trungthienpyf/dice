<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToDiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dice_tables', function (Blueprint $table) {
            $table->integer('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('td')->default(0);
            $table->integer('cc')->default(0);
            $table->integer('ts')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dice_tables', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('name');
            $table->dropColumn('td');
            $table->dropColumn('cc');
            $table->dropColumn('ts');
        });
    }
}
