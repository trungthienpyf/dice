<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('super_id')->nullable()->after('id');
            $table->unsignedBigInteger('master_id')->nullable()->after('id');
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('super_id');
            $table->dropColumn('master_id');
            $table->dropColumn('admin_id');
            $table->dropColumn('user_id');
        });
    }
};
