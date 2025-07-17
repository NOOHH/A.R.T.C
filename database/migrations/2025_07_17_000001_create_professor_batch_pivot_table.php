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
        Schema::create('professor_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->integer('professor_id');
            $table->timestamp('assigned_at')->nullable();
            $table->integer('assigned_by')->nullable(); // Admin who assigned
            $table->timestamps();
            
            $table->foreign('batch_id')->references('batch_id')->on('student_batches')->onDelete('cascade');
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('cascade');
            $table->foreign('assigned_by')->references('admin_id')->on('admins')->onDelete('set null');
            
            $table->unique(['batch_id', 'professor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professor_batch');
    }
};
