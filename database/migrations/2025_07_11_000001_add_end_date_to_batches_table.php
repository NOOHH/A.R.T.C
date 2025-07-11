<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add end_date to student_batches table
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                if (!Schema::hasColumn('student_batches', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
            });
            
            // Update batch_status enum to include pending and not_verified
            DB::statement("ALTER TABLE student_batches MODIFY COLUMN batch_status ENUM('pending', 'not_verified', 'available', 'ongoing', 'closed', 'completed') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                if (Schema::hasColumn('student_batches', 'end_date')) {
                    $table->dropColumn('end_date');
                }
            });
            
            // Restore original batch_status enum
            DB::statement("ALTER TABLE student_batches MODIFY COLUMN batch_status ENUM('available', 'ongoing', 'closed', 'completed') DEFAULT 'available'");
        }
    }
};
