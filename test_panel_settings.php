<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UiSetting;

try {
    echo "Checking admin panel settings to copy to clients...\n\n";
    
    $sections = [
        'general' => 'General Settings',
        'navbar' => 'Navigation Bar',
        'branding' => 'Branding',
        'homepage' => 'Homepage',
        'student_portal' => 'Student Portal',
        'professor_panel' => 'Professor Panel',
        'admin_panel' => 'Admin Panel',
        'student_sidebar' => 'Student Sidebar',
        'professor_sidebar' => 'Professor Sidebar',
        'admin_sidebar' => 'Admin Sidebar',
    ];
    
    $totalSettings = 0;
    
    foreach ($sections as $section => $label) {
        $settings = UiSetting::getSection($section);
        $count = count($settings);
        $totalSettings += $count;
        
        echo "✓ {$label}: {$count} settings\n";
        
        if ($count > 0) {
            echo "  Sample settings:\n";
            foreach ($settings->take(3) as $key => $value) {
                $displayValue = strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value;
                echo "    - {$key}: {$displayValue}\n";
            }
        }
        echo "\n";
    }
    
    echo "Total settings to copy: {$totalSettings}\n";
    echo "\nController is now ready to copy all panel customizations!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
