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
        Schema::create('module_completions', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // References students.student_id
            $table->unsignedBigInteger('module_id'); // References modules.modules_id
            $table->unsignedBigInteger('program_id'); // References programs.program_id
            $table->timestamp('completed_at')->nullable();
            $table->decimal('score', 5, 2)->nullable(); // For quizzes/tests
            $table->integer('time_spent')->nullable(); // Time spent in seconds
            $table->json('submission_data')->nullable(); // For assignments/quiz responses
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['student_id', 'program_id']);
            $table->index(['student_id', 'module_id']);
            $table->unique(['student_id', 'module_id']); // Prevent duplicate completions
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_completions');
    }
};
