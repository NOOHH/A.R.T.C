<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix quiz_questions table to ensure correct_answer has a default and all necessary columns exist.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Make quiz_title nullable if not already
            if (Schema::hasColumn('quiz_questions', 'quiz_title')) {
                DB::statement('ALTER TABLE quiz_questions MODIFY quiz_title VARCHAR(255) NULL DEFAULT NULL');
            }
            
            // Make correct_answer nullable with default empty string
            if (Schema::hasColumn('quiz_questions', 'correct_answer')) {
                DB::statement('ALTER TABLE quiz_questions MODIFY correct_answer TEXT NULL DEFAULT ""');
            }
            
            // Add question_order if it doesn't exist
            if (!Schema::hasColumn('quiz_questions', 'question_order')) {
                $table->integer('question_order')->nullable()->default(0)->after('question_type');
            }
            
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('quiz_questions', 'program_id')) {
                $table->integer('program_id')->nullable()->after('quiz_title');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'question_source')) {
                $table->enum('question_source', ['generated', 'manual', 'quizapi', 'gemini_ai'])->default('generated')->after('explanation');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'question_metadata')) {
                $table->json('question_metadata')->nullable()->after('question_source');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'points')) {
                $table->integer('points')->default(1)->after('question_metadata');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'source_file')) {
                $table->string('source_file')->nullable()->after('points');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('source_file');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'created_by_admin')) {
                $table->integer('created_by_admin')->nullable()->after('is_active');
            }
            
            if (!Schema::hasColumn('quiz_questions', 'created_by_professor')) {
                $table->integer('created_by_professor')->nullable()->after('created_by_admin');
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
        // Revert changes - be careful with data loss
    }
};
