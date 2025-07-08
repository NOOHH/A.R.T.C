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
        // First, let's create the quizzes table without foreign keys
        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->id('quiz_id');
                $table->unsignedBigInteger('professor_id');
                $table->unsignedBigInteger('program_id');
                $table->string('quiz_title');
                $table->text('instructions')->nullable();
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->integer('total_questions')->default(10);
                $table->integer('time_limit')->default(60); // in minutes
                $table->string('document_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create deadlines table if it doesn't exist
        if (!Schema::hasTable('deadlines')) {
            Schema::create('deadlines', function (Blueprint $table) {
                $table->id('deadline_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('program_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['assignment', 'quiz', 'activity', 'exam'])->default('assignment');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->datetime('due_date');
                $table->enum('status', ['pending', 'completed', 'overdue'])->default('pending');
                $table->timestamps();
            });
        }

        // Create announcements table if it doesn't exist
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id('announcement_id');
                $table->unsignedBigInteger('professor_id');
                $table->unsignedBigInteger('program_id');
                $table->string('title');
                $table->text('content');
                $table->enum('type', ['general', 'video', 'assignment', 'quiz'])->default('general');
                $table->string('video_link')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create assignments table if it doesn't exist
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id('assignment_id');
                $table->unsignedBigInteger('professor_id');
                $table->unsignedBigInteger('program_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->integer('max_points')->default(100);
                $table->datetime('due_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create activities table if it doesn't exist
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id('activity_id');
                $table->unsignedBigInteger('professor_id');
                $table->unsignedBigInteger('program_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->integer('max_points')->default(100);
                $table->datetime('due_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('quizzes');
    }
};
