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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->string('student_id'); // Reference to students.student_id
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('content_id')->references('id')->on('content_items')->onDelete('cascade');
            $table->unique(['student_id', 'content_id'], 'unique_student_content_progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_progress');
    }
};
