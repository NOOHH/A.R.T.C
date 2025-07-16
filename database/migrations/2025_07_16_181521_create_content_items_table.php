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
            $table->id();
            $table->string('content_title');
            $table->text('content_description')->nullable();
            $table->unsignedBigInteger('lesson_id');
            $table->enum('content_type', ['assignment', 'quiz', 'test', 'link', 'video', 'document']);
            $table->json('content_data')->nullable();
            $table->string('attachment_path')->nullable();
            $table->decimal('max_points', 8, 2)->nullable();
            $table->datetime('due_date')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('content_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('lesson_id')->references('lesson_id')->on('lessons')->onDelete('cascade');
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
