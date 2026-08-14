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
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dateTime('start_date_time')->after('promotion_id');
            $table->dateTime('end_date_time')->after('start_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
        });
    }
};
