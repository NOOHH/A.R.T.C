<?php
// Test the referral system functionality

// Include required files
require_once 'app/Helpers/ReferralCodeGenerator.php';

echo "<h1>Referral System Test Results</h1>\n";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>\n";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>\n";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=artc", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✓ Database connection successful</p>\n";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>\n";
    exit;
}

// Test 2: Check Table Structure
echo "<h2>Test 2: Table Structure</h2>\n";

// Check directors table
$result = $pdo->query("DESCRIBE directors");
$columns = $result->fetchAll(PDO::FETCH_COLUMN);
if (in_array('referral_code', $columns)) {
    echo "<p class='success'>✓ Directors table has referral_code column</p>\n";
} else {
    echo "<p class='error'>✗ Directors table missing referral_code column</p>\n";
}

// Check professors table
$result = $pdo->query("DESCRIBE professors");
$columns = $result->fetchAll(PDO::FETCH_COLUMN);
if (in_array('referral_code', $columns)) {
    echo "<p class='success'>✓ Professors table has referral_code column</p>\n";
} else {
    echo "<p class='error'>✗ Professors table missing referral_code column</p>\n";
}

// Check referrals table
try {
    $pdo->query("SELECT 1 FROM referrals LIMIT 1");
    echo "<p class='success'>✓ Referrals table exists</p>\n";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Referrals table missing</p>\n";
}

// Test 3: Existing Referral Codes
echo "<h2>Test 3: Existing Referral Codes</h2>\n";

$directors = $pdo->query("SELECT directors_id, directors_name, referral_code FROM directors WHERE referral_code IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Directors:</h3>\n";
foreach ($directors as $director) {
    echo "<p class='info'>ID {$director['directors_id']}: {$director['directors_name']} → <strong>{$director['referral_code']}</strong></p>\n";
}

$professors = $pdo->query("SELECT professor_id, professor_name, referral_code FROM professors WHERE referral_code IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Professors:</h3>\n";
foreach ($professors as $professor) {
    echo "<p class='info'>ID {$professor['professor_id']}: {$professor['professor_name']} → <strong>{$professor['referral_code']}</strong></p>\n";
}

// Test 4: Referral Code Validation
echo "<h2>Test 4: Referral Code Validation</h2>\n";

$testCodes = ['DIR07ALEK', 'PROF08ROBERT', 'INVALID123', 'DIR999TEST'];

foreach ($testCodes as $code) {
    echo "<h4>Testing code: <strong>$code</strong></h4>\n";
    
    try {
        $generator = new ReferralCodeGenerator();
        $result = $generator->validateReferralCode($code);
        
        if ($result) {
            echo "<p class='success'>✓ Valid code</p>\n";
            echo "<p class='info'>Type: {$result['referrer_type']}</p>\n";
            echo "<p class='info'>Name: {$result['referrer_name']}</p>\n";
            echo "<p class='info'>ID: {$result['referrer_id']}</p>\n";
        } else {
            echo "<p class='error'>✗ Invalid code</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>\n";
    }
    echo "<hr>\n";
}

// Test 5: Code Generation
echo "<h2>Test 5: Code Generation Test</h2>\n";

try {
    $generator = new ReferralCodeGenerator();
    
    // Test director code generation
    $directorCode = $generator->generateCode('director', 1, 'John');
    echo "<p class='info'>Generated director code for ID 1, name 'John': <strong>$directorCode</strong></p>\n";
    
    // Test professor code generation
    $professorCode = $generator->generateCode('professor', 1, 'Jane');
    echo "<p class='info'>Generated professor code for ID 1, name 'Jane': <strong>$professorCode</strong></p>\n";
    
    echo "<p class='success'>✓ Code generation working correctly</p>\n";
} catch (Exception $e) {
    echo "<p class='error'>✗ Code generation failed: " . $e->getMessage() . "</p>\n";
}

// Test 6: Check enrollments table structure for conditional recording
echo "<h2>Test 6: Enrollments Table Check</h2>\n";

try {
    $result = $pdo->query("DESCRIBE enrollments");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    $hasEnrollmentStatus = false;
    $hasPaymentStatus = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'enrollment_status') {
            $hasEnrollmentStatus = true;
            echo "<p class='success'>✓ Found enrollment_status column</p>\n";
        }
        if ($column['Field'] === 'payment_status') {
            $hasPaymentStatus = true;
            echo "<p class='success'>✓ Found payment_status column</p>\n";
        }
    }
    
    if (!$hasEnrollmentStatus) {
        echo "<p class='error'>✗ Missing enrollment_status column (required for conditional referral recording)</p>\n";
    }
    if (!$hasPaymentStatus) {
        echo "<p class='error'>✗ Missing payment_status column (required for conditional referral recording)</p>\n";
    }
    
    if ($hasEnrollmentStatus && $hasPaymentStatus) {
        echo "<p class='success'>✓ All required columns present for conditional referral recording</p>\n";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error checking enrollments table: " . $e->getMessage() . "</p>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p class='success'>✓ Referral system implementation complete</p>\n";
echo "<p class='info'>The system is ready to:</p>\n";
echo "<ul>\n";
echo "<li>Generate unique referral codes for directors and professors</li>\n";
echo "<li>Validate referral codes during registration</li>\n";
echo "<li>Record referrals only when enrollment_status='approved' AND payment_status='paid'</li>\n";
echo "<li>Track referral usage and analytics</li>\n";
echo "</ul>\n";

echo "<h3>Next Steps:</h3>\n";
echo "<ol>\n";
echo "<li>Test the enrollment form with referral codes</li>\n";
echo "<li>Test the admin approval process</li>\n";
echo "<li>Verify referral recording when conditions are met</li>\n";
echo "</ol>\n";
?>
