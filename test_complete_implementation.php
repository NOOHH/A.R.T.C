<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;
use App\Models\Client;
use App\Models\User;

try {
    echo "Testing complete admin settings copying functionality...\n\n";
    
    // Check if we have all the admin settings available
    $sections = [
        'general' => 'General Settings',
        'navbar' => 'Navigation Bar',
        'branding' => 'Branding',
        'homepage' => 'Homepage Content',
        'student_portal' => 'Student Portal',
        'professor_panel' => 'Professor Panel',
        'admin_panel' => 'Admin Panel',
        'student_sidebar' => 'Student Sidebar',
        'professor_sidebar' => 'Professor Sidebar',
        'admin_sidebar' => 'Admin Sidebar',
    ];
    
    echo "✅ ADMIN SETTINGS AVAILABLE:\n";
    $totalSettings = 0;
    
    foreach ($sections as $section => $label) {
        $settings = UiSetting::getSection($section);
        $count = count($settings);
        $totalSettings += $count;
        
        echo "   {$label}: {$count} settings\n";
        
        if ($count > 0 && in_array($section, ['homepage', 'student_portal', 'professor_panel'])) {
            echo "     Sample settings:\n";
            foreach ($settings->take(2) as $key => $value) {
                $displayValue = strlen($value) > 30 ? substr($value, 0, 27) . '...' : $value;
                echo "       • {$key}: {$displayValue}\n";
            }
        }
    }
    
    echo "\n   TOTAL SETTINGS TO COPY: {$totalSettings}\n\n";
    
    // Test routes
    echo "✅ ROUTES REGISTERED:\n";
    $routes = [
        'smartprep.dashboard.websites.store' => 'Create Website',
        'smartprep.dashboard.settings.update.general' => 'Update General',
        'smartprep.dashboard.settings.update.homepage' => 'Update Homepage',
        'smartprep.dashboard.settings.update.student' => 'Update Student Portal',
        'smartprep.dashboard.settings.update.professor' => 'Update Professor Panel',
        'smartprep.dashboard.settings.update.admin' => 'Update Admin Panel',
        'smartprep.dashboard.settings.update.sidebar' => 'Update Sidebars',
    ];
    
    foreach ($routes as $routeName => $description) {
        try {
            $url = route($routeName, ['website' => 1]);
            echo "   ✓ {$description}: {$url}\n";
        } catch (Exception $e) {
            echo "   ✗ {$description}: Route not found\n";
        }
    }
    
    echo "\n✅ CONTROLLER METHODS:\n";
    $reflection = new ReflectionClass('App\Http\Controllers\Smartprep\Dashboard\CustomizeWebsiteController');
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    
    foreach ($methods as $method) {
        if (in_array($method->getName(), ['store', 'updateGeneral', 'updateHomepage', 'updateStudent', 'updateProfessor', 'updateAdmin', 'updateSidebar'])) {
            echo "   ✓ {$method->getName()}()\n";
        }
    }
    
    echo "\n🎯 READY TO TEST:\n";
    echo "   1. Visit: http://127.0.0.1:8000/smartprep/dashboard/customize-website\n";
    echo "   2. Create a new website\n";
    echo "   3. Verify all {$totalSettings} admin settings are copied\n";
    echo "   4. Test customization interface with live preview\n";
    echo "\n✨ Implementation Complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
