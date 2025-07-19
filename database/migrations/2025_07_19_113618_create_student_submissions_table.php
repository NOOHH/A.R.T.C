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
        Schema::create('student_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('content_id');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_type', 50);
            $table->bigInteger('file_size'); // in bytes
            $table->text('submission_notes')->nullable();
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('graded_at')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable(); // admin/teacher user ID
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('content_items')->onDelete('cascade');
            $table->foreign('graded_by')->references('user_id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['student_id', 'content_id']);
            $table->index(['status']);
            $table->index(['submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_submissions');
    }
};
