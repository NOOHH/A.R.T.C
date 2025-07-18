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
        Schema::create('package_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('subject_id'); // Use the actual course primary key name
            $table->timestamps();

            // Add indexes
            $table->index('package_id');
            $table->index('subject_id');
            
            // Ensure no duplicate course-package combinations
            $table->unique(['package_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_courses');
    }
};
