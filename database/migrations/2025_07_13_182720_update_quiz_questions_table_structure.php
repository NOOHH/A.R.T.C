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
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Add quiz_id if it doesn't exist
            if (!Schema::hasColumn('quiz_questions', 'quiz_id')) {
                $table->unsignedBigInteger('quiz_id')->nullable();
            }
            
            // Add question_id as primary key if it doesn't exist
            if (!Schema::hasColumn('quiz_questions', 'question_id')) {
                $table->id('question_id');
            }
            
            // Add question_number if it doesn't exist
            if (!Schema::hasColumn('quiz_questions', 'question_number')) {
                $table->integer('question_number')->nullable();
            }
            
            // Add foreign key constraint if it doesn't exist
            try {
                $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
            } catch (Exception $e) {
                // Foreign key might already exist
            }
        });
    }

    public function down()
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropColumn(['quiz_id', 'question_number']);
        });
    }
};
