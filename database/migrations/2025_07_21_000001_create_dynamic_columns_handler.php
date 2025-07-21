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
        // This migration will be run by the DynamicColumnHandler service
        // when new document requirements are added to education levels
        
        // Add some commonly used document columns that might be missing
        $tables = ['registrations', 'students', 'enrollments'];
        
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $commonColumns = [
                    'school_id' => 'string',
                    'diploma' => 'string', 
                    'valid_school_identification' => 'string',
                    'transcript_of_records' => 'string',
                    'certificate_of_good_moral_character' => 'string',
                    'psa_birth_certificate' => 'string',
                    'photo_2x2' => 'string',
                    'diploma_certificate' => 'string',
                    'transcript_records' => 'string',
                    'moral_certificate' => 'string',
                    'birth_cert' => 'string',
                    'id_photo' => 'string',
                    'passport_photo' => 'string',
                    'medical_certificate' => 'string',
                    'barangay_clearance' => 'string',
                    'police_clearance' => 'string',
                    'nbi_clearance' => 'string'
                ];
                
                Schema::table($tableName, function (Blueprint $table) use ($commonColumns) {
                    foreach ($commonColumns as $columnName => $columnType) {
                        if (!Schema::hasColumn($table->getTable(), $columnName)) {
                            $table->string($columnName, 255)->nullable();
                        }
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns as they might contain important data
        // Instead, we'll just mark this migration as reversible
    }
};
