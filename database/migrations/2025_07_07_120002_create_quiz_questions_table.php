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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id('quiz_id');
            $table->string('quiz_title');
            $table->integer('program_id')->nullable(); // Link to program
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->json('options')->nullable(); // For multiple choice questions
            $table->json('tags')->nullable(); // Tags for filtering/searching
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->string('difficulty')->nullable(); // easy, medium, hard
            $table->text('instructions')->nullable();
            $table->integer('points')->default(1);
            $table->string('source_file')->nullable(); // Original file name
            $table->boolean('is_active')->default(true);
            $table->integer('created_by_admin')->nullable();
            $table->integer('created_by_professor')->nullable();
            $table->timestamps();

            // Foreign key constraints (optional since they might not exist)
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('set null');
            $table->foreign('created_by_professor')->references('professor_id')->on('professors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_questions');
    }
};
