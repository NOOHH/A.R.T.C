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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('access_period_days')->nullable()->after('module_count');
            $table->integer('access_period_months')->nullable()->after('access_period_days');
            $table->integer('access_period_years')->nullable()->after('access_period_months');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['access_period_days', 'access_period_months', 'access_period_years']);
        });
    }
}; 