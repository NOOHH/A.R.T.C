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
        Schema::table('education_levels', function (Blueprint $table) {
            // Add missing columns for plan types
            $table->boolean('available_full_plan')->default(true)->after('level_order');
            $table->boolean('available_modular_plan')->default(true)->after('available_full_plan');
            $table->text('level_description')->nullable()->after('level_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('education_levels', function (Blueprint $table) {
            $table->dropColumn(['available_full_plan', 'available_modular_plan', 'level_description']);
        });
    }
};
