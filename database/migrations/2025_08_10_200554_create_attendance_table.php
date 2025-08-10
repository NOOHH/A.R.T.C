<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('30')('student_id')->nullable(false);
            $table->integer('program_id')->nullable(false);
            $table->integer('professor_id')->nullable(false);
            $table->date('attendance_date')->nullable(false);
            $table->enum(''present','absent','late','excused'')('status')->nullable(false);
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
        Schema::dropIfExists('attendance');
    }
}
