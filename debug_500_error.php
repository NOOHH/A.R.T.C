<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== DEBUGGING 500 ERROR ISSUE ===\n\n";

try {
    echo "1. CHECKING CLIENT AND TENANT RELATIONSHIP:\n";
    
    // Check clients table
    $clients = DB::table('clients')->get();
    echo "Clients found: " . $clients->count() . "\n";
    
    foreach ($clients as $client) {
        echo "  Client ID: {$client->id}\n";
        echo "  Name: {$client->name}\n";
        echo "  Slug: {$client->slug}\n";
        echo "  Domain: {$client->domain}\n";
        echo "  DB Name: " . ($client->db_name ?? 'NOT SET') . "\n";
        echo "  User ID: {$client->user_id}\n";
        echo "  ---\n";
    }
    
    echo "\n2. CHECKING TENANTS TABLE:\n";
    
    // Check tenants table
    $tenants = DB::table('tenants')->get();
    echo "Tenants found: " . $tenants->count() . "\n";
    
    foreach ($tenants as $tenant) {
        echo "  Tenant ID: {$tenant->id}\n";
        echo "  Name: {$tenant->name}\n";
        echo "  Slug: {$tenant->slug}\n";
        echo "  Domain: {$tenant->domain}\n";
        echo "  Database: {$tenant->database}\n";
        echo "  ---\n";
    }
    
    echo "\n3. CHECKING DATABASE EXISTENCE:\n";
    
    // Check which databases actually exist
    $databases = DB::select('SHOW DATABASES');
    $dbNames = array_column($databases, 'Database');
    
    echo "Existing databases:\n";
    foreach ($dbNames as $dbName) {
        if (strpos($dbName, 'smartprep_') === 0) {
            echo "  - $dbName\n";
            
            // Check if settings table exists in this database
            try {
                $tables = DB::select("SHOW TABLES FROM `$dbName` LIKE 'settings'");
                if (count($tables) > 0) {
                    echo "    ✓ Has settings table\n";
                    
                    // Count settings
                    $settingsCount = DB::select("SELECT COUNT(*) as count FROM `$dbName`.settings")[0]->count;
                    echo "    ✓ Settings count: $settingsCount\n";
                } else {
                    echo "    ✗ Missing settings table\n";
                }
            } catch (Exception $e) {
                echo "    ✗ Error checking: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n4. CHECKING URL PARAMETERS:\n";
    echo "The error shows website ID 10 in the URL\n";
    echo "Let's check if client ID 10 exists:\n";
    
    $client10 = DB::table('clients')->where('id', 10)->first();
    if ($client10) {
        echo "  ✓ Client ID 10 exists:\n";
        echo "    Name: {$client10->name}\n";
        echo "    Slug: {$client10->slug}\n";
        echo "    Domain: {$client10->domain}\n";
        echo "    DB Name: " . ($client10->db_name ?? 'NOT SET') . "\n";
        
        // Find corresponding tenant
        $tenant = DB::table('tenants')->where('slug', $client10->slug)->first();
        if ($tenant) {
            echo "  ✓ Corresponding tenant found:\n";
            echo "    Database: {$tenant->database}\n";
            
            // Check if this database exists and has settings table
            $dbExists = in_array($tenant->database, $dbNames);
            echo "    Database exists: " . ($dbExists ? 'YES' : 'NO') . "\n";
            
            if ($dbExists) {
                try {
                    $settingsTables = DB::select("SHOW TABLES FROM `{$tenant->database}` LIKE 'settings'");
                    $hasSettings = count($settingsTables) > 0;
                    echo "    Settings table exists: " . ($hasSettings ? 'YES' : 'NO') . "\n";
                    
                    if (!$hasSettings) {
                        echo "  ❌ THIS IS THE PROBLEM: Database exists but missing settings table\n";
                    }
                } catch (Exception $e) {
                    echo "    Error checking settings table: " . $e->getMessage() . "\n";
                }
            } else {
                echo "  ❌ THIS IS THE PROBLEM: Tenant database doesn't exist\n";
            }
        } else {
            echo "  ✗ No corresponding tenant found for slug: {$client10->slug}\n";
        }
    } else {
        echo "  ✗ Client ID 10 not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSIS COMPLETE ===\n";
