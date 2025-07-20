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
        Schema::create('admin_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('override_type'); // 'module', 'course', 'content'
            $table->unsignedBigInteger('target_id'); // ID of the module/course/content
            $table->unsignedBigInteger('student_id')->nullable(); // If null, applies to all students
            $table->boolean('is_locked')->default(false); // Admin lock override
            $table->boolean('requires_prerequisite')->default(false); // Prerequisite enforcement
            $table->unsignedBigInteger('prerequisite_id')->nullable(); // Parent item that must be completed
            $table->string('prerequisite_type')->nullable(); // 'module', 'course', 'content'
            $table->timestamp('release_date')->nullable(); // Scheduled release date/time
            $table->json('completion_criteria')->nullable(); // What constitutes completion
            $table->string('lock_reason')->nullable(); // Admin-defined reason for lock
            $table->unsignedBigInteger('created_by'); // Admin who created the override
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['override_type', 'target_id']);
            $table->index(['student_id']);
            $table->index(['is_locked']);
            $table->index(['release_date']);
            
            // Foreign key constraints
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_overrides');
    }
};
