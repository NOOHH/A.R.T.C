<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the existing table if it exists
        Schema::dropIfExists('quiz_questions');
        
        // Create the quiz_questions table with proper structure
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->bigInteger('quiz_id')->unsigned();
            $table->string('quiz_title');
            $table->integer('program_id')->nullable();
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('points')->default(1);
            $table->string('source_file')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('created_by_admin')->nullable();
            $table->integer('created_by_professor')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
