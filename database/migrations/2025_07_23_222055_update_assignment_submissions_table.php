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
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Add missing columns
            $table->string('file_path')->nullable()->after('program_id');
            $table->string('original_filename')->nullable()->after('file_path');
            $table->text('comments')->nullable()->after('files');
            
            // Update status enum to include new values
            $table->dropColumn('status');
        });
        
        // Add the status column with new enum values
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'graded', 'reviewed', 'returned'])->default('submitted')->after('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['file_path', 'original_filename', 'comments']);
            
            // Restore original status enum
            $table->dropColumn('status');
        });
        
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted')->after('submitted_at');
        });
    }
};
