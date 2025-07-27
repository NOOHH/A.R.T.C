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
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('is_draft');
            }
            if (!Schema::hasColumn('quizzes', 'quiz_description')) {
                $table->text('quiz_description')->nullable()->after('quiz_title');
            }
            if (!Schema::hasColumn('quizzes', 'modules_id')) {
                $table->unsignedBigInteger('modules_id')->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('quizzes', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('modules_id');
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
            $table->dropColumn(['status', 'quiz_description', 'modules_id', 'course_id']);
        });
    }
};
