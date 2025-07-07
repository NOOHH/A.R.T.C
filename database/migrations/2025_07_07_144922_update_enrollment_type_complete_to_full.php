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
        // First, modify the enum to include both 'Complete' and 'Full' temporarily
        DB::statement("ALTER TABLE enrollments MODIFY enrollment_type ENUM('Modular', 'Complete', 'Full') NOT NULL");
        
        // Update existing 'Complete' values to 'Full' in enrollments table
        DB::statement("UPDATE enrollments SET enrollment_type = 'Full' WHERE enrollment_type = 'Complete'");
        
        // Now remove 'Complete' from the enum, leaving only 'Modular' and 'Full'
        DB::statement("ALTER TABLE enrollments MODIFY enrollment_type ENUM('Modular', 'Full') NOT NULL");
        
        // Update any other tables that might reference 'Complete'
        DB::statement("UPDATE form_requirements SET program_type = 'Full' WHERE program_type = 'Complete'");
        DB::statement("UPDATE form_requirements SET program_type = 'full' WHERE program_type = 'complete'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First, modify the enum to include both 'Full' and 'Complete' temporarily
        DB::statement("ALTER TABLE enrollments MODIFY enrollment_type ENUM('Modular', 'Full', 'Complete') NOT NULL");
        
        // Revert 'Full' values back to 'Complete' in enrollments table
        DB::statement("UPDATE enrollments SET enrollment_type = 'Complete' WHERE enrollment_type = 'Full'");
        
        // Remove 'Full' from the enum, leaving only 'Modular' and 'Complete'
        DB::statement("ALTER TABLE enrollments MODIFY enrollment_type ENUM('Modular', 'Complete') NOT NULL");
        
        // Revert any other tables
        DB::statement("UPDATE form_requirements SET program_type = 'Complete' WHERE program_type = 'Full'");
        DB::statement("UPDATE form_requirements SET program_type = 'complete' WHERE program_type = 'full'");
    }
};
