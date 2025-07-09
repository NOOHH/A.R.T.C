<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::create('batch_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->unsignedBigInteger('program_id');
            $table->integer('max_students');
            $table->integer('current_students')->default(0);
            $table->enum('batch_status', ['available', 'ongoing', 'closed'])->default('available');
            $table->timestamp('enrollment_deadline')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('batch_enrollments');
    }
}
