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
        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'batch_status')) {
                $table->enum('batch_status', ['available', 'ongoing', 'closed', 'completed', 'pending', 'not_verified'])->default('available')->after('batch_capacity');
            } else {
                // Modify existing enum to include new statuses
                DB::statement("ALTER TABLE batches MODIFY COLUMN batch_status ENUM('available', 'ongoing', 'closed', 'completed', 'pending', 'not_verified') DEFAULT 'available'");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            // Revert to original enum values
            DB::statement("ALTER TABLE batches MODIFY COLUMN batch_status ENUM('available', 'ongoing', 'closed', 'completed') DEFAULT 'available'");
        });
    }
};
