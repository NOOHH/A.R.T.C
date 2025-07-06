<?php
echo "=== FINAL ENROLLMENT FIXES VERIFICATION ===\n\n";

// Test 1: Check if package field is correctly used
echo "1. CHECKING PACKAGE FIELD USAGE:\n";
$modularFile = 'resources/views/registration/Modular_enrollment.blade.php';
$fullFile = 'resources/views/registration/Full_enrollment.blade.php';

if (file_exists($modularFile)) {
    $content = file_get_contents($modularFile);
    
    // Check if using correct field (amount instead of price)
    $amountCount = substr_count($content, '$package->amount');
    $priceCount = substr_count($content, '$package->price');
    
    echo "  - Modular_enrollment.blade.php:\n";
    echo "    ✓ Uses \$package->amount: $amountCount times\n";
    echo "    ✗ Uses \$package->price: $priceCount times\n";
    
    if ($priceCount === 0 && $amountCount > 0) {
        echo "    ✅ FIXED: Now using correct 'amount' field\n";
    } else {
        echo "    ❌ ISSUE: Still using incorrect 'price' field\n";
    }
    
    // Check for selectPackage function with 3 parameters
    if (strpos($content, 'function selectPackage(packageId, packageName, packagePrice)') !== false) {
        echo "    ✅ FIXED: selectPackage function accepts price parameter\n";
    } else {
        echo "    ❌ ISSUE: selectPackage function missing price parameter\n";
    }
    
    // Check for data-package-price attribute
    if (strpos($content, 'data-package-price') !== false) {
        echo "    ✅ FIXED: data-package-price attribute added\n";
    } else {
        echo "    ❌ ISSUE: data-package-price attribute missing\n";
    }
    
    // Check for selectedPackagePrice element
    if (strpos($content, 'selectedPackagePrice') !== false) {
        echo "    ✅ FIXED: selectedPackagePrice element added\n";
    } else {
        echo "    ❌ ISSUE: selectedPackagePrice element missing\n";
    }
    
    // Check for updatePaymentStepInfo function
    if (strpos($content, 'function updatePaymentStepInfo') !== false) {
        echo "    ✅ FIXED: updatePaymentStepInfo function added\n";
    } else {
        echo "    ❌ ISSUE: updatePaymentStepInfo function missing\n";
    }
    
    // Check for payment step package summary
    if (strpos($content, 'paymentPackageSummary') !== false) {
        echo "    ✅ FIXED: Payment step package summary added\n";
    } else {
        echo "    ❌ ISSUE: Payment step package summary missing\n";
    }
    
} else {
    echo "  ❌ Modular_enrollment.blade.php not found\n";
}

echo "\n";

// Test 2: Check Full enrollment consistency
echo "2. CHECKING FULL ENROLLMENT CONSISTENCY:\n";
if (file_exists($fullFile)) {
    $fullContent = file_get_contents($fullFile);
    
    // Check if using correct field (amount instead of price)
    $fullAmountCount = substr_count($fullContent, '$package->amount');
    $fullPriceCount = substr_count($fullContent, '$package->price');
    
    echo "  - Full_enrollment.blade.php:\n";
    echo "    ✓ Uses \$package->amount: $fullAmountCount times\n";
    echo "    ✗ Uses \$package->price: $fullPriceCount times\n";
    
    if ($fullPriceCount === 0 && $fullAmountCount > 0) {
        echo "    ✅ CONSISTENT: Using correct 'amount' field\n";
    } else {
        echo "    ⚠️  WARNING: Check field consistency\n";
    }
    
    // Check for selectPackage function with 3 parameters
    if (strpos($fullContent, 'function selectPackage(packageId, packageName, packagePrice)') !== false) {
        echo "    ✅ CONSISTENT: selectPackage function accepts price parameter\n";
    } else {
        echo "    ⚠️  WARNING: selectPackage function may be inconsistent\n";
    }
    
} else {
    echo "  ❌ Full_enrollment.blade.php not found\n";
}

echo "\n";

// Test 3: Package Model Check
echo "3. CHECKING PACKAGE MODEL:\n";
$packageModel = 'app/Models/Package.php';
if (file_exists($packageModel)) {
    $modelContent = file_get_contents($packageModel);
    
    if (strpos($modelContent, "'amount'") !== false) {
        echo "  ✅ Package model includes 'amount' field\n";
    } else {
        echo "  ⚠️  WARNING: Package model may not include 'amount' field\n";
    }
    
    // Check for fillable fields
    if (strpos($modelContent, 'fillable') !== false) {
        echo "  ✅ Package model has fillable array defined\n";
    } else {
        echo "  ⚠️  WARNING: Package model missing fillable array\n";
    }
    
} else {
    echo "  ❌ Package.php model not found\n";
}

echo "\n";

// Test 4: Admin Package Controller Check
echo "4. CHECKING ADMIN PACKAGE CONTROLLER:\n";
$adminController = 'app/Http/Controllers/Admin/PackageController.php';
if (file_exists($adminController)) {
    echo "  ✅ Admin PackageController exists\n";
    
    $controllerContent = file_get_contents($adminController);
    
    if (strpos($controllerContent, 'function index') !== false) {
        echo "  ✅ Index method exists\n";
    }
    
    if (strpos($controllerContent, 'function create') !== false) {
        echo "  ✅ Create method exists\n";
    }
    
    if (strpos($controllerContent, 'function store') !== false) {
        echo "  ✅ Store method exists\n";
    }
    
    if (strpos($controllerContent, 'function edit') !== false) {
        echo "  ✅ Edit method exists\n";
    }
    
    if (strpos($controllerContent, 'function update') !== false) {
        echo "  ✅ Update method exists\n";
    }
    
    if (strpos($controllerContent, 'function destroy') !== false) {
        echo "  ✅ Destroy method exists\n";
    }
    
} else {
    echo "  ❌ Admin PackageController not found\n";
}

echo "\n";

// Test 5: JavaScript Function Verification
echo "5. CHECKING JAVASCRIPT FUNCTIONS:\n";
if (file_exists($modularFile)) {
    $content = file_get_contents($modularFile);
    
    $jsChecks = [
        'function nextStep()' => 'nextStep function',
        'function prevStep()' => 'prevStep function', 
        'function selectPackage(' => 'selectPackage function',
        'function selectPaymentMethod(' => 'selectPaymentMethod function',
        'function updatePaymentStepInfo(' => 'updatePaymentStepInfo function',
        'function validateModuleSelection(' => 'validateModuleSelection function',
        'function animateStepTransition(' => 'animateStepTransition function',
        'sessionStorage.setItem' => 'sessionStorage usage'
    ];
    
    foreach ($jsChecks as $search => $description) {
        if (strpos($content, $search) !== false) {
            echo "  ✅ $description exists\n";
        } else {
            echo "  ❌ $description missing\n";
        }
    }
}

echo "\n";

// Test 6: Step Navigation Logic
echo "6. CHECKING STEP NAVIGATION LOGIC:\n";
if (file_exists($modularFile)) {
    $content = file_get_contents($modularFile);
    
    // Check if step logic considers logged-in users
    if (strpos($content, 'isUserLoggedIn') !== false) {
        echo "  ✅ Step navigation considers user login status\n";
    } else {
        echo "  ❌ Step navigation missing user login status check\n";
    }
    
    // Check for step skipping logic
    if (strpos($content, 'Skip account registration') !== false || strpos($content, 'skipping to payment') !== false) {
        echo "  ✅ Step skipping logic implemented\n";
    } else {
        echo "  ❌ Step skipping logic missing\n";
    }
    
    // Check for progress bar updates
    if (strpos($content, 'updateProgress') !== false) {
        echo "  ✅ Progress bar update function exists\n";
    } else {
        echo "  ❌ Progress bar update function missing\n";
    }
}

echo "\n";

// Test 7: UI/CSS Improvements
echo "7. CHECKING UI/CSS IMPROVEMENTS:\n";
if (file_exists($modularFile)) {
    $content = file_get_contents($modularFile);
    
    // Check for carousel implementation
    if (strpos($content, 'packages-carousel') !== false) {
        echo "  ✅ Package carousel implemented\n";
    } else {
        echo "  ❌ Package carousel missing\n";
    }
    
    // Check for centered container
    if (strpos($content, 'registration-container') !== false) {
        echo "  ✅ Centered registration container exists\n";
    } else {
        echo "  ❌ Centered registration container missing\n";
    }
    
    // Check for step transitions
    if (strpos($content, 'animateStepTransition') !== false) {
        echo "  ✅ Step transition animations implemented\n";
    } else {
        echo "  ❌ Step transition animations missing\n";
    }
    
    // Check for responsive styles
    if (strpos($content, '@media') !== false) {
        echo "  ✅ Responsive styles included\n";
    } else {
        echo "  ⚠️  WARNING: Check if responsive styles are included\n";
    }
}

echo "\n";

// Test 8: Form Validation
echo "8. CHECKING FORM VALIDATION:\n";
if (file_exists($modularFile)) {
    $content = file_get_contents($modularFile);
    
    // Check for module selection validation
    if (strpos($content, 'hasModulesSelected') !== false) {
        echo "  ✅ Module selection validation exists\n";
    } else {
        echo "  ❌ Module selection validation missing\n";
    }
    
    // Check for terms checkbox validation
    if (strpos($content, 'termsAccepted') !== false) {
        echo "  ✅ Terms checkbox validation exists\n";
    } else {
        echo "  ❌ Terms checkbox validation missing\n";
    }
    
    // Check for program selection validation
    if (strpos($content, 'programSelected') !== false) {
        echo "  ✅ Program selection validation exists\n";
    } else {
        echo "  ❌ Program selection validation missing\n";
    }
    
    // Check for enroll button state management
    if (strpos($content, 'enrollBtn.disabled') !== false) {
        echo "  ✅ Enroll button state management exists\n";
    } else {
        echo "  ❌ Enroll button state management missing\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "Review the results above to ensure all fixes are properly implemented.\n";
echo "✅ = Fixed/Working\n";
echo "❌ = Missing/Not Working\n";
echo "⚠️  = Warning/Check Needed\n";
?>
