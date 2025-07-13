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
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id('option_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('question_id'); // This will be the id from quiz_questions
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            
            // Use id as foreign key instead of question_id
            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_options');
    }
};
