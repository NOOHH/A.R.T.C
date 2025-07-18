<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollment_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('module_id');
            $table->enum('enrollment_type', ['module', 'course'])->default('course');
            $table->decimal('course_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index('enrollment_id');
            $table->index('course_id');
            $table->index('module_id');
            
            // Ensure no duplicate course enrollments
            $table->unique(['enrollment_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_courses');
    }
};
