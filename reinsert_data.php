<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Re-inserting education levels...\n";

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

echo "Education levels inserted successfully!\n";
echo "Total count: " . DB::table('education_levels')->count() . "\n";
