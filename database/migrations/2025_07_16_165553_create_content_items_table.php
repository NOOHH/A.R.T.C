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
        Schema::create('content_items', function (Blueprint $table) {
            $table->id('content_id');
            $table->string('content_title');
            $table->text('content_description')->nullable();
            $table->enum('content_type', ['lesson', 'assignment', 'activity', 'quiz', 'test', 'file', 'video', 'link'])->default('lesson');
            $table->string('parent_type'); // 'module', 'course', 'lesson'
            $table->integer('parent_id'); // ID of parent (module_id, course_id, lesson_id)
            $table->text('content_data')->nullable(); // JSON data for specific content type
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('content_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->enum('learning_mode', ['Synchronous', 'Asynchronous', 'Both'])->default('Both');
            $table->timestamps();
            
            $table->index(['parent_type', 'parent_id', 'content_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_items');
    }
};
