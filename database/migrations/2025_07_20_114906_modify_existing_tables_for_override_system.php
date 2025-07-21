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
<<<<<<< HEAD
        // First, drop the lessons table constraint from content_items
        Schema::table('content_items', function (Blueprint $table) {
            // Drop foreign key constraint to lessons table
            if (Schema::hasColumn('content_items', 'lesson_id')) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            }
        });

        // Add override-related columns to modules table
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'is_locked_by_admin')) {
                $table->boolean('is_locked_by_admin')->default(false)->after('module_order');
                $table->timestamp('admin_release_date')->nullable()->after('is_locked_by_admin');
                $table->string('admin_lock_reason')->nullable()->after('admin_release_date');
                $table->boolean('requires_prerequisite')->default(false)->after('admin_lock_reason');
                $table->unsignedBigInteger('prerequisite_module_id')->nullable()->after('requires_prerequisite');
                $table->json('completion_criteria')->nullable()->after('prerequisite_module_id');
                
                // Add foreign key for prerequisite
                $table->foreign('prerequisite_module_id')->references('modules_id')->on('modules')->onDelete('set null');
            }
        });

        // Add override-related columns to courses table  
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'is_locked_by_admin')) {
                $table->boolean('is_locked_by_admin')->default(false)->after('is_active');
                $table->timestamp('admin_release_date')->nullable()->after('is_locked_by_admin');
                $table->string('admin_lock_reason')->nullable()->after('admin_release_date');
                $table->boolean('requires_prerequisite')->default(false)->after('admin_lock_reason');
                $table->string('prerequisite_type')->nullable()->after('requires_prerequisite'); // 'module' or 'course'
                $table->unsignedBigInteger('prerequisite_id')->nullable()->after('prerequisite_type');
                $table->json('completion_criteria')->nullable()->after('prerequisite_id');
            }
        });

        // Add override-related columns to content_items table
        Schema::table('content_items', function (Blueprint $table) {
            if (!Schema::hasColumn('content_items', 'is_locked_by_admin')) {
                $table->boolean('is_locked_by_admin')->default(false)->after('is_active');
                $table->timestamp('admin_release_date')->nullable()->after('is_locked_by_admin');
                $table->string('admin_lock_reason')->nullable()->after('admin_release_date');
                $table->boolean('requires_prerequisite')->default(false)->after('admin_lock_reason');
                $table->string('prerequisite_type')->nullable()->after('requires_prerequisite'); // 'module', 'course', or 'content'
                $table->unsignedBigInteger('prerequisite_id')->nullable()->after('prerequisite_type');
                $table->json('completion_criteria')->nullable()->after('prerequisite_id');
            }
        });

        // Create student_progress table for tracking completion
        if (!Schema::hasTable('student_progress')) {
            Schema::create('student_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->string('item_type'); // 'module', 'course', 'content'
                $table->unsignedBigInteger('item_id'); // ID of the module/course/content
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->decimal('progress_percentage', 5, 2)->default(0); // For partial completion
                $table->json('completion_data')->nullable(); // Store additional completion data
                $table->timestamps();
                
                // Unique constraint to prevent duplicate progress records
                $table->unique(['student_id', 'item_type', 'item_id']);
                
                // Indexes for performance
                $table->index(['student_id']);
                $table->index(['item_type', 'item_id']);
                $table->index(['is_completed']);
                
                // Foreign key constraints
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
=======
        //
>>>>>>> broken-enroll-upload
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD
        // Remove override columns from content_items table
        Schema::table('content_items', function (Blueprint $table) {
            if (Schema::hasColumn('content_items', 'is_locked_by_admin')) {
                $table->dropColumn([
                    'is_locked_by_admin',
                    'admin_release_date', 
                    'admin_lock_reason',
                    'requires_prerequisite',
                    'prerequisite_type',
                    'prerequisite_id',
                    'completion_criteria'
                ]);
            }
        });

        // Remove override columns from courses table
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'is_locked_by_admin')) {
                $table->dropColumn([
                    'is_locked_by_admin',
                    'admin_release_date',
                    'admin_lock_reason', 
                    'requires_prerequisite',
                    'prerequisite_type',
                    'prerequisite_id',
                    'completion_criteria'
                ]);
            }
        });

        // Remove override columns from modules table
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'prerequisite_module_id')) {
                $table->dropForeign(['prerequisite_module_id']);
            }
            if (Schema::hasColumn('modules', 'is_locked_by_admin')) {
                $table->dropColumn([
                    'is_locked_by_admin',
                    'admin_release_date',
                    'admin_lock_reason',
                    'requires_prerequisite', 
                    'prerequisite_module_id',
                    'completion_criteria'
                ]);
            }
        });

        // Drop student_progress table
        Schema::dropIfExists('student_progress');

        // Re-add lesson_id column to content_items if needed
        Schema::table('content_items', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable()->after('content_description');
        });
=======
        //
>>>>>>> broken-enroll-upload
    }
};
