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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
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
=======
            $table->timestamps();
>>>>>>> broken-enroll-upload
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_progress');
    }
};
