<?php
// Database check script
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Get database configuration
$config = require 'config/database.php';
$dbConfig = $config['connections']['mysql'];

$capsule = new Capsule;
$capsule->addConnection($dbConfig);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Test connection
    $pdo = $capsule->getConnection()->getPdo();
    echo "✓ Database connection successful\n";
    
    // Get database name
    $dbName = $dbConfig['database'];
    echo "Database: {$dbName}\n\n";
    
    // Show all tables
    $tables = $capsule->getConnection()->select("SHOW TABLES");
    echo "=== DATABASE TABLES ===\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- {$tableName}\n";
    }
    echo "\n";
    
    // Check key tables structure
    $keyTables = ['announcements', 'admin_settings', 'directors', 'admins', 'professors', 'assignment_submissions'];
    
    foreach ($keyTables as $tableName) {
        try {
            echo "=== {$tableName} TABLE STRUCTURE ===\n";
            $columns = $capsule->getConnection()->select("DESCRIBE {$tableName}");
            foreach ($columns as $column) {
                echo "- {$column->Field} ({$column->Type}) " . 
                     ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . 
                     ($column->Key ? " {$column->Key}" : '') . 
                     ($column->Default ? " DEFAULT {$column->Default}" : '') . "\n";
            }
            
            // Show sample data for announcements
            if ($tableName === 'announcements') {
                echo "\nSample announcements data:\n";
                $announcements = $capsule->getConnection()->select("SELECT id, title, admin_id, professor_id, created_at FROM announcements ORDER BY id DESC LIMIT 5");
                foreach ($announcements as $announcement) {
                    echo "ID: {$announcement->id}, Title: {$announcement->title}, Admin ID: {$announcement->admin_id}, Professor ID: {$announcement->professor_id}, Created: {$announcement->created_at}\n";
                }
            }
            
            // Show sample data for directors
            if ($tableName === 'directors') {
                echo "\nDirectors data:\n";
                $directors = $capsule->getConnection()->select("SELECT * FROM directors LIMIT 5");
                foreach ($directors as $director) {
                    echo "ID: {$director->director_id}, Name: {$director->first_name} {$director->last_name}, Admin ID: {$director->admin_id}\n";
                }
            }
            
            // Show sample data for admin_settings
            if ($tableName === 'admin_settings') {
                echo "\nAdmin settings (announcement related):\n";
                $settings = $capsule->getConnection()->select("SELECT * FROM admin_settings WHERE setting_name LIKE '%announcement%'");
                foreach ($settings as $setting) {
                    echo "Setting: {$setting->setting_name}, Value: {$setting->setting_value}, Whitelist: {$setting->whitelisted_users}\n";
                }
            }
            
            echo "\n";
        } catch (Exception $e) {
            echo "Error checking {$tableName}: " . $e->getMessage() . "\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}
