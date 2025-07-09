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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('program_id');
            $table->string('batch_name');
            $table->text('batch_description')->nullable();
            $table->integer('batch_capacity')->default(10);
            $table->enum('batch_status', ['available', 'ongoing', 'closed', 'completed'])->default('available');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->datetime('enrollment_deadline')->nullable();
            $table->unsignedInteger('created_by_admin_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('created_by_admin_id')->references('admin_id')->on('admins')->onDelete('cascade');
            
            // Indexes
            $table->index(['program_id', 'batch_status']);
            $table->index('batch_status');
            $table->index('enrollment_deadline');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batches');
    }
};
