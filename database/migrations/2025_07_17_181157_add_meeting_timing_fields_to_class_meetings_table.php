<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_meetings', function (Blueprint $table) {
            $table->datetime('actual_start_time')->nullable()->after('url_visibility_minutes_before');
            $table->datetime('actual_end_time')->nullable()->after('actual_start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_meetings', function (Blueprint $table) {
            $table->dropColumn(['actual_start_time', 'actual_end_time']);
        });
    }
};
