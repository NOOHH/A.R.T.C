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
        // Remove JotForm fields from quizzes table and add new features
        Schema::table('quizzes', function (Blueprint $table) {
            // Remove JotForm fields if they exist
            if (Schema::hasColumn('quizzes', 'jotform_id')) {
                $table->dropColumn('jotform_id');
            }
            if (Schema::hasColumn('quizzes', 'jotform_url')) {
                $table->dropColumn('jotform_url');
            }
            
            // Add new quiz features only if they don't exist
            if (!Schema::hasColumn('quizzes', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('is_active');
            }
            if (!Schema::hasColumn('quizzes', 'allow_retakes')) {
                $table->boolean('allow_retakes')->default(false)->after('status');
            }
            if (!Schema::hasColumn('quizzes', 'instant_feedback')) {
                $table->boolean('instant_feedback')->default(false)->after('allow_retakes');
            }
            if (!Schema::hasColumn('quizzes', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(true)->after('instant_feedback');
            }
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->nullable()->after('show_correct_answers');
            }
            if (!Schema::hasColumn('quizzes', 'randomize_order')) {
                $table->boolean('randomize_order')->default(false)->after('max_attempts');
            }
            if (!Schema::hasColumn('quizzes', 'quiz_description')) {
                $table->text('quiz_description')->nullable()->after('instructions');
            }
        });

        // Create quiz_drafts table for better organization (only if it doesn't exist)
        if (!Schema::hasTable('quiz_drafts')) {
            Schema::create('quiz_drafts', function (Blueprint $table) {
                $table->id('draft_id');
                $table->unsignedInteger('professor_id'); // Changed to match professors table
                $table->unsignedBigInteger('program_id')->nullable();
                $table->unsignedBigInteger('module_id')->nullable();
                $table->unsignedBigInteger('course_id')->nullable();
                $table->unsignedBigInteger('content_id')->nullable();
                $table->string('quiz_title');
                $table->text('quiz_description')->nullable();
                $table->text('instructions')->nullable();
                $table->integer('total_questions')->default(10);
                $table->integer('time_limit')->nullable(); // in minutes
                $table->string('document_path')->nullable();
                $table->boolean('allow_retakes')->default(false);
                $table->boolean('instant_feedback')->default(false);
                $table->boolean('show_correct_answers')->default(true);
                $table->integer('max_attempts')->nullable();
                $table->boolean('randomize_order')->default(false);
                $table->json('tags')->nullable();
                $table->text('quiz_source')->nullable(); // QuizAPI or Document
                $table->json('quiz_settings')->nullable(); // Additional settings
                $table->timestamps();
            });
        }

        // Create quiz_attempts table (only if it doesn't exist)
        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id('attempt_id');
                $table->unsignedBigInteger('quiz_id');
                $table->unsignedInteger('student_id'); // Changed to match students table
                $table->json('answers'); // Store student answers
                $table->decimal('score', 5, 2)->nullable();
                $table->integer('total_questions');
                $table->integer('correct_answers')->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->integer('time_taken')->nullable(); // in seconds
                $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
                $table->timestamps();
                
                // Indexes
                $table->index(['quiz_id', 'student_id']);
                $table->index('status');
            });
        }

        // Enhance quiz_questions table
        Schema::table('quiz_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('correct_answer');
            }
            if (!Schema::hasColumn('quiz_questions', 'question_source')) {
                $table->enum('question_source', ['generated', 'manual', 'quizapi'])->default('generated')->after('explanation');
            }
            if (!Schema::hasColumn('quiz_questions', 'question_metadata')) {
                $table->json('question_metadata')->nullable()->after('question_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore JotForm fields
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('jotform_id')->nullable();
            $table->string('jotform_url')->nullable();
            
            // Remove new fields
            $table->dropColumn([
                'status', 
                'allow_retakes', 
                'instant_feedback', 
                'show_correct_answers',
                'max_attempts',
                'quiz_description'
            ]);
        });

        // Drop new tables
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_drafts');
        
        // Remove quiz_questions enhancements
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn(['explanation', 'question_source', 'question_metadata']);
        });
    }
};
