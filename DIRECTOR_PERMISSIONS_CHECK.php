<?php
echo "🔧 DIRECTOR PERMISSIONS DIAGNOSTIC\n";
echo "=" . str_repeat("=", 40) . "\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Initialize Laravel
$response = $kernel->handle(
    \Illuminate\Http\Request::create('/test', 'GET')
);

echo "📋 Current Director Permission Settings:\n";
echo "=" . str_repeat("-", 35) . "\n";

$permissions = [
    'director_manage_modules' => 'Manage Modules',
    'director_manage_professors' => 'Manage Professors', 
    'director_manage_programs' => 'Manage Programs',
    'director_manage_batches' => 'Manage Batches',
    'director_view_students' => 'View Students',
    'director_view_analytics' => 'View Analytics',
    'director_manage_enrollments' => 'Manage Enrollments'
];

foreach ($permissions as $key => $label) {
    $value = \App\Models\AdminSetting::getValue($key, 'not_set');
    $status = $value === 'true' || $value === '1' ? '✅ ENABLED' : '❌ DISABLED';
    echo sprintf("%-30s: %s (value: %s)\n", $label, $status, $value);
}

echo "\n💡 RECOMMENDED ACTIONS:\n";
echo "=" . str_repeat("-", 35) . "\n";

echo "To enable director access to modules, run:\n";
echo "php artisan tinker --execute=\"App\\Models\\AdminSetting::setValue('director_manage_modules', 'true');\"\n\n";

echo "To enable all director permissions, run these commands:\n";
foreach (array_keys($permissions) as $key) {
    echo "App\\Models\\AdminSetting::setValue('$key', 'true');\n";
}

echo "\n🎯 ISSUE SUMMARY:\n";
echo "=" . str_repeat("-", 35) . "\n";
echo "The director cannot access admin features because the permission\n";
echo "settings are not enabled. Enable the required permissions to\n";
echo "allow director access to modules and other admin features.\n";
?>
