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
        // First check if the table exists and has only id and timestamps
        if (Schema::hasTable('enrollment_courses')) {
            $columns = Schema::getColumnListing('enrollment_courses');
            
            // If it only has basic columns, add the missing ones
            if (count($columns) <= 4) { // id, created_at, updated_at + maybe one more
                Schema::table('enrollment_courses', function (Blueprint $table) {
                    if (!Schema::hasColumn('enrollment_courses', 'enrollment_id')) {
                        $table->unsignedBigInteger('enrollment_id');
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'course_id')) {
                        $table->unsignedBigInteger('course_id');
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'module_id')) {
                        $table->unsignedBigInteger('module_id')->nullable();
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'enrollment_type')) {
                        $table->string('enrollment_type')->default('course');
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'course_price')) {
                        $table->decimal('course_price', 10, 2)->default(0);
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'is_active')) {
                        $table->boolean('is_active')->default(true);
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'enrolled_at')) {
                        $table->timestamp('enrolled_at')->nullable();
                    }
                    if (!Schema::hasColumn('enrollment_courses', 'completed_at')) {
                        $table->timestamp('completed_at')->nullable();
                    }
                    
                    // Add indexes for performance
                    try {
                        $table->index(['enrollment_id', 'course_id']);
                        $table->index(['enrollment_id', 'module_id']);
                        $table->index('course_id');
                    } catch (Exception $e) {
                        // Indexes might already exist, ignore errors
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't drop columns to preserve data
    }
};
