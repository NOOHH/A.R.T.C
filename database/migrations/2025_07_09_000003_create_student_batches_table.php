<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('student_batches', function (Blueprint $table) {
            $table->id('batch_id');
            $table->string('batch_name');
            $table->integer('program_id');
            $table->integer('max_capacity');
            $table->integer('current_capacity')->default(0);
            $table->enum('batch_status', ['available', 'ongoing', 'closed'])->default('available');
            $table->date('registration_deadline');
            $table->date('start_date');
            $table->text('description')->nullable();
            $table->integer('created_by')->nullable(); // Admin who created the batch
            $table->timestamps();
            
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_batches');
    }
}
