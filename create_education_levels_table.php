<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Set up the database connection using Laravel's database configuration
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if table already exists
    if (Capsule::schema()->hasTable('education_levels')) {
        echo "Table 'education_levels' already exists.\n";
        exit;
    }

    // Create the education_levels table
    Capsule::schema()->create('education_levels', function (Blueprint $table) {
        $table->id();
        $table->string('level_name')->unique();
        $table->json('file_requirements')->nullable();
        $table->boolean('available_for_general')->default(true);
        $table->boolean('available_for_professional')->default(true);
        $table->boolean('available_for_review')->default(true);
        $table->timestamps();
    });

    echo "Table 'education_levels' created successfully!\n";

    // Insert default data
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
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
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
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    ];

    foreach ($educationLevels as $level) {
        Capsule::table('education_levels')->insert($level);
    }

    echo "Default education levels inserted successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
