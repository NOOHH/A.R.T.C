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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id('grade_id');
            $table->string('student_id', 30); // Match students table
            $table->integer('program_id'); // Match programs table
            $table->integer('professor_id'); // Match professors table
            $table->string('assignment_name');
            $table->decimal('grade', 5, 2); // Grade achieved
            $table->decimal('max_points', 5, 2); // Maximum possible points
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_grades');
    }
};
