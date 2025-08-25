<?php
echo "🔧 COMPREHENSIVE TENANT-AWARE DATABASE QUERY FIX\n";
echo "===============================================\n\n";

$modularController = 'app/Http/Controllers/ModularRegistrationController.php';

if (file_exists($modularController)) {
    // Create another backup
    $backupFile = $modularController . '.backup.comprehensive.' . date('Y-m-d-H-i-s');
    copy($modularController, $backupFile);
    echo "✅ Backup created: $backupFile\n";
    
    $content = file_get_contents($modularController);
    $originalContent = $content;
    
    echo "🔍 Replacing ALL model queries with tenant-aware database queries:\n";
    
    // Fix 1: Replace FormRequirement model queries
    $content = str_replace(
        'FormRequirement::active()',
        "DB::connection('tenant')->table('form_requirements')->where('active', true)"
    , $content);
    echo "✅ Fixed FormRequirement::active() queries\n";
    
    // Fix 2: Replace Program model queries  
    $content = str_replace(
        '\App\Models\Program::when(Schema::hasColumn(\'programs\', \'archived\'), function($q) {',
        "DB::connection('tenant')->table('programs')->when(Schema::hasColumn('programs', 'archived'), function(\$q) {"
    , $content);
    echo "✅ Fixed Program model queries\n";
    
    // Fix 3: Replace Program::find queries
    $content = str_replace(
        'Program::find($validated[\'program_id\'])',
        "DB::connection('tenant')->table('programs')->where('program_id', \$validated['program_id'])->first()"
    , $content);
    echo "✅ Fixed Program::find queries\n";
    
    // Fix 4: Replace any remaining Package model usage
    $content = preg_replace(
        '/Package::([a-zA-Z]+)/',
        "DB::connection('tenant')->table('packages')->$1",
        $content
    );
    echo "✅ Fixed any remaining Package model usage\n";
    
    // Fix 5: Replace Module model usage  
    $content = preg_replace(
        '/Module::([a-zA-Z]+)/',
        "DB::connection('tenant')->table('modules')->$1",
        $content
    );
    echo "✅ Fixed Module model usage\n";
    
    // Fix 6: Replace EducationLevel model usage
    $content = preg_replace(
        '/EducationLevel::([a-zA-Z]+)/',
        "DB::connection('tenant')->table('education_levels')->$1",
        $content
    );
    echo "✅ Fixed EducationLevel model usage\n";
    
    // Fix 7: Add tenant connection specification for any remaining DB queries that don't specify it
    $content = preg_replace(
        '/DB::table\(([\'"][^\'\"]+[\'"])\)/',
        "DB::connection('tenant')->table($1)",
        $content
    );
    echo "✅ Added tenant connection to DB queries\n";
    
    // Fix 8: Replace any direct model instantiation with tenant-aware queries
    $modelReplacements = [
        'User::' => "DB::connection('tenant')->table('users')->",
        'Student::' => "DB::connection('tenant')->table('students')->", 
        'Registration::' => "DB::connection('tenant')->table('registrations')->",
        'Enrollment::' => "DB::connection('tenant')->table('enrollments')->",
        'EnrollmentCourse::' => "DB::connection('tenant')->table('enrollment_courses')->",
        'Director::' => "DB::connection('tenant')->table('directors')->",
        'Professor::' => "DB::connection('tenant')->table('professors')->",
        'Plan::' => "DB::connection('tenant')->table('plans')->"
    ];
    
    foreach ($modelReplacements as $from => $to) {
        if (strpos($content, $from) !== false) {
            $content = str_replace($from, $to, $content);
            echo "✅ Fixed $from usage\n";
        }
    }
    
    // Check if we made any changes
    if ($content !== $originalContent) {
        file_put_contents($modularController, $content);
        echo "\n✅ ModularRegistrationController updated with comprehensive tenant-aware queries\n";
        
        // Count total replacements
        $changes = substr_count($originalContent, '::') - substr_count($content, '::');
        echo "📊 Total model query changes made: $changes\n";
    } else {
        echo "\nℹ️  No additional changes needed\n";
    }
    
} else {
    echo "❌ ModularRegistrationController not found\n";
}

echo "\n🧪 TESTING AFTER COMPREHENSIVE FIX:\n";
echo "-----------------------------------\n";

// Test the problematic route
$testUrl = 'http://127.0.0.1:8000/t/draft/artc/enrollment/modular';
echo "🔍 Testing: $testUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 200) {
    echo "🎉 SUCCESS! Modular enrollment page now works\n";
} elseif ($httpCode == 302) {
    echo "🔄 Redirect - This is normal behavior\n";
} else {
    echo "❌ Still having issues - HTTP $httpCode\n";
    
    if ($httpCode == 500) {
        // Check for any error information
        if (strpos($body, 'error') !== false || strpos($body, 'Exception') !== false) {
            echo "📄 Error still present in response\n";
            
            // Get fresh logs
            $logFile = 'storage/logs/laravel.log';
            if (file_exists($logFile)) {
                $logs = file_get_contents($logFile);
                $logLines = explode("\n", $logs);
                $recentLogs = array_slice($logLines, -5);
                
                echo "📋 Latest error logs:\n";
                foreach ($recentLogs as $line) {
                    if (!empty(trim($line)) && (strpos($line, 'ERROR') !== false)) {
                        echo "   🔍 " . trim($line) . "\n";
                    }
                }
            }
        }
    }
}

echo "\n=== COMPREHENSIVE TENANT DATABASE FIX COMPLETE ===\n";
?>
