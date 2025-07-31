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
        // Add progress tracking fields to enrollments table if they don't exist
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'progress_percentage')) {
                $table->decimal('progress_percentage', 5, 2)->default(0.00)->after('enrollment_status');
            }
            if (!Schema::hasColumn('enrollments', 'completion_date')) {
                $table->timestamp('completion_date')->nullable()->after('progress_percentage');
            }
            if (!Schema::hasColumn('enrollments', 'certificate_generated')) {
                $table->boolean('certificate_generated')->default(false)->after('completion_date');
            }
            if (!Schema::hasColumn('enrollments', 'certificate_generated_at')) {
                $table->timestamp('certificate_generated_at')->nullable()->after('certificate_generated');
            }
        });
        
        // Create course completions table for tracking individual course progress
        if (!Schema::hasTable('course_completions')) {
            Schema::create('course_completions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('enrollment_id');
                $table->decimal('completion_percentage', 5, 2)->default(0.00);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamps();
                
                $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
                $table->foreign('enrollment_id')->references('enrollment_id')->on('enrollments')->onDelete('cascade');
                
                $table->unique(['student_id', 'course_id', 'enrollment_id']);
            });
        }
        
        // Create module completions table for tracking module progress
        if (!Schema::hasTable('module_completions')) {
            Schema::create('module_completions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('module_id');
                $table->unsignedBigInteger('enrollment_id');
                $table->decimal('completion_percentage', 5, 2)->default(0.00);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamps();
                
                $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
                $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
                $table->foreign('enrollment_id')->references('enrollment_id')->on('enrollments')->onDelete('cascade');
                
                $table->unique(['student_id', 'module_id', 'enrollment_id']);
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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['progress_percentage', 'completion_date', 'certificate_generated', 'certificate_generated_at']);
        });
        
        Schema::dropIfExists('course_completions');
        Schema::dropIfExists('module_completions');
    }
};
