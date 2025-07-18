<?php

require_once 'bootstrap/app.php';

try {
    // Test form requirements table
    $requirements = DB::table('form_requirements')->get();
    
    echo "Form Requirements Count: " . $requirements->count() . "\n\n";
    
    if ($requirements->count() > 0) {
        echo "Sample Form Requirements:\n";
        foreach ($requirements->take(5) as $req) {
            echo "ID: {$req->id}\n";
            echo "Field Name: {$req->field_name}\n";
            echo "Field Label: {$req->field_label}\n";
            echo "Field Type: {$req->field_type}\n";
            echo "Program Type: {$req->program_type}\n";
            echo "Is Required: " . ($req->is_required ? 'Yes' : 'No') . "\n";
            echo "Is Active: " . ($req->is_active ? 'Yes' : 'No') . "\n";
            echo "Sort Order: " . ($req->sort_order ?? 'null') . "\n";
            echo "---\n";
        }
    } else {
        echo "No form requirements found.\n";
    }
    
    // Test for modular program requirements
    echo "\nModular Program Requirements:\n";
    $modularReqs = DB::table('form_requirements')
        ->where('program_type', 'modular')
        ->orWhere('program_type', 'both')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
        
    foreach ($modularReqs as $req) {
        echo "- {$req->field_label} ({$req->field_name}) - Type: {$req->field_type}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
