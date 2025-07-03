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
    public function up(): void
    {
        Schema::create('professor_program', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('professor_id'); // Match the existing professors table primary key type
            $table->unsignedInteger('program_id');   // Match the existing programs table primary key type
            $table->string('video_link')->nullable();
            $table->text('video_description')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('professor_id')->references('professor_id')->on('professors')->onDelete('cascade');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['professor_id', 'program_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_program');
    }
};
