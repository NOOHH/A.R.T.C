<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Announcement Debug Report\n";
echo "=========================\n";

// Check setting
$setting = \App\Models\AdminSetting::where('setting_key', 'professor_announcement_management_enabled')->value('setting_value');
echo "Professor announcement enabled: " . ($setting ?? 'not set') . "\n\n";

// Check announcements
$announcements = \App\Models\Announcement::where('is_active', true)->where('is_published', true)->get();
echo "Active announcements: " . $announcements->count() . "\n";

foreach($announcements as $ann) {
    echo "\nAnnouncement: " . $ann->title . "\n";
    echo "  Target Scope: " . $ann->target_scope . "\n";
    echo "  Target Users: " . (is_array($ann->target_users) ? json_encode($ann->target_users) : ($ann->target_users ?? 'null')) . "\n";
    echo "  Target Programs: " . (is_array($ann->target_programs) ? json_encode($ann->target_programs) : ($ann->target_programs ?? 'null')) . "\n";
    echo "  Published: " . ($ann->is_published ? 'Yes' : 'No') . "\n";
    echo "  Active: " . ($ann->is_active ? 'Yes' : 'No') . "\n";
}

// Test professor data
$professor = \App\Models\Professor::first();
if ($professor) {
    echo "\nProfessor found: " . $professor->professor_name . "\n";
    $programs = $professor->programs;
    echo "Professor programs: " . $programs->count() . "\n";
    
    $programIds = $programs->pluck('program_id')->toArray();
    echo "Program IDs: " . implode(', ', $programIds) . "\n";
    
    // Test if announcements would be fetched
    $testAnnouncements = \App\Models\Announcement::where('is_active', true)
        ->where('is_published', true)
        ->where(function($q) {
            $q->whereNull('publish_date')
              ->orWhere('publish_date', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('expire_date')
              ->orWhere('expire_date', '>', now());
        })
        ->where(function($mainQuery) use ($programIds) {
            $mainQuery->where('target_scope', 'all');
            $mainQuery->orWhere(function($specificQuery) use ($programIds) {
                $specificQuery->where('target_scope', 'specific');
                $specificQuery->where(function($userQuery) {
                    $userQuery->whereJsonContains('target_users', 'professors')
                             ->orWhereNull('target_users');
                });
            });
        })
        ->get();
        
    echo "\nAnnouncements that would be shown to professor: " . $testAnnouncements->count() . "\n";
}
