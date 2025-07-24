<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_content_completions', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->index(['student_id', 'content_id']);
            $table->index(['student_id', 'course_id']);
            $table->index(['user_id', 'content_id']);
            $table->unique(['student_id', 'content_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_content_completions');
    }
};
