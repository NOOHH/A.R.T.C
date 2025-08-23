<?php
echo "🔧 ENABLING DIRECTOR PERMISSIONS\n";
echo "=" . str_repeat("=", 40) . "\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Initialize Laravel
$response = $kernel->handle(
    \Illuminate\Http\Request::create('/test', 'GET')
);

try {
    // Check the admin_settings table structure first
    $columns = \DB::select("SHOW COLUMNS FROM admin_settings");
    echo "📋 Admin Settings Table Structure:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    echo "\n";

    // Simple direct database inserts to avoid model issues
    $permissions = [
        'director_manage_modules' => 'true',
        'director_manage_professors' => 'true', 
        'director_manage_programs' => 'true',
        'director_view_students' => 'true',
        'director_manage_batches' => 'true',
        'director_view_analytics' => 'true',
        'director_manage_enrollments' => 'true'
    ];

    echo "🔑 Setting Director Permissions:\n";
    echo "=" . str_repeat("-", 35) . "\n";

    foreach ($permissions as $key => $value) {
        try {
            // Check if setting already exists
            $existing = \DB::table('admin_settings')->where('setting_key', $key)->first();
            
            if ($existing) {
                // Update existing
                \DB::table('admin_settings')
                    ->where('setting_key', $key)
                    ->update(['setting_value' => $value]);
                echo "✅ UPDATED: $key = $value\n";
            } else {
                // Insert new (only with required columns)
                \DB::table('admin_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $value
                ]);
                echo "✅ CREATED: $key = $value\n";
            }
        } catch (Exception $e) {
            echo "❌ ERROR setting $key: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 DIRECTOR PERMISSIONS ENABLED!\n";
    echo "=" . str_repeat("-", 35) . "\n";
    echo "Directors can now access:\n";
    echo "- Modules management\n";
    echo "- Professors management\n";
    echo "- Programs management\n";
    echo "- Students viewing\n";
    echo "- Batches management\n";
    echo "- Analytics viewing\n";
    echo "- Enrollments management\n";

} catch (Exception $e) {
    echo "❌ SETUP ERROR: " . $e->getMessage() . "\n";
}
?>
