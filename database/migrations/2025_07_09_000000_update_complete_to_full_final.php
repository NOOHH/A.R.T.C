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
        // Update form_requirements table to use 'full' instead of 'complete'
        DB::statement("ALTER TABLE form_requirements MODIFY program_type ENUM('full', 'modular', 'both') NOT NULL DEFAULT 'both'");
        
        // Update any remaining 'complete' values to 'full' in form_requirements table
        DB::statement("UPDATE form_requirements SET program_type = 'full' WHERE program_type = 'complete'");
        
        // Update any remaining 'Complete' values to 'full' in form_requirements table
        DB::statement("UPDATE form_requirements SET program_type = 'full' WHERE program_type = 'Complete'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert 'full' values back to 'complete' in form_requirements table
        DB::statement("UPDATE form_requirements SET program_type = 'complete' WHERE program_type = 'full'");
        
        // Restore the original enum
        DB::statement("ALTER TABLE form_requirements MODIFY program_type ENUM('complete', 'modular', 'both') NOT NULL DEFAULT 'both'");
    }
};
