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
        Schema::table('content_items', function (Blueprint $table) {
            // Check if lesson_id exists and drop it
            if (Schema::hasColumn('content_items', 'lesson_id')) {
                // Drop foreign key constraint for lesson_id if it exists
                try {
                    $table->dropForeign(['lesson_id']);
                } catch (Exception $e) {
                    // Foreign key might not exist
                }
                
                // Drop lesson_id column since lessons table is removed
                $table->dropColumn('lesson_id');
            }
            
            // Add course_id column if it doesn't exist
            if (!Schema::hasColumn('content_items', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('content_description');
                $table->foreign('course_id')->references('subject_id')->on('courses')->onDelete('cascade');
            }
            
            // Add missing columns for file uploads and prerequisites
            if (!Schema::hasColumn('content_items', 'content_url')) {
                $table->string('content_url')->nullable()->after('content_data');
            }
            
            if (!Schema::hasColumn('content_items', 'enable_submission')) {
                $table->boolean('enable_submission')->default(false)->after('is_active');
            }
            
            if (!Schema::hasColumn('content_items', 'allowed_file_types')) {
                $table->string('allowed_file_types')->nullable()->after('enable_submission');
            }
            
            if (!Schema::hasColumn('content_items', 'max_file_size')) {
                $table->integer('max_file_size')->nullable()->after('allowed_file_types'); // in MB
            }
            
            if (!Schema::hasColumn('content_items', 'submission_instructions')) {
                $table->text('submission_instructions')->nullable()->after('max_file_size');
            }
            
            if (!Schema::hasColumn('content_items', 'allow_multiple_submissions')) {
                $table->boolean('allow_multiple_submissions')->default(false)->after('submission_instructions');
            }
            
            // Add prerequisite columns for content dependencies
            if (!Schema::hasColumn('content_items', 'requires_prerequisite')) {
                $table->boolean('requires_prerequisite')->default(false)->after('allow_multiple_submissions');
            }
            
            if (!Schema::hasColumn('content_items', 'prerequisite_module_id')) {
                $table->unsignedBigInteger('prerequisite_module_id')->nullable()->after('requires_prerequisite');
                $table->foreign('prerequisite_module_id')->references('modules_id')->on('modules')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('content_items', 'prerequisite_course_id')) {
                $table->unsignedBigInteger('prerequisite_course_id')->nullable()->after('prerequisite_module_id');
                $table->foreign('prerequisite_course_id')->references('subject_id')->on('courses')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('content_items', 'prerequisite_content_id')) {
                $table->unsignedBigInteger('prerequisite_content_id')->nullable()->after('prerequisite_course_id');
                $table->foreign('prerequisite_content_id')->references('id')->on('content_items')->onDelete('set null');
            }
            
            // Add sort_order as alias for content_order
            if (!Schema::hasColumn('content_items', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('content_order');
            }
        });
=======
        //
>>>>>>> origin/broken-enroll-upload
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD
        Schema::table('content_items', function (Blueprint $table) {
            // Drop new foreign keys if they exist
            try {
                $table->dropForeign(['course_id']);
            } catch (Exception $e) {}
            
            try {
                $table->dropForeign(['prerequisite_module_id']);
            } catch (Exception $e) {}
            
            try {
                $table->dropForeign(['prerequisite_course_id']);
            } catch (Exception $e) {}
            
            try {
                $table->dropForeign(['prerequisite_content_id']);
            } catch (Exception $e) {}
            
            // Remove new columns if they exist
            $columnsToRemove = [
                'course_id',
                'content_url',
                'enable_submission',
                'allowed_file_types',
                'max_file_size',
                'submission_instructions',
                'allow_multiple_submissions',
                'requires_prerequisite',
                'prerequisite_module_id',
                'prerequisite_course_id',
                'prerequisite_content_id',
                'sort_order'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('content_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
=======
        //
>>>>>>> origin/broken-enroll-upload
    }
};
