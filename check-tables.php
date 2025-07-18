<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Checking table structures:\n";

if (Schema::hasTable('subjects')) {
    echo "subjects table EXISTS\n";
    echo "subjects columns: " . implode(', ', Schema::getColumnListing('subjects')) . "\n";
}

if (Schema::hasTable('courses')) {
    echo "courses table EXISTS\n";
    echo "courses columns: " . implode(', ', Schema::getColumnListing('courses')) . "\n";
}

echo "package_courses columns: " . implode(', ', Schema::getColumnListing('package_courses')) . "\n";

// Check what the Course model primary key is
$course = new App\Models\Course();
echo "Course model key name: " . $course->getKeyName() . "\n";

?>
