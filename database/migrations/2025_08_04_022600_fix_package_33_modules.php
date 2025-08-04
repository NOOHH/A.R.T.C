<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, set package 33's program_id to 40 (Nursing program)
        DB::table('packages')
            ->where('package_id', 33)
            ->update(['program_id' => 40]);
        
        // Then add some modules from program 40 to package 33
        // Get modules from program 40 (Nursing)
        $nursingModules = DB::table('modules')
            ->where('program_id', 40)
            ->where('is_archived', false)
            ->get();
        
        // Add these modules to package 33
        foreach ($nursingModules as $module) {
            DB::table('package_modules')->insert([
                'package_id' => 33,
                'modules_id' => $module->modules_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove modules from package 33
        DB::table('package_modules')
            ->where('package_id', 33)
            ->delete();
        
        // Reset package 33's program_id to NULL
        DB::table('packages')
            ->where('package_id', 33)
            ->update(['program_id' => null]);
    }
};
