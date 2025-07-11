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
        Schema::create('quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('program_id');
            $table->json('answers'); // Array of question answers
            $table->decimal('score', 5, 2)->default(0);
            $table->integer('time_spent')->default(0); // Time in seconds
            $table->boolean('is_practice')->default(false);
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
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
        Schema::dropIfExists('quiz_submissions');
    }
};
