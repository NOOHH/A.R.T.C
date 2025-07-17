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
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->id('meeting_id');
            $table->unsignedBigInteger('batch_id');
            $table->integer('professor_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('meeting_date');
            $table->integer('duration_minutes')->default(60);
            $table->string('meeting_url')->nullable();
            $table->json('attached_files')->nullable(); // Store file paths as JSON
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('created_by'); // Admin/Director who created
            $table->boolean('url_visible_before_meeting')->default(false);
            $table->integer('url_visibility_minutes_before')->default(0); // Minutes before meeting to show URL
            $table->timestamps();
            
            $table->foreign('batch_id')->references('batch_id')->on('student_batches')->onDelete('cascade');
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('cascade');
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('cascade');
            
            $table->index(['batch_id', 'meeting_date']);
            $table->index(['professor_id', 'meeting_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_meetings');
    }
};
