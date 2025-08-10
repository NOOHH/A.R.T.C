<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingattendancelogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meeting_attendance_logs', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->bigInteger('meeting_id')->nullable(false);
            $table->bigInteger('student_id')->nullable(false);
            $table->enum(''present','absent','late'')('status')->nullable(false);
            $table->timestamp('joined_at');
            $table->timestamp('left_at');
            $table->integer('duration_minutes');
            $table->string('255')('ip_address');
            $table->text('notes');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('meeting_attendance_logs');
    }
}
