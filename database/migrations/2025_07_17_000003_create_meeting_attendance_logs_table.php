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
        Schema::create('meeting_attendance_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('meeting_id');
            $table->integer('student_id');
            $table->timestamp('link_clicked_at')->nullable(); // When student clicked meeting link
            $table->enum('attendance_status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->integer('marked_by')->nullable(); // Professor who marked attendance
            $table->timestamp('marked_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable(); // IP when link was clicked
            $table->text('user_agent')->nullable(); // Browser info when link was clicked
            $table->timestamps();
            
            $table->foreign('meeting_id')->references('meeting_id')->on('class_meetings')->onDelete('cascade');
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('marked_by')->references('professor_id')->on('professors')->onDelete('set null');
            
            $table->unique(['meeting_id', 'student_id']);
            $table->index(['meeting_id', 'attendance_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_attendance_logs');
    }
};
