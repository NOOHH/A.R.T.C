<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Checking for roles in users table:\n";
    $roles = DB::table('users')->distinct()->pluck('role');
    foreach ($roles as $role) {
        echo "- $role\n";
    }
    
    echo "\nChecking admins table:\n";
    try {
        $adminColumns = DB::select('SHOW COLUMNS FROM admins');
        echo "Admins table exists with columns:\n";
        foreach ($adminColumns as $col) {
            echo "- {$col->Field}\n";
        }
        $adminCount = DB::table('admins')->count();
        echo "Total admins: $adminCount\n";
        
        if ($adminCount > 0) {
            $sample = DB::table('admins')->first();
            echo "Sample admin: ";
            foreach ($sample as $key => $value) {
                echo "$key: $value ";
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "Admins table error: " . $e->getMessage() . "\n";
    }
    
    echo "\nChecking directors table:\n";
    try {
        $directorColumns = DB::select('SHOW COLUMNS FROM directors');
        echo "Directors table exists with columns:\n";
        foreach ($directorColumns as $col) {
            echo "- {$col->Field}\n";
        }
        $directorCount = DB::table('directors')->count();
        echo "Total directors: $directorCount\n";
        
        if ($directorCount > 0) {
            $sample = DB::table('directors')->first();
            echo "Sample director: ";
            foreach ($sample as $key => $value) {
                echo "$key: $value ";
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "Directors table error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
