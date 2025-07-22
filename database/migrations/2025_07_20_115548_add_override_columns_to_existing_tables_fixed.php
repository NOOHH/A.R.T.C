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
        // Add override columns to modules table (without foreign keys for now)
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (!Schema::hasColumn('modules', 'requires_prerequisite')) {
                $table->boolean('requires_prerequisite')->default(false);
            }
            if (!Schema::hasColumn('modules', 'prerequisite_module_id')) {
                $table->unsignedInteger('prerequisite_module_id')->nullable();
            }
            if (!Schema::hasColumn('modules', 'release_date')) {
                $table->timestamp('release_date')->nullable();
            }
            if (!Schema::hasColumn('modules', 'completion_criteria')) {
                $table->json('completion_criteria')->nullable();
            }
            if (!Schema::hasColumn('modules', 'lock_reason')) {
                $table->string('lock_reason')->nullable();
            }
            if (!Schema::hasColumn('modules', 'locked_by')) {
                $table->unsignedBigInteger('locked_by')->nullable();
            }
        });

        // Add override columns to courses table
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (!Schema::hasColumn('courses', 'requires_prerequisite')) {
                $table->boolean('requires_prerequisite')->default(false);
            }
            if (!Schema::hasColumn('courses', 'prerequisite_course_id')) {
                $table->unsignedInteger('prerequisite_course_id')->nullable();
            }
            if (!Schema::hasColumn('courses', 'release_date')) {
                $table->timestamp('release_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'completion_criteria')) {
                $table->json('completion_criteria')->nullable();
            }
            if (!Schema::hasColumn('courses', 'lock_reason')) {
                $table->string('lock_reason')->nullable();
            }
            if (!Schema::hasColumn('courses', 'locked_by')) {
                $table->unsignedBigInteger('locked_by')->nullable();
            }
        });

        // Add override columns to content_items table
        Schema::table('content_items', function (Blueprint $table) {
            if (!Schema::hasColumn('content_items', 'is_locked')) {
                $table->boolean('is_locked')->default(false);
            }
            if (!Schema::hasColumn('content_items', 'requires_prerequisite')) {
                $table->boolean('requires_prerequisite')->default(false);
            }
            if (!Schema::hasColumn('content_items', 'prerequisite_content_id')) {
                $table->unsignedInteger('prerequisite_content_id')->nullable();
            }
            if (!Schema::hasColumn('content_items', 'release_date')) {
                $table->timestamp('release_date')->nullable();
            }
            if (!Schema::hasColumn('content_items', 'completion_criteria')) {
                $table->json('completion_criteria')->nullable();
            }
            if (!Schema::hasColumn('content_items', 'lock_reason')) {
                $table->string('lock_reason')->nullable();
            }
            if (!Schema::hasColumn('content_items', 'locked_by')) {
                $table->unsignedBigInteger('locked_by')->nullable();
            }
        });

        // Remove lesson_id from content_items if it exists (since lessons table was dropped)
        if (Schema::hasColumn('content_items', 'lesson_id')) {
            Schema::table('content_items', function (Blueprint $table) {
                try {
                    $table->dropForeign(['lesson_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, ignore
                }
                $table->dropColumn('lesson_id');
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
        // Remove override columns from modules table
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'is_locked')) {
                $table->dropColumn([
                    'is_locked', 'requires_prerequisite', 'prerequisite_module_id', 
                    'release_date', 'completion_criteria', 'lock_reason', 'locked_by'
                ]);
            }
        });

        // Remove override columns from courses table
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'is_locked')) {
                $table->dropColumn([
                    'is_locked', 'requires_prerequisite', 'prerequisite_course_id', 
                    'release_date', 'completion_criteria', 'lock_reason', 'locked_by'
                ]);
            }
        });

        // Remove override columns from content_items table
        Schema::table('content_items', function (Blueprint $table) {
            if (Schema::hasColumn('content_items', 'is_locked')) {
                $table->dropColumn([
                    'is_locked', 'requires_prerequisite', 'prerequisite_content_id', 
                    'release_date', 'completion_criteria', 'lock_reason', 'locked_by'
                ]);
            }
        });
    }
};
