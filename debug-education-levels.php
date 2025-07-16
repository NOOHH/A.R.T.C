<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EducationLevel;

echo "=== EDUCATION LEVEL FILE REQUIREMENTS DEBUG ===\n\n";

$levels = EducationLevel::all();

foreach ($levels as $level) {
    echo "Level: {$level->level_name}\n";
    echo "Raw file_requirements: " . json_encode($level->file_requirements) . "\n";
    echo "Formatted for plan: " . json_encode($level->getFileRequirementsForPlan('full')) . "\n";
    echo "---\n";
}
