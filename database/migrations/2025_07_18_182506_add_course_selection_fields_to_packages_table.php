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
            $table->enum('selection_mode', ['modules', 'courses'])->default('modules')->after('selection_type');
            $table->integer('course_count')->nullable()->after('module_count');
            $table->integer('min_courses')->nullable()->after('course_count');
            $table->integer('max_courses')->nullable()->after('min_courses');
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
            $table->dropColumn(['selection_mode', 'course_count', 'min_courses', 'max_courses']);
        });
    }
};
