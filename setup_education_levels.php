<?php

// Simple script to create education_levels table
require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Checking if education_levels table exists...\n";
    
    if (Schema::hasTable('education_levels')) {
        echo "Table 'education_levels' already exists.\n";
    } else {
        echo "Creating education_levels table...\n";
        
        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_name')->unique();
            $table->json('file_requirements')->nullable();
            $table->boolean('available_for_general')->default(true);
            $table->boolean('available_for_professional')->default(true);
            $table->boolean('available_for_review')->default(true);
            $table->timestamps();
        });
        
        echo "Table 'education_levels' created successfully!\n";
    }

    // Check if data exists
    $count = DB::table('education_levels')->count();
    if ($count > 0) {
        echo "Found $count education levels in database.\n";
    } else {
        echo "Inserting default education levels...\n";
        
        $educationLevels = [
            [
                'level_name' => 'Undergraduate',
                'file_requirements' => json_encode([
                    'School ID' => ['required' => true, 'type' => 'image', 'description' => 'Valid school identification'],
                    'TOR file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Transcript of Records'],
                    'Good Moral file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Certificate of Good Moral Character'],
                    'PSA file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'PSA Birth Certificate']
                ]),
                'available_for_general' => true,
                'available_for_professional' => true,
                'available_for_review' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_name' => 'Graduate',
                'file_requirements' => json_encode([
                    'School ID' => ['required' => true, 'type' => 'image', 'description' => 'Valid school identification'],
                    'Diploma' => ['required' => true, 'type' => 'pdf', 'description' => 'College Diploma'],
                    'TOR file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'Transcript of Records'],
                    'PSA file upload' => ['required' => true, 'type' => 'pdf', 'description' => 'PSA Birth Certificate']
                ]),
                'available_for_general' => true,
                'available_for_professional' => true,
                'available_for_review' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($educationLevels as $level) {
            DB::table('education_levels')->updateOrInsert(
                ['level_name' => $level['level_name']],
                $level
            );
        }

        echo "Default education levels inserted successfully!\n";
    }

    echo "Education levels setup completed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
