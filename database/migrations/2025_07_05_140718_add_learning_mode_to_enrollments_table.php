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
        Schema::table('enrollments', function (Blueprint $table) {
            // Add learning mode field (Synchronous/Asynchronous)
            $table->enum('learning_mode', ['Synchronous', 'Asynchronous'])->default('Synchronous')->after('enrollment_type');
            
            // Remove the unique constraint if it exists to allow multiple enrollments per student
            $table->dropUnique(['student_id', 'program_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Remove learning mode field
            $table->dropColumn('learning_mode');
            
            // Re-add unique constraint
            $table->unique(['student_id', 'program_id']);
        });
    }
};
