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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('program_id');
            $table->json('files'); // Array of file paths
            $table->timestamp('submitted_at');
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('student_id')->on('students');
            $table->foreign('module_id')->references('modules_id')->on('modules');
            $table->foreign('program_id')->references('program_id')->on('programs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
