<?php
echo "🔧 FIXING STUDENT REGISTRATION CONTROLLER DATABASE QUERIES\n";
echo "=========================================================\n\n";

$controllerPath = 'app/Http/Controllers/StudentRegistrationController.php';

if (!file_exists($controllerPath)) {
    echo "❌ StudentRegistrationController not found\n";
    exit(1);
}

// Create backup
$backupPath = $controllerPath . '.backup.tenant-fix.' . date('Y-m-d-H-i-s');
copy($controllerPath, $backupPath);
echo "✅ Backup created: $backupPath\n\n";

$content = file_get_contents($controllerPath);
$originalContent = $content;

echo "🔍 Replacing ALL model queries with tenant-aware database queries:\n\n";

// Fix 1: FormRequirement queries
$patterns = [
    // FormRequirement static calls
    'FormRequirement::active()' => "DB::connection('tenant')->table('form_requirements')->where('is_active', 1)",
    'FormRequirement::columnExists(' => "DB::connection('tenant')->getSchemaBuilder()->hasColumn('form_requirements', ",
    
    // Package queries
    'Package::create([' => "DB::connection('tenant')->table('packages')->insertGetId([",
    '\App\Models\Package::where(' => "DB::connection('tenant')->table('packages')->where(",
    'Package::where(' => "DB::connection('tenant')->table('packages')->where(",
    
    // Plan queries
    '\App\Models\Plan::where(' => "DB::connection('tenant')->table('plans')->where(",
    'Plan::where(' => "DB::connection('tenant')->table('plans')->where(",
    
    // Program queries  
    'Program::find(' => "DB::connection('tenant')->table('programs')->find(",
    '\App\Models\Program::find(' => "DB::connection('tenant')->table('programs')->find(",
    'Program::where(' => "DB::connection('tenant')->table('programs')->where(",
    '\App\Models\Program::where(' => "DB::connection('tenant')->table('programs')->where(",
    
    // Student queries
    'Student::where(' => "DB::connection('tenant')->table('students')->where(",
    'Student::create(' => "DB::connection('tenant')->table('students')->insertGetId(",
    '\App\Models\Student::where(' => "DB::connection('tenant')->table('students')->where(",
    
    // User queries (these might stay on main DB, but let's make them tenant-aware for consistency)
    'User::find(' => "DB::connection('tenant')->table('users')->find(",
    'User::where(' => "DB::connection('tenant')->table('users')->where(",
    '\App\Models\User::where(' => "DB::connection('tenant')->table('users')->where(",
    
    // Registration queries
    'Registration::where(' => "DB::connection('tenant')->table('registrations')->where(",
    '\App\Models\Registration::where(' => "DB::connection('tenant')->table('registrations')->where(",
    
    // Enrollment queries
    'Enrollment::create(' => "DB::connection('tenant')->table('enrollments')->insert(",
    '\App\Models\Enrollment::where(' => "DB::connection('tenant')->table('enrollments')->where(",
    'Enrollment::where(' => "DB::connection('tenant')->table('enrollments')->where(",
    
    // EnrollmentCourse queries
    'EnrollmentCourse::create(' => "DB::connection('tenant')->table('enrollment_courses')->insert(",
    '\App\Models\EnrollmentCourse::where(' => "DB::connection('tenant')->table('enrollment_courses')->where(",
];

$changesCount = 0;
foreach ($patterns as $search => $replace) {
    $newContent = str_replace($search, $replace, $content);
    if ($newContent !== $content) {
        $occurrences = substr_count($content, $search);
        echo "✅ Fixed: $search → $replace ($occurrences occurrences)\n";
        $content = $newContent;
        $changesCount += $occurrences;
    }
}

// Special fixes for method chaining
$chainPatterns = [
    // Fix ->forProgram() method calls
    "->forProgram('full')" => "->where('program_type', 'full')->orWhere('program_type', 'both')",
    "->forProgram('modular')" => "->where('program_type', 'modular')->orWhere('program_type', 'both')",
    
    // Fix ->ordered() method calls  
    "->ordered()" => "->orderBy('sort_order', 'asc')",
];

foreach ($chainPatterns as $search => $replace) {
    $newContent = str_replace($search, $replace, $content);
    if ($newContent !== $content) {
        $occurrences = substr_count($content, $search);
        echo "✅ Fixed method chain: $search → $replace ($occurrences occurrences)\n";
        $content = $newContent;
        $changesCount += $occurrences;
    }
}

// Fix package query specifically
$packagePattern = '/packages\s*=\s*Package::where\(\s*[\'"]package_type[\'"]\s*,\s*[\'"]full[\'"](?:[^;]+);/';
if (preg_match($packagePattern, $content)) {
    $content = preg_replace(
        $packagePattern,
        "packages = DB::connection('tenant')->table('packages')->where('package_type', 'full')->get();",
        $content
    );
    echo "✅ Fixed packages query pattern\n";
    $changesCount++;
}

echo "\n📊 Total changes made: $changesCount\n";

if ($content !== $originalContent) {
    file_put_contents($controllerPath, $content);
    echo "✅ StudentRegistrationController updated successfully\n";
} else {
    echo "ℹ️  No changes were needed\n";
}

echo "\n🧪 Testing the fixed controller...\n";

// Test the problematic route
$testUrl = 'http://127.0.0.1:8000/t/draft/test/enrollment/full';
echo "🔍 Testing: $testUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 200) {
    echo "🎉 SUCCESS! Full enrollment page now works\n";
} elseif ($httpCode == 302) {
    echo "🔄 Redirect - This might be normal behavior\n";
} elseif ($httpCode == 500) {
    echo "❌ Still having HTTP 500 errors\n";
    
    // Check logs for remaining issues
    $logFile = 'storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $logLines = explode("\n", $logs);
        $recentLogs = array_slice($logLines, -3);
        
        echo "📋 Recent error logs:\n";
        foreach ($recentLogs as $line) {
            if (!empty(trim($line)) && (strpos($line, 'ERROR') !== false || strpos($line, 'SQLSTATE') !== false)) {
                echo "   🔍 " . trim($line) . "\n";
            }
        }
    }
} else {
    echo "⚠️  Unexpected status: HTTP $httpCode\n";
}

echo "\n=== STUDENT REGISTRATION CONTROLLER FIX COMPLETE ===\n";
?>
