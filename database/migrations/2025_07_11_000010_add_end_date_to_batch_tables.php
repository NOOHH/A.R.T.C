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
        // Add end_date to student_batches table
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                if (!Schema::hasColumn('student_batches', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
            });
        }
        
        // Add end_date to batches table if it exists
        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                if (!Schema::hasColumn('batches', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('batches')) {
            Schema::table('batches', function (Blueprint $table) {
                if (Schema::hasColumn('batches', 'end_date')) {
                    $table->dropColumn('end_date');
                }
            });
        }
        
        if (Schema::hasTable('student_batches')) {
            Schema::table('student_batches', function (Blueprint $table) {
                if (Schema::hasColumn('student_batches', 'end_date')) {
                    $table->dropColumn('end_date');
                }
            });
        }
    }
};
