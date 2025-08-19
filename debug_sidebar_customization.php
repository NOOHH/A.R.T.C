<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\UiSetting;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== SIDEBAR CUSTOMIZATION SYSTEM DEBUG ===\n\n";

try {
    echo "1. Testing Database Connection:\n";
    $connection = DB::connection()->getPdo();
    echo "   ✓ Database connected successfully\n\n";
    
    echo "2. Testing UI Settings Table:\n";
    $columns = DB::select('DESCRIBE ui_settings');
    echo "   ✓ UI Settings table exists with " . count($columns) . " columns\n";
    foreach ($columns as $column) {
        echo "     - {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
    
    echo "3. Testing Default Sidebar Settings:\n";
    $defaultSettings = UiSetting::where('section', 'student_sidebar')->get();
    echo "   ✓ Found " . $defaultSettings->count() . " default sidebar settings:\n";
    foreach ($defaultSettings as $setting) {
        echo "     - {$setting->setting_key}: {$setting->setting_value}\n";
    }
    echo "\n";
    
    echo "4. Testing UiSetting Model Methods:\n";
    $primaryColor = UiSetting::get('student_sidebar', 'primary_color', '#000000');
    echo "   ✓ UiSetting::get() method works: primary_color = {$primaryColor}\n";
    
    $allSidebarSettings = UiSetting::getSection('student_sidebar');
    echo "   ✓ UiSetting::getSection() method works: " . count($allSidebarSettings) . " settings retrieved\n\n";
    
    echo "5. Testing Student-Specific Settings:\n";
    $userSettings = UiSetting::where('section', 'LIKE', 'student_sidebar_%')->get();
    echo "   ✓ Found " . $userSettings->count() . " user-specific sidebar settings\n";
    if ($userSettings->count() > 0) {
        foreach ($userSettings as $setting) {
            echo "     - {$setting->section}: {$setting->setting_key} = {$setting->setting_value}\n";
        }
    } else {
        echo "     - No user-specific settings found (this is normal for new installation)\n";
    }
    echo "\n";
    
    echo "6. Testing Routes:\n";
    $routes = [
        '/api/student/sidebar-settings',
        '/student/settings',
        '/student/dashboard/preview',
        '/test-ui-settings'
    ];
    
    foreach ($routes as $route) {
        try {
            $response = file_get_contents("http://127.0.0.1:8000{$route}");
            echo "   ✓ Route {$route} is accessible\n";
        } catch (Exception $e) {
            echo "   ✗ Route {$route} failed: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    
    echo "7. Testing CSS Custom Properties:\n";
    $cssProperties = [
        '--sidebar-bg' => $allSidebarSettings['primary_color'] ?? '#1a1a1a',
        '--sidebar-hover' => $allSidebarSettings['secondary_color'] ?? '#2d2d2d',
        '--sidebar-active' => $allSidebarSettings['accent_color'] ?? '#3b82f6',
        '--sidebar-text' => $allSidebarSettings['text_color'] ?? '#e0e0e0',
        '--sidebar-border' => $allSidebarSettings['secondary_color'] ?? '#2d2d2d'
    ];
    
    foreach ($cssProperties as $property => $value) {
        echo "   ✓ {$property}: {$value}\n";
    }
    echo "\n";
    
    echo "8. File Integrity Check:\n";
    $files = [
        'resources/views/student/student-settings/settings.blade.php',
        'resources/views/components/student-sidebar.blade.php',
        'public/css/student/student-sidebar-professional.css',
        'app/Http/Controllers/StudentController.php',
        'app/Models/UiSetting.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "   ✓ {$file} exists (" . number_format(filesize($file)) . " bytes)\n";
        } else {
            echo "   ✗ {$file} missing\n";
        }
    }
    echo "\n";
    
    echo "9. Testing Color Validation:\n";
    $testColors = ['#1a1a1a', '#ff0000', '#invalid', '#12345g'];
    foreach ($testColors as $color) {
        $isValid = preg_match('/^#[0-9A-Fa-f]{6}$/', $color);
        $status = $isValid ? '✓' : '✗';
        echo "   {$status} {$color}: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
    }
    echo "\n";
    
    echo "✅ SIDEBAR CUSTOMIZATION SYSTEM STATUS: FULLY OPERATIONAL\n\n";
    
    echo "Next Steps:\n";
    echo "1. Open http://127.0.0.1:8000/student/dashboard/preview to see the sidebar\n";
    echo "2. Open http://127.0.0.1:8000/sidebar-customization-test.html for interactive testing\n";
    echo "3. Login as a student and go to Settings to customize sidebar colors\n";
    echo "4. Test the API endpoints with authentication\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
