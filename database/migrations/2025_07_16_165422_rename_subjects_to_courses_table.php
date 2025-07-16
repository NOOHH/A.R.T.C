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
        // Rename subjects table to courses
        Schema::rename('subjects', 'courses');
        
        // Rename columns in courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('subject_id', 'course_id');
            $table->renameColumn('subject_name', 'course_name');
            $table->renameColumn('subject_description', 'course_description');
            $table->renameColumn('subject_price', 'course_price');
            $table->renameColumn('subject_order', 'course_order');
        });
        
        // Update module_subjects table to module_courses
        Schema::rename('module_subjects', 'module_courses');
        
        // Update foreign key references
        Schema::table('module_courses', function (Blueprint $table) {
            $table->renameColumn('subject_id', 'course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse the changes
        Schema::table('module_courses', function (Blueprint $table) {
            $table->renameColumn('course_id', 'subject_id');
        });
        
        Schema::rename('module_courses', 'module_subjects');
        
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('course_id', 'subject_id');
            $table->renameColumn('course_name', 'subject_name');
            $table->renameColumn('course_description', 'subject_description');
            $table->renameColumn('course_price', 'subject_price');
            $table->renameColumn('course_order', 'subject_order');
        });
        
        Schema::rename('courses', 'subjects');
    }
};
