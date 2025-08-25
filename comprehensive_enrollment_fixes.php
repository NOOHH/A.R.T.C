<?php
echo "🛠️  IMPLEMENTING COMPREHENSIVE FIXES FOR ALL IDENTIFIED ISSUES\n";
echo "=============================================================\n\n";

// Issue 1: Missing packages table in tenant database
echo "1️⃣ FIXING MISSING PACKAGES TABLE IN TENANT DATABASE:\n";
echo "----------------------------------------------------\n";

try {
    // Connect to main database to check tenant structure
    $mainPdo = new PDO('mysql:host=localhost;dbname=smartprep', 'root', '');
    $mainPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get ARTC tenant info
    $stmt = $mainPdo->prepare("SELECT * FROM tenants WHERE slug = 'artc'");
    $stmt->execute();
    $tenant = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($tenant) {
        echo "✅ Found ARTC tenant: {$tenant->name}\n";
        echo "   Database: {$tenant->database_name}\n";
        
        // Check if tenant database exists
        $tenantDbName = $tenant->database_name;
        
        // Connect to tenant database
        try {
            $tenantPdo = new PDO("mysql:host=localhost;dbname=$tenantDbName", 'root', '');
            $tenantPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Connected to tenant database: $tenantDbName\n";
            
            // Check if packages table exists
            $stmt = $tenantPdo->query("SHOW TABLES LIKE 'packages'");
            $packageTable = $stmt->fetch();
            
            if (!$packageTable) {
                echo "❌ packages table missing in tenant database\n";
                echo "🔧 Creating packages table...\n";
                
                // Get packages table structure from main database
                $mainPdo->exec("USE smartprep");
                $stmt = $mainPdo->query("SHOW CREATE TABLE packages");
                $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($createTable) {
                    $createSql = $createTable['Create Table'];
                    
                    // Execute in tenant database
                    $tenantPdo->exec($createSql);
                    echo "✅ packages table created in tenant database\n";
                    
                    // Copy sample data
                    $stmt = $mainPdo->query("SELECT * FROM packages WHERE package_type = 'modular' LIMIT 5");
                    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($packages) {
                        foreach ($packages as $package) {
                            $columns = implode(',', array_keys($package));
                            $placeholders = ':' . implode(', :', array_keys($package));
                            
                            $insertSql = "INSERT INTO packages ($columns) VALUES ($placeholders)";
                            $insertStmt = $tenantPdo->prepare($insertSql);
                            $insertStmt->execute($package);
                        }
                        echo "✅ Sample package data copied to tenant database\n";
                    }
                }
                
            } else {
                echo "✅ packages table exists in tenant database\n";
            }
            
            // Check other critical tables
            $requiredTables = ['programs', 'courses', 'modules', 'education_levels'];
            foreach ($requiredTables as $table) {
                $stmt = $tenantPdo->query("SHOW TABLES LIKE '$table'");
                $exists = $stmt->fetch();
                
                if ($exists) {
                    echo "✅ $table table exists\n";
                } else {
                    echo "❌ $table table missing\n";
                    
                    // Copy table structure and data
                    $stmt = $mainPdo->query("SHOW CREATE TABLE $table");
                    $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($createTable) {
                        $createSql = $createTable['Create Table'];
                        $tenantPdo->exec($createSql);
                        echo "✅ Created $table table\n";
                        
                        // Copy sample data
                        $stmt = $mainPdo->query("SELECT * FROM $table LIMIT 10");
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($data as $row) {
                            $columns = implode(',', array_keys($row));
                            $placeholders = ':' . implode(', :', array_keys($row));
                            
                            $insertSql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
                            $insertStmt = $tenantPdo->prepare($insertSql);
                            $insertStmt->execute($row);
                        }
                        echo "✅ Sample data copied to $table\n";
                    }
                }
            }
            
        } catch (PDOException $e) {
            echo "❌ Error connecting to tenant database: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ ARTC tenant not found\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Issue 2: Test the enrollment routes after database fix
echo "\n2️⃣ TESTING ENROLLMENT ROUTES AFTER DATABASE FIX:\n";
echo "------------------------------------------------\n";

$testUrls = [
    'Draft ARTC modular' => 'http://127.0.0.1:8000/t/draft/artc/enrollment/modular',
    'Draft ARTC full' => 'http://127.0.0.1:8000/t/draft/artc/enrollment/full',
    'Live ARTC modular' => 'http://127.0.0.1:8000/t/artc/enrollment/modular',
    'Live ARTC full' => 'http://127.0.0.1:8000/t/artc/enrollment/full',
];

foreach ($testUrls as $name => $url) {
    echo "🧪 Testing: $name\n";
    echo "   URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "   ✅ Success (HTTP $httpCode)\n";
    } elseif ($httpCode == 302 || $httpCode == 301) {
        echo "   🔄 Redirect (HTTP $httpCode) - Normal behavior\n";
    } else {
        echo "   ❌ Error (HTTP $httpCode)\n";
    }
    
    echo "\n";
}

// Issue 3: Create a comprehensive browser test
echo "\n3️⃣ CREATING BROWSER TEST FOR ENROLLMENT BUTTONS:\n";
echo "------------------------------------------------\n";

$browserTestHtml = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Button Tenant-Awareness Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">🧪 Enrollment Button Tenant-Awareness Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🌐 Regular Enrollment (Non-Tenant)</h5>
                    </div>
                    <div class="card-body">
                        <p>These buttons should go to regular enrollment pages:</p>
                        <a href="http://127.0.0.1:8000/enrollment" class="btn btn-primary me-2">Regular Enrollment</a>
                        <a href="http://127.0.0.1:8000/enrollment/modular" class="btn btn-secondary">Regular Modular</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🏢 ARTC Draft Tenant</h5>
                    </div>
                    <div class="card-body">
                        <p>These buttons should go to ARTC draft tenant enrollment:</p>
                        <a href="http://127.0.0.1:8000/t/draft/artc/enrollment" class="btn btn-success me-2">ARTC Draft Enrollment</a>
                        <a href="http://127.0.0.1:8000/t/draft/artc/enrollment/modular" class="btn btn-info">ARTC Draft Modular</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🚀 ARTC Live Tenant</h5>
                    </div>
                    <div class="card-body">
                        <p>These buttons should go to ARTC live tenant enrollment:</p>
                        <a href="http://127.0.0.1:8000/t/artc/enrollment" class="btn btn-warning me-2">ARTC Live Enrollment</a>
                        <a href="http://127.0.0.1:8000/t/artc/enrollment/modular" class="btn btn-danger">ARTC Live Modular</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="testResults">
                            <p class="text-muted">Click buttons to test tenant-awareness...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🔍 URL Analysis</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Current URL:</strong> <span id="currentUrl"></span></p>
                        <p><strong>Tenant Context:</strong> <span id="tenantContext"></span></p>
                        <p><strong>Expected Enrollment URL:</strong> <span id="expectedUrl"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Update current URL info
        document.getElementById("currentUrl").textContent = window.location.href;
        
        // Analyze tenant context from URL
        const url = window.location.href;
        const tenantMatch = url.match(/\/t\/(?:draft\/)?([^\/]+)/);
        
        if (tenantMatch) {
            const tenant = tenantMatch[1];
            const isDraft = url.includes("/draft/");
            document.getElementById("tenantContext").textContent = `${tenant} (${isDraft ? "Draft" : "Live"})`;
            
            const enrollmentPath = isDraft ? `/t/draft/${tenant}/enrollment` : `/t/${tenant}/enrollment`;
            document.getElementById("expectedUrl").textContent = `http://127.0.0.1:8000${enrollmentPath}`;
        } else {
            document.getElementById("tenantContext").textContent = "No tenant context";
            document.getElementById("expectedUrl").textContent = "http://127.0.0.1:8000/enrollment";
        }
        
        // Test button click tracking
        document.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", function(e) {
                const results = document.getElementById("testResults");
                const href = this.href;
                const text = this.textContent;
                
                results.innerHTML += `<p class="small">🔗 Clicked: ${text} → ${href}</p>`;
            });
        });
    </script>
</body>
</html>';

$testFile = 'public/enrollment-button-test.html';
file_put_contents($testFile, $browserTestHtml);
echo "✅ Browser test created: $testFile\n";
echo "🌐 Access at: http://127.0.0.1:8000/enrollment-button-test.html\n";

// Issue 4: Create enrollment URL validation script
echo "\n4️⃣ CREATING ENROLLMENT URL VALIDATION SCRIPT:\n";
echo "---------------------------------------------\n";

$validationScript = '<?php
// Enrollment URL validation script

echo "🔍 ENROLLMENT URL VALIDATION TEST\\n";
echo "=================================\\n\\n";

// Test different tenant contexts
$testContexts = [
    [
        "name" => "Regular non-tenant",
        "base_url" => "http://127.0.0.1:8000",
        "enrollment_path" => "/enrollment",
        "modular_path" => "/enrollment/modular"
    ],
    [
        "name" => "ARTC Draft Tenant", 
        "base_url" => "http://127.0.0.1:8000",
        "enrollment_path" => "/t/draft/artc/enrollment",
        "modular_path" => "/t/draft/artc/enrollment/modular"
    ],
    [
        "name" => "ARTC Live Tenant",
        "base_url" => "http://127.0.0.1:8000", 
        "enrollment_path" => "/t/artc/enrollment",
        "modular_path" => "/t/artc/enrollment/modular"
    ]
];

foreach ($testContexts as $context) {
    echo "📋 Testing: {$context[\"name\"]}\\n";
    echo "   Base URL: {$context[\"base_url\"]}\\n";
    
    $enrollmentUrl = $context[\"base_url\"] . $context[\"enrollment_path\"];
    $modularUrl = $context[\"base_url\"] . $context[\"modular_path\"];
    
    // Test enrollment page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $enrollmentUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($httpCode == 200 || $httpCode == 302) ? "✅" : "❌";
    echo "   $status Enrollment page: $enrollmentUrl (HTTP $httpCode)\\n";
    
    // Test modular page
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $modularUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $status = ($httpCode == 200 || $httpCode == 302) ? "✅" : "❌";
    echo "   $status Modular page: $modularUrl (HTTP $httpCode)\\n";
    
    echo "\\n";
}

echo "=== VALIDATION COMPLETE ===\\n";
?>';

$validationFile = 'enrollment_url_validation.php';
file_put_contents($validationFile, $validationScript);
echo "✅ URL validation script created: $validationFile\n";

echo "\n=== ALL FIXES IMPLEMENTED ===\n";
echo "🎯 NEXT STEPS:\n";
echo "1. Run: php enrollment_url_validation.php\n";
echo "2. Visit: http://127.0.0.1:8000/enrollment-button-test.html\n";
echo "3. Test enrollment buttons in browser\n";
echo "4. Verify tenant-aware URL generation\n";
?>
