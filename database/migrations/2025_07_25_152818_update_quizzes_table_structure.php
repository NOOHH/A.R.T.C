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
        // Add missing columns to quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'module_id')) {
                $table->bigInteger('module_id')->unsigned()->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('quizzes', 'course_id')) {
                $table->bigInteger('course_id')->unsigned()->nullable()->after('module_id');
            }
            if (!Schema::hasColumn('quizzes', 'content_id')) {
                $table->bigInteger('content_id')->unsigned()->nullable()->after('course_id');
            }
            if (!Schema::hasColumn('quizzes', 'is_draft')) {
                $table->boolean('is_draft')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('quizzes', 'randomize_order')) {
                $table->boolean('randomize_order')->default(false)->after('is_draft');
            }
            if (!Schema::hasColumn('quizzes', 'tags')) {
                $table->json('tags')->nullable()->after('randomize_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['module_id', 'course_id', 'content_id', 'is_draft', 'randomize_order', 'tags']);
        });
    }
};
