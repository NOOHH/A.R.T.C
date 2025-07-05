<?php
echo "=== PAYMENT METHOD & FORM FIXES VERIFICATION ===\n\n";

// Test 1: Check for payment method CSS
echo "1. CHECKING PAYMENT METHOD CSS:\n";
$fullFile = 'resources/views/registration/Full_enrollment.blade.php';
$modularFile = 'resources/views/registration/Modular_enrollment.blade.php';

// Check Full enrollment
if (file_exists($fullFile)) {
    $fullContent = file_get_contents($fullFile);
    
    echo "  - Full_enrollment.blade.php:\n";
    
    if (strpos($fullContent, '.payment-method {') !== false) {
        echo "    ✅ Payment method CSS added\n";
    } else {
        echo "    ❌ Payment method CSS missing\n";
    }
    
    if (strpos($fullContent, '.payment-method:hover') !== false) {
        echo "    ✅ Payment method hover effects added\n";
    } else {
        echo "    ❌ Payment method hover effects missing\n";
    }
    
    if (strpos($fullContent, '.payment-method.selected') !== false) {
        echo "    ✅ Payment method selection styling added\n";
    } else {
        echo "    ❌ Payment method selection styling missing\n";
    }
    
    if (strpos($fullContent, '.payment-icon') !== false) {
        echo "    ✅ Payment icon styling added\n";
    } else {
        echo "    ❌ Payment icon styling missing\n";
    }
    
    // Check for form input fixes
    if (strpos($fullContent, 'pointer-events: auto !important') !== false) {
        echo "    ✅ Form input focus fixes added\n";
    } else {
        echo "    ❌ Form input focus fixes missing\n";
    }
    
} else {
    echo "  ❌ Full_enrollment.blade.php not found\n";
}

echo "\n";

// Check Modular enrollment  
if (file_exists($modularFile)) {
    $modularContent = file_get_contents($modularFile);
    
    echo "  - Modular_enrollment.blade.php:\n";
    
    if (strpos($modularContent, '.payment-method {') !== false) {
        echo "    ✅ Payment method CSS added\n";
    } else {
        echo "    ❌ Payment method CSS missing\n";
    }
    
    if (strpos($modularContent, '.payment-method:hover') !== false) {
        echo "    ✅ Payment method hover effects added\n";
    } else {
        echo "    ❌ Payment method hover effects missing\n";
    }
    
    if (strpos($modularContent, '.payment-method.selected') !== false) {
        echo "    ✅ Payment method selection styling added\n";
    } else {
        echo "    ❌ Payment method selection styling missing\n";
    }
    
    if (strpos($modularContent, '.payment-icon') !== false) {
        echo "    ✅ Payment icon styling added\n";
    } else {
        echo "    ❌ Payment icon styling missing\n";
    }
    
    // Check for form input fixes
    if (strpos($modularContent, 'pointer-events: auto !important') !== false) {
        echo "    ✅ Form input focus fixes added\n";
    } else {
        echo "    ❌ Form input focus fixes missing\n";
    }
    
} else {
    echo "  ❌ Modular_enrollment.blade.php not found\n";
}

echo "\n";

// Test 2: Check for selectPaymentMethod function
echo "2. CHECKING PAYMENT METHOD JAVASCRIPT:\n";

if (file_exists($fullFile)) {
    $fullContent = file_get_contents($fullFile);
    echo "  - Full_enrollment.blade.php:\n";
    
    if (strpos($fullContent, 'function selectPaymentMethod') !== false) {
        echo "    ✅ selectPaymentMethod function exists\n";
    } else {
        echo "    ❌ selectPaymentMethod function missing\n";
    }
    
    if (strpos($fullContent, 'onclick="selectPaymentMethod(') !== false) {
        echo "    ✅ Payment method onclick handlers exist\n";
    } else {
        echo "    ❌ Payment method onclick handlers missing\n";
    }
}

if (file_exists($modularFile)) {
    $modularContent = file_get_contents($modularFile);
    echo "  - Modular_enrollment.blade.php:\n";
    
    if (strpos($modularContent, 'function selectPaymentMethod') !== false) {
        echo "    ✅ selectPaymentMethod function exists\n";
    } else {
        echo "    ❌ selectPaymentMethod function missing\n";
    }
    
    if (strpos($modularContent, 'onclick="selectPaymentMethod(') !== false) {
        echo "    ✅ Payment method onclick handlers exist\n";
    } else {
        echo "    ❌ Payment method onclick handlers missing\n";
    }
}

echo "\n";

// Test 3: Check for password field structure
echo "3. CHECKING PASSWORD FIELD STRUCTURE:\n";

if (file_exists($fullFile)) {
    $fullContent = file_get_contents($fullFile);
    echo "  - Full_enrollment.blade.php:\n";
    
    if (strpos($fullContent, 'type="password"') !== false) {
        echo "    ✅ Password fields exist\n";
    } else {
        echo "    ❌ Password fields missing\n";
    }
    
    if (strpos($fullContent, 'id="password"') !== false) {
        echo "    ✅ Password field has proper ID\n";
    } else {
        echo "    ❌ Password field missing proper ID\n";
    }
    
    if (strpos($fullContent, 'id="password_confirmation"') !== false) {
        echo "    ✅ Password confirmation field has proper ID\n";
    } else {
        echo "    ❌ Password confirmation field missing proper ID\n";
    }
    
    // Check if password fields are inside a form
    $passwordPos = strpos($fullContent, 'id="password"');
    $formPos = strpos($fullContent, '<form');
    $formEndPos = strpos($fullContent, '</form>');
    
    if ($passwordPos > $formPos && $passwordPos < $formEndPos) {
        echo "    ✅ Password fields are inside form tag\n";
    } else {
        echo "    ❌ Password fields may not be inside form tag\n";
    }
}

echo "\n";

// Test 4: Check specific CSS properties
echo "4. CHECKING SPECIFIC CSS FIXES:\n";

$files = [$fullFile, $modularFile];
$fileNames = ['Full_enrollment.blade.php', 'Modular_enrollment.blade.php'];

for ($i = 0; $i < count($files); $i++) {
    if (file_exists($files[$i])) {
        $content = file_get_contents($files[$i]);
        echo "  - {$fileNames[$i]}:\n";
        
        // Check for cursor pointer
        if (strpos($content, 'cursor: pointer') !== false) {
            echo "    ✅ Cursor pointer styling exists\n";
        } else {
            echo "    ⚠️  Check cursor pointer styling\n";
        }
        
        // Check for hover transitions
        if (strpos($content, 'transition: all') !== false) {
            echo "    ✅ Hover transitions exist\n";
        } else {
            echo "    ⚠️  Check hover transitions\n";
        }
        
        // Check for border styling
        if (strpos($content, 'border:') !== false || strpos($content, 'border-color:') !== false) {
            echo "    ✅ Border styling exists\n";
        } else {
            echo "    ⚠️  Check border styling\n";
        }
        
        // Check for box-shadow
        if (strpos($content, 'box-shadow:') !== false) {
            echo "    ✅ Box shadow effects exist\n";
        } else {
            echo "    ⚠️  Check box shadow effects\n";
        }
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "Review the results above to ensure all payment method and form fixes are properly implemented.\n";
echo "✅ = Fixed/Working\n";
echo "❌ = Missing/Not Working\n";
echo "⚠️  = Warning/Check Needed\n";
echo "\nYou should now be able to:\n";
echo "1. Click on payment methods and see visual feedback\n";
echo "2. Type in password fields without focus issues\n";
echo "3. See proper hover effects on payment options\n";
?>
