<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentprogressTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('student_progress', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(false)->primary();
            $table->string('255')('student_id')->nullable(false);
            $table->bigInteger('content_id')->nullable(false);
            $table->bigInteger('course_id');
            $table->bigInteger('module_id');
            $table->boolean('is_completed')->nullable(false)->default(0);
            $table->timestamp('completed_at');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_progress');
    }
}
