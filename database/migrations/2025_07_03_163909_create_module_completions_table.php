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
            $table->string('student_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('program_id');
            $table->dateTime('completed_at');
            $table->float('score')->nullable();
            $table->json('submission_data')->nullable();
            $table->timestamps();
            
            // Note: Foreign keys are commented out for now since we're having issues with them
            // You may need to adjust these based on your database structure
            // $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            // $table->foreign('module_id')->references('modules_id')->on('modules')->onDelete('cascade');
            // $table->foreign('program_id')->references('program_id')->on('program')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate completions
            $table->unique(['student_id', 'module_id']);
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
