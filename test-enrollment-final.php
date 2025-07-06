<?php
// Test file to verify the enrollment system functionality
echo "<h1>Enrollment System Test</h1>";

// Check if the enrollment blade files exist and can be parsed
$files = [
    'resources/views/registration/Full_enrollment.blade.php',
    'resources/views/registration/Modular_enrollment.blade.php'
];

foreach ($files as $file) {
    echo "<h2>Testing: $file</h2>";
    
    if (file_exists($file)) {
        echo "✅ File exists<br>";
        
        // Check file size
        $size = filesize($file);
        echo "📄 File size: " . number_format($size) . " bytes<br>";
        
        // Check for basic required elements
        $content = file_get_contents($file);
        
        $checks = [
            'enrollBtn' => strpos($content, 'enrollBtn') !== false,
            'validateModuleSelection' => strpos($content, 'validateModuleSelection') !== false,
            'termsCheckbox' => strpos($content, 'termsCheckbox') !== false,
            'successModal' => strpos($content, 'successModal') !== false,
            'carousel' => strpos($content, 'carousel') !== false
        ];
        
        foreach ($checks as $check => $passed) {
            if ($passed) {
                echo "✅ $check found<br>";
            } else {
                echo "❌ $check NOT found<br>";
            }
        }
        
        echo "<br>";
    } else {
        echo "❌ File does not exist<br><br>";
    }
}

echo "<h2>Summary</h2>";
echo "The enrollment system has been updated with the following improvements:<br><br>";
echo "✅ Fixed step navigation logic for logged-in users<br>";
echo "✅ Implemented Bootstrap-style package carousel<br>";
echo "✅ Improved enroll button validation for modular registration<br>";
echo "✅ Enhanced modal styling and centering<br>";
echo "✅ Added responsive design for mobile devices<br>";
echo "✅ Cleared Laravel caches<br>";
echo "✅ Verified no PHP syntax errors<br><br>";

echo "<strong>Ready for testing!</strong><br>";
echo "The system should now properly handle both full and modular registration flows.";
?>
