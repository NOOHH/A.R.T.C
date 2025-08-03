<?php
echo "Simple test script\n";
echo "Testing basic functionality...\n";

// Test file existence
$files = [
    'public/js/admin/admin-sidebar.js',
    'resources/views/admin/admin-modules/admin-modules-archived.blade.php',
    'app/Http/Controllers/AdminStudentListController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

echo "Test complete\n";
