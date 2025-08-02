<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking announcement settings and data...\n";

// Check if announcement management is enabled
$setting = \App\Models\AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->first();
echo "Professor announcement management enabled: " . ($setting ? $setting->setting_value : 'not set') . "\n";

// Check total announcements
$totalAnnouncements = \App\Models\Announcement::count();
echo "Total announcements in database: " . $totalAnnouncements . "\n";

// Check active announcements
$activeAnnouncements = \App\Models\Announcement::where('is_active', true)->where('is_published', true)->count();
echo "Active and published announcements: " . $activeAnnouncements . "\n";

// Get first few announcements for debugging
$announcements = \App\Models\Announcement::where('is_active', true)->where('is_published', true)->take(3)->get();
foreach($announcements as $ann) {
    echo "Announcement: " . $ann->title . " - Target Scope: " . $ann->target_scope . "\n";
}

// Check if there are any professors in the system
$professorCount = \App\Models\Professor::count();
echo "Total professors: " . $professorCount . "\n";

// Check if current professor session exists
session_start();
if (isset($_SESSION['professor_id'])) {
    echo "Professor ID in session: " . $_SESSION['professor_id'] . "\n";
    
    $professor = \App\Models\Professor::find($_SESSION['professor_id']);
    if ($professor) {
        $programs = $professor->programs;
        echo "Professor programs: " . $programs->count() . "\n";
        
        // Test the announcement query directly
        $programIds = $programs->pluck('program_id')->toArray();
        echo "Program IDs: " . implode(', ', $programIds) . "\n";
    }
} else {
    echo "No professor session found\n";
}
