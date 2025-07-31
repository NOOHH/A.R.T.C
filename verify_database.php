<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== DATABASE VERIFICATION ===\n";
    
    // Test database connection
    $connection = \Illuminate\Support\Facades\DB::connection();
    echo "✓ Database connected successfully\n";
    echo "Database: " . $connection->getDatabaseName() . "\n\n";
    
    // Check key tables
    echo "=== CHECKING KEY TABLES ===\n";
    
    $tables = ['announcements', 'admin_settings', 'directors', 'admins', 'professors', 'assignment_submissions'];
    
    foreach ($tables as $table) {
        try {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "✓ {$table}: {$count} records\n";
        } catch (Exception $e) {
            echo "✗ {$table}: Error - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== ANNOUNCEMENT SETTINGS ===\n";
    $settings = \Illuminate\Support\Facades\DB::table('admin_settings')
        ->where('setting_name', 'like', '%announcement%')
        ->get();
    
    foreach ($settings as $setting) {
        echo "Setting: {$setting->setting_name}\n";
        echo "Value: {$setting->setting_value}\n";
        echo "Whitelist: {$setting->whitelisted_users}\n\n";
    }
    
    echo "=== RECENT ANNOUNCEMENTS ===\n";
    $announcements = \Illuminate\Support\Facades\DB::table('announcements')
        ->select('id', 'title', 'admin_id', 'professor_id', 'created_at')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($announcements as $announcement) {
        echo "ID: {$announcement->id}, Title: {$announcement->title}, ";
        echo "Admin ID: {$announcement->admin_id}, Professor ID: {$announcement->professor_id}, ";
        echo "Created: {$announcement->created_at}\n";
    }
    
    echo "\n=== ASSIGNMENT SUBMISSIONS SAMPLE ===\n";
    $submissions = \Illuminate\Support\Facades\DB::table('assignment_submissions')
        ->select('id', 'files', 'original_filename', 'file_path')
        ->whereNotNull('files')
        ->limit(3)
        ->get();
        
    foreach ($submissions as $submission) {
        echo "Submission ID: {$submission->id}\n";
        echo "Files JSON: " . substr($submission->files ?? 'NULL', 0, 100) . "...\n";
        echo "Original filename: " . ($submission->original_filename ?? 'NULL') . "\n";
        echo "File path: " . ($submission->file_path ?? 'NULL') . "\n\n";
    }
    
    echo "=== VERIFICATION COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
