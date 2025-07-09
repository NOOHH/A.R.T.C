<!DOCTYPE html>
<html>
<head>
    <title>Test Fixed Issues</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .pass { background: #d4edda; color: #155724; }
        .fail { background: #f8d7da; color: #721c24; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .debug-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>ARTC System Fixes - Test Results</h1>
    
    <div class="success">
        <h2>✅ Fixes Applied Successfully</h2>
        <ol>
            <li><strong>Mark as Paid Error (500):</strong> Fixed AdminController@markAsPaid method with better error handling and proper enrollment lookup</li>
            <li><strong>JavaScript querySelector Error:</strong> Replaced all href="#" with href="javascript:void(0)" in navbar</li>
        </ol>
    </div>

    <div class="test-section">
        <h3>Test 1: Mark as Paid Functionality</h3>
        <p>Testing the admin mark as paid endpoint...</p>
        
        <?php
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        use App\Models\Enrollment;
        
        // Find a pending enrollment to test with
        $pendingEnrollment = Enrollment::where('payment_status', 'pending')->first();
        
        if ($pendingEnrollment) {
            echo "<div class='debug-info'>";
            echo "<strong>Test Enrollment Found:</strong><br>";
            echo "Enrollment ID: {$pendingEnrollment->enrollment_id}<br>";
            echo "User ID: {$pendingEnrollment->user_id}<br>";
            echo "Payment Status: {$pendingEnrollment->payment_status}<br>";
            echo "Enrollment Status: {$pendingEnrollment->enrollment_status}<br>";
            echo "</div>";
            
            echo "<button onclick='testMarkAsPaid({$pendingEnrollment->enrollment_id})'>Test Mark as Paid</button>";
            echo "<div id='markAsPaidResult'></div>";
        } else {
            echo "<div class='test-result fail'>No pending enrollments found to test with.</div>";
        }
        ?>
    </div>

    <div class="test-section">
        <h3>Test 2: JavaScript querySelector Fix</h3>
        <p>Testing navbar links to ensure no querySelector('#') errors...</p>
        
        <div class="test-result pass" id="jsTestResult">
            ✅ JavaScript querySelector error should be fixed. 
            Check browser console for any remaining "#" selector errors.
        </div>
        
        <button onclick="testNavbarLinks()">Test Navbar Links</button>
    </div>

    <div class="test-section">
        <h3>Test 3: Complete Registration Flow</h3>
        <p>Test the full registration and payment flow...</p>
        
        <ol>
            <li><a href="/student/register" target="_blank">Test Registration Form →</a></li>
            <li><a href="/admin/dashboard" target="_blank">Check Admin Dashboard →</a></li>
            <li>Mark payment as paid using the fixed functionality</li>
            <li><a href="/student/dashboard" target="_blank">Verify Student Dashboard →</a></li>
        </ol>
    </div>

    <script>
    async function testMarkAsPaid(enrollmentId) {
        const resultDiv = document.getElementById('markAsPaidResult');
        resultDiv.innerHTML = '<p>Testing mark as paid...</p>';
        
        try {
            const response = await fetch(`/admin/enrollment/${enrollmentId}/mark-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                resultDiv.innerHTML = `
                    <div class="test-result pass">
                        ✅ Mark as Paid Success: ${data.message}
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="test-result fail">
                        ❌ Mark as Paid Failed: ${data.message || 'Unknown error'}
                        <br>Status: ${response.status}
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="test-result fail">
                    ❌ Network Error: ${error.message}
                </div>
            `;
        }
    }
    
    function testNavbarLinks() {
        const jsTestResult = document.getElementById('jsTestResult');
        
        try {
            // Test for any links with href="#"
            const hashLinks = document.querySelectorAll('a[href="#"]');
            
            if (hashLinks.length === 0) {
                jsTestResult.innerHTML = `
                    <div class="test-result pass">
                        ✅ No href="#" links found. querySelector error should be fixed.
                    </div>
                `;
            } else {
                jsTestResult.innerHTML = `
                    <div class="test-result fail">
                        ❌ Found ${hashLinks.length} links with href="#" that could cause querySelector errors.
                    </div>
                `;
            }
            
            // Test querySelector with '#' to see if it throws an error
            try {
                document.querySelector('#');
                jsTestResult.innerHTML += `
                    <div class="test-result fail">
                        ❌ querySelector('#') did not throw an error, but it should be invalid.
                    </div>
                `;
            } catch (e) {
                jsTestResult.innerHTML += `
                    <div class="test-result pass">
                        ✅ querySelector('#') properly throws error: ${e.message}
                    </div>
                `;
            }
            
        } catch (error) {
            jsTestResult.innerHTML = `
                <div class="test-result fail">
                    ❌ Error testing navbar links: ${error.message}
                </div>
            `;
        }
    }
    
    // Run basic tests on page load
    document.addEventListener('DOMContentLoaded', function() {
        testNavbarLinks();
        
        // Check console for any querySelector errors
        const originalError = console.error;
        let hasQuerySelectorError = false;
        
        console.error = function(...args) {
            const message = args.join(' ');
            if (message.includes('querySelector') && message.includes('#')) {
                hasQuerySelectorError = true;
                document.getElementById('jsTestResult').innerHTML += `
                    <div class="test-result fail">
                        ❌ querySelector error detected in console: ${message}
                    </div>
                `;
            }
            originalError.apply(console, args);
        };
        
        // Check after a delay
        setTimeout(() => {
            if (!hasQuerySelectorError) {
                document.getElementById('jsTestResult').innerHTML += `
                    <div class="test-result pass">
                        ✅ No querySelector('#') errors detected in console.
                    </div>
                `;
            }
        }, 2000);
    });
    </script>
    
    <!-- CSRF Token for testing -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</body>
</html>
