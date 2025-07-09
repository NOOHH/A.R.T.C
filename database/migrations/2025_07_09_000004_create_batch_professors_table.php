<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchProfessorsTable extends Migration
{
    public function up()
    {
        Schema::create('batch_professors', function (Blueprint $table) {
            $table->id();
            $table->integer('batch_id');
            $table->integer('professor_id');
            $table->timestamp('assigned_at')->useCurrent();
            $table->integer('assigned_by')->nullable(); // Admin who made the assignment
            $table->timestamps();
            
            $table->foreign('batch_id')->references('batch_id')->on('student_batches')->onDelete('cascade');
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('cascade');
            $table->foreign('assigned_by')->references('admin_id')->on('admins')->onDelete('set null');
            
            // Ensure one professor per batch (unique constraint)
            $table->unique(['batch_id', 'professor_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('batch_professors');
    }
}
