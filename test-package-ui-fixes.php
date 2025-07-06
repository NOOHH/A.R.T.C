<?php
echo "=== PACKAGE DATA AND UI FIXES VERIFICATION ===\n\n";

// Test 1: Check database for packages
echo "1. CHECKING DATABASE PACKAGES:\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=artc_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM packages LIMIT 5");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($packages) > 0) {
        echo "  ✅ Found " . count($packages) . " packages in database\n";
        foreach ($packages as $package) {
            echo "    - Package: {$package['package_name']} | Amount: {$package['amount']}\n";
        }
    } else {
        echo "  ❌ No packages found in database\n";
    }
    
} catch (Exception $e) {
    echo "  ❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check for missing CSS classes in files
echo "2. CHECKING CSS IMPLEMENTATION:\n";
$fullFile = 'resources/views/registration/Full_enrollment.blade.php';

if (file_exists($fullFile)) {
    $content = file_get_contents($fullFile);
    
    echo "  - Full_enrollment.blade.php:\n";
    
    // Check for payment method styles
    if (strpos($content, '.payment-method {') !== false) {
        echo "    ✅ Payment method base CSS exists\n";
    } else {
        echo "    ❌ Payment method base CSS missing\n";
    }
    
    if (strpos($content, '.payment-method:hover') !== false) {
        echo "    ✅ Payment method hover CSS exists\n";
    } else {
        echo "    ❌ Payment method hover CSS missing\n";
    }
    
    if (strpos($content, '.payment-method.selected') !== false) {
        echo "    ✅ Payment method selected CSS exists\n";
    } else {
        echo "    ❌ Payment method selected CSS missing\n";
    }
    
    // Check for selectedPackagePrice element
    if (strpos($content, 'id="selectedPackagePrice"') !== false) {
        echo "    ✅ selectedPackagePrice element exists\n";
    } else {
        echo "    ❌ selectedPackagePrice element missing\n";
    }
    
    // Check form validation
    if (strpos($content, 'addEventListener.*submit') !== false) {
        echo "    ✅ Form validation added\n";
    } else {
        echo "    ❌ Form validation missing\n";
    }
    
    // Check for novalidate attribute
    if (strpos($content, 'novalidate') !== false) {
        echo "    ✅ Form novalidate attribute added\n";
    } else {
        echo "    ❌ Form novalidate attribute missing\n";
    }
    
    // Check password field required removal
    if (strpos($content, 'type="password" name="password" id="password" placeholder="Password" required') !== false) {
        echo "    ❌ Password fields still have required attribute\n";
    } else {
        echo "    ✅ Password fields required attribute removed\n";
    }
    
} else {
    echo "  ❌ Full_enrollment.blade.php not found\n";
}

echo "\n";

// Test 3: Check for specific issues
echo "3. CHECKING SPECIFIC ISSUES:\n";

// Check route
echo "  - Testing enrollment route accessibility:\n";
$fullRoute = 'http://localhost/A.R.T.C/registration/full';
$ch = curl_init($fullRoute);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "    ✅ Full enrollment route accessible (HTTP $httpCode)\n";
    
    // Check for package data in response
    if (strpos($response, 'SELECT YOUR PACKAGE') !== false) {
        echo "    ✅ Package selection step found in response\n";
    } else {
        echo "    ❌ Package selection step not found in response\n";
    }
    
    // Check for payment methods in response
    if (strpos($response, 'Credit/Debit Card') !== false) {
        echo "    ✅ Payment methods found in response\n";
    } else {
        echo "    ❌ Payment methods not found in response\n";
    }
    
} else {
    echo "    ❌ Full enrollment route not accessible (HTTP $httpCode)\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "If packages are showing broken text, check:\n";
echo "1. Database connection and package data\n";
echo "2. Laravel cache clearance\n";
echo "3. Package model fillable fields\n";
echo "4. Controller passing packages to view\n";
?>
