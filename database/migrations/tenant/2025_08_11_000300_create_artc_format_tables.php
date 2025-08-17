<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Programs table
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->text('program_description')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->unsignedBigInteger('director_id')->nullable();
            $table->timestamps();
        });

        // Directors table
        if (!Schema::hasTable('directors')) {
            Schema::create('directors', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->string('department')->nullable();
                $table->string('avatar')->nullable();
                $table->boolean('is_archived')->default(false);
                $table->boolean('all_program_access')->default(false);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Professors table (create or extend if already exists)
        if (!Schema::hasTable('professors')) {
            Schema::create('professors', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->text('bio')->nullable();
                $table->string('phone')->nullable();
                $table->string('department')->nullable();
                $table->string('avatar')->nullable();
                $table->boolean('is_archived')->default(false);
                $table->string('referral_code')->nullable()->unique();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table('professors', function (Blueprint $table) {
                if (!Schema::hasColumn('professors', 'first_name')) $table->string('first_name')->nullable();
                if (!Schema::hasColumn('professors', 'last_name')) $table->string('last_name')->nullable();
                if (!Schema::hasColumn('professors', 'email')) $table->string('email')->nullable();
                if (!Schema::hasColumn('professors', 'email_verified_at')) $table->timestamp('email_verified_at')->nullable();
                if (!Schema::hasColumn('professors', 'password')) $table->string('password')->nullable();
                if (!Schema::hasColumn('professors', 'bio')) $table->text('bio')->nullable();
                if (!Schema::hasColumn('professors', 'phone')) $table->string('phone')->nullable();
                if (!Schema::hasColumn('professors', 'department')) $table->string('department')->nullable();
                if (!Schema::hasColumn('professors', 'avatar')) $table->string('avatar')->nullable();
                if (!Schema::hasColumn('professors', 'is_archived')) $table->boolean('is_archived')->default(false);
                if (!Schema::hasColumn('professors', 'referral_code')) $table->string('referral_code')->nullable();
                if (!Schema::hasColumn('professors', 'remember_token')) $table->rememberToken();
                if (!Schema::hasColumn('professors', 'created_at')) $table->timestamps();
            });
            // Ensure unique indexes if possible
            try {
                Schema::table('professors', function (Blueprint $table) {
                    if (!Schema::hasColumn('professors', 'email')) return;
                    $table->unique('email');
                });
            } catch (\Throwable $e) { /* ignore if already unique */ }
            try {
                Schema::table('professors', function (Blueprint $table) {
                    if (Schema::hasColumn('professors', 'referral_code')) {
                        $table->unique('referral_code');
                    }
                });
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Students table (create or extend)
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('student_id')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('address')->nullable();
                $table->string('avatar')->nullable();
                $table->boolean('is_archived')->default(false);
                $table->integer('points')->default(0);
                $table->integer('level')->default(1);
                $table->integer('experience')->default(0);
                $table->json('achievements')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'student_id')) $table->string('student_id')->nullable();
                if (!Schema::hasColumn('students', 'first_name')) $table->string('first_name')->nullable();
                if (!Schema::hasColumn('students', 'last_name')) $table->string('last_name')->nullable();
                if (!Schema::hasColumn('students', 'email')) $table->string('email')->nullable();
                if (!Schema::hasColumn('students', 'email_verified_at')) $table->timestamp('email_verified_at')->nullable();
                if (!Schema::hasColumn('students', 'password')) $table->string('password')->nullable();
                if (!Schema::hasColumn('students', 'phone')) $table->string('phone')->nullable();
                if (!Schema::hasColumn('students', 'date_of_birth')) $table->date('date_of_birth')->nullable();
                if (!Schema::hasColumn('students', 'address')) $table->string('address')->nullable();
                if (!Schema::hasColumn('students', 'avatar')) $table->string('avatar')->nullable();
                if (!Schema::hasColumn('students', 'is_archived')) $table->boolean('is_archived')->default(false);
                if (!Schema::hasColumn('students', 'points')) $table->integer('points')->default(0);
                if (!Schema::hasColumn('students', 'level')) $table->integer('level')->default(1);
                if (!Schema::hasColumn('students', 'experience')) $table->integer('experience')->default(0);
                if (!Schema::hasColumn('students', 'achievements')) $table->json('achievements')->nullable();
                if (!Schema::hasColumn('students', 'remember_token')) $table->rememberToken();
                if (!Schema::hasColumn('students', 'created_at')) $table->timestamps();
            });
            try { Schema::table('students', function (Blueprint $table) { if (Schema::hasColumn('students', 'student_id')) $table->unique('student_id'); }); } catch (\Throwable $e) {}
            try { Schema::table('students', function (Blueprint $table) { if (Schema::hasColumn('students', 'email')) $table->unique('email'); }); } catch (\Throwable $e) {}
        }

        // Courses table (create or extend)
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('course_name');
                $table->text('course_description')->nullable();
                $table->unsignedBigInteger('program_id');
                $table->integer('course_order')->default(0);
                $table->boolean('is_archived')->default(false);
                $table->timestamps();
                $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            });
        } else {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'course_name')) $table->string('course_name')->nullable();
                if (!Schema::hasColumn('courses', 'course_description')) $table->text('course_description')->nullable();
                if (!Schema::hasColumn('courses', 'program_id')) $table->unsignedBigInteger('program_id')->nullable();
                if (!Schema::hasColumn('courses', 'course_order')) $table->integer('course_order')->default(0);
                if (!Schema::hasColumn('courses', 'is_archived')) $table->boolean('is_archived')->default(false);
            });
            try { Schema::table('courses', function (Blueprint $table) { if (Schema::hasColumn('courses', 'program_id')) $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); }); } catch (\Throwable $e) {}
        }

        // Modules table (create or extend)
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('module_name');
                $table->text('module_description')->nullable();
                $table->unsignedBigInteger('course_id');
                $table->text('content')->nullable();
                $table->string('video_url')->nullable();
                $table->integer('module_order')->default(0);
                $table->boolean('is_archived')->default(false);
                $table->date('deadline')->nullable();
                $table->string('batch')->nullable();
                $table->enum('learning_mode', ['synchronous', 'asynchronous', 'both'])->default('both');
                $table->timestamps();
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        } else {
            Schema::table('modules', function (Blueprint $table) {
                if (!Schema::hasColumn('modules', 'module_name')) $table->string('module_name')->nullable();
                if (!Schema::hasColumn('modules', 'module_description')) $table->text('module_description')->nullable();
                if (!Schema::hasColumn('modules', 'video_url')) $table->string('video_url')->nullable();
                if (!Schema::hasColumn('modules', 'module_order')) $table->integer('module_order')->default(0);
                if (!Schema::hasColumn('modules', 'is_archived')) $table->boolean('is_archived')->default(false);
                if (!Schema::hasColumn('modules', 'deadline')) $table->date('deadline')->nullable();
                if (!Schema::hasColumn('modules', 'batch')) $table->string('batch')->nullable();
                if (!Schema::hasColumn('modules', 'learning_mode')) $table->enum('learning_mode', ['synchronous', 'asynchronous', 'both'])->default('both');
            });
            try { Schema::table('modules', function (Blueprint $table) { if (Schema::hasColumn('modules', 'course_id')) $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); }); } catch (\Throwable $e) {}
        }

        // Enrollments table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('program_id');
            $table->enum('enrollment_type', ['full', 'modular'])->default('full');
            $table->enum('learning_mode', ['synchronous', 'asynchronous', 'both'])->default('both');
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
            $table->date('enrollment_date');
            $table->date('completion_date')->nullable();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Batches table
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->text('batch_description')->nullable();
            $table->unsignedBigInteger('program_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->integer('max_students')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Quizzes table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_title');
            $table->text('quiz_description')->nullable();
            $table->unsignedBigInteger('module_id');
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('max_attempts')->default(1);
            $table->float('passing_score')->default(70);
            $table->boolean('is_active')->default(true);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->timestamps();
            
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });

        // Quiz Questions table
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable(); // for multiple choice questions
            $table->text('correct_answer');
            $table->integer('points')->default(1);
            $table->integer('question_order')->default(0);
            $table->timestamps();
            
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        });

        // Assignments table
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('assignment_title');
            $table->text('assignment_description');
            $table->unsignedBigInteger('module_id');
            $table->date('due_date');
            $table->integer('max_points')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });

        // Announcements table
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('author_id'); // can be director, professor, or admin
            $table->string('author_type'); // 'director', 'professor', 'admin'
            $table->unsignedBigInteger('program_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_urgent')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'gcash', 'maya', 'bank_transfer', 'credit_card'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('set null');
        });

        // Add foreign key constraints that reference other tables
        Schema::table('programs', function (Blueprint $table) {
            $table->foreign('director_id')->references('id')->on('directors')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('students');
        Schema::dropIfExists('professors');
        Schema::dropIfExists('directors');
        Schema::dropIfExists('programs');
    }
};

