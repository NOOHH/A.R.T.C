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
        Schema::create('student_deadlines', function (Blueprint $table) {
            $table->id('deadline_id');
            $table->unsignedBigInteger('student_id');
            $table->string('assignment_type'); // 'quiz', 'assignment', 'project', etc.
            $table->string('assignment_title');
            $table->text('description')->nullable();
            $table->datetime('due_date');
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_deadlines');
    }
};
