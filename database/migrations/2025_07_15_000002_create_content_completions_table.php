<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('content_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('content_items')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->unique(['student_id', 'content_id', 'course_id', 'module_id'], 'unique_content_completion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('content_completions');
    }
}; 