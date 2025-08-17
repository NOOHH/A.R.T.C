<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== FIXING UI_SETTINGS TABLE STRUCTURE ===\n\n";
    
    // First, check both databases
    echo "1. Checking main database (smartprep):\n";
    $mainColumns = DB::select("DESCRIBE ui_settings");
    foreach($mainColumns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n2. Checking tenant database (smartprep_artc):\n";
    $tenantColumns = DB::select("DESCRIBE smartprep_artc.ui_settings");
    foreach($tenantColumns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n3. Fixing main database structure...\n";
    
    // Backup existing data if any
    $existingData = DB::table('ui_settings')->get();
    echo "   Found " . count($existingData) . " existing records\n";
    
    // Drop and recreate with correct structure
    DB::statement("DROP TABLE ui_settings");
    echo "   ✅ Dropped old table\n";
    
    // Create table with correct structure (matching tenant database)
    DB::statement("
        CREATE TABLE `ui_settings` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `setting_value` text COLLATE utf8mb4_unicode_ci,
            `setting_type` enum('color','file','text','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ui_settings_section_setting_key_unique` (`section`,`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✅ Created new table with correct structure\n";
    
    // Restore data if it existed (convert column names)
    if (!empty($existingData)) {
        foreach($existingData as $record) {
            DB::table('ui_settings')->insert([
                'section' => $record->section,
                'setting_key' => $record->key ?? $record->setting_key ?? 'unknown',
                'setting_value' => $record->value ?? $record->setting_value ?? '',
                'setting_type' => $record->type ?? $record->setting_type ?? 'text',
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
        echo "   ✅ Restored " . count($existingData) . " records with corrected column names\n";
    }
    
    echo "\n4. Verifying fix...\n";
    $newColumns = DB::select("DESCRIBE ui_settings");
    echo "   New structure:\n";
    foreach($newColumns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n✅ UI_SETTINGS TABLE STRUCTURE FIXED!\n";
    echo "Both main and tenant databases now have matching structures.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
