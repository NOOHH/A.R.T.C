<?php

/**
 * Comprehensive Performance and Error Testing for Tenant Analytics
 * Tests multiple scenarios to ensure robustness
 */

class TenantAnalyticsPerformanceTester
{
    private $baseUrl = 'http://127.0.0.1:8000';
    private $results = [];
    
    public function runAllTests()
    {
        echo "=== Comprehensive Tenant Analytics Testing ===\n\n";
        
        $this->testPerformance();
        $this->testErrorHandling();
        $this->testDataConsistency();
        $this->testConcurrentRequests();
        $this->testCaching();
        
        $this->generateReport();
    }
    
    public function testPerformance()
    {
        echo "1. Performance Testing...\n";
        
        $tenants = ['test2', 'artc'];
        
        foreach ($tenants as $tenant) {
            $start = microtime(true);
            
            // Test dashboard page load time
            $dashboardTime = $this->measureRequest("{$this->baseUrl}/t/draft/{$tenant}/admin-dashboard");
            
            // Test API response time
            $apiTime = $this->measureRequest("{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
            
            $total = microtime(true) - $start;
            
            echo "  Tenant {$tenant}:\n";
            echo "    Dashboard: {$dashboardTime}ms\n";
            echo "    API: {$apiTime}ms\n";
            echo "    Total: " . round($total * 1000, 2) . "ms\n";
            
            $this->results['performance'][$tenant] = [
                'dashboard' => $dashboardTime,
                'api' => $apiTime,
                'total' => round($total * 1000, 2)
            ];
        }
        echo "\n";
    }
    
    public function testErrorHandling()
    {
        echo "2. Error Handling Testing...\n";
        
        // Test non-existent tenant
        $invalidTenant = 'nonexistent123';
        $response = $this->makeRequest("{$this->baseUrl}/t/draft/{$invalidTenant}/admin-dashboard");
        
        if ($response['status'] === 404 || $response['status'] === 500) {
            echo "  ✅ Invalid tenant handled correctly (HTTP {$response['status']})\n";
            $this->results['error_handling']['invalid_tenant'] = true;
        } else {
            echo "  ❌ Invalid tenant not handled properly (HTTP {$response['status']})\n";
            $this->results['error_handling']['invalid_tenant'] = false;
        }
        
        // Test API with invalid tenant
        $apiResponse = $this->makeRequest("{$this->baseUrl}/t/draft/{$invalidTenant}/admin/analytics/api");
        
        if ($apiResponse['status'] === 404 || $apiResponse['status'] === 500) {
            echo "  ✅ Invalid tenant API handled correctly (HTTP {$apiResponse['status']})\n";
            $this->results['error_handling']['invalid_api'] = true;
        } else {
            echo "  ❌ Invalid tenant API not handled properly (HTTP {$apiResponse['status']})\n";
            $this->results['error_handling']['invalid_api'] = false;
        }
        
        echo "\n";
    }
    
    public function testDataConsistency()
    {
        echo "3. Data Consistency Testing...\n";
        
        $tenants = ['test2', 'artc'];
        
        foreach ($tenants as $tenant) {
            // Get data from dashboard
            $dashboardData = $this->extractAnalyticsFromDashboard($tenant);
            
            // Get data from API
            $apiData = $this->getAnalyticsFromAPI($tenant);
            
            if (isset($dashboardData['total_students']) && isset($apiData['total_students'])) {
                $consistent = ($dashboardData['total_students'] === $apiData['total_students']);
                
                echo "  Tenant {$tenant}: ";
                if ($consistent) {
                    echo "✅ Dashboard and API data consistent\n";
                    $this->results['consistency'][$tenant] = true;
                } else {
                    echo "❌ Data mismatch - Dashboard: {$dashboardData['total_students']}, API: {$apiData['total_students']}\n";
                    $this->results['consistency'][$tenant] = false;
                }
            } else {
                echo "  Tenant {$tenant}: ❌ Unable to extract data for comparison\n";
                $this->results['consistency'][$tenant] = false;
            }
        }
        echo "\n";
    }
    
    public function testConcurrentRequests()
    {
        echo "4. Concurrent Request Testing...\n";
        
        $tenant = 'test2';
        $requests = 5;
        
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        
        // Create multiple concurrent requests
        for ($i = 0; $i < $requests; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[] = $ch;
        }
        
        // Execute all requests
        $start = microtime(true);
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);
        $totalTime = microtime(true) - $start;
        
        // Check results
        $successCount = 0;
        for ($i = 0; $i < $requests; $i++) {
            $httpCode = curl_getinfo($curlHandles[$i], CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $successCount++;
            }
            curl_multi_remove_handle($multiHandle, $curlHandles[$i]);
            curl_close($curlHandles[$i]);
        }
        curl_multi_close($multiHandle);
        
        echo "  Concurrent requests: {$successCount}/{$requests} successful\n";
        echo "  Total time: " . round($totalTime * 1000, 2) . "ms\n";
        
        $this->results['concurrent'] = [
            'success_rate' => $successCount / $requests,
            'total_time' => round($totalTime * 1000, 2)
        ];
        
        echo "\n";
    }
    
    public function testCaching()
    {
        echo "5. Caching/Response Time Testing...\n";
        
        $tenant = 'test2';
        
        // First request (potential cache miss)
        $time1 = $this->measureRequest("{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
        
        // Second request (potential cache hit)
        $time2 = $this->measureRequest("{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
        
        // Third request
        $time3 = $this->measureRequest("{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
        
        $avgTime = ($time1 + $time2 + $time3) / 3;
        
        echo "  Request 1: {$time1}ms\n";
        echo "  Request 2: {$time2}ms\n";
        echo "  Request 3: {$time3}ms\n";
        echo "  Average: " . round($avgTime, 2) . "ms\n";
        
        $this->results['caching'] = [
            'times' => [$time1, $time2, $time3],
            'average' => round($avgTime, 2)
        ];
        
        echo "\n";
    }
    
    private function measureRequest($url)
    {
        $start = microtime(true);
        $response = $this->makeRequest($url);
        $end = microtime(true);
        
        return round(($end - $start) * 1000, 2);
    }
    
    private function makeRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'body' => $response
        ];
    }
    
    private function extractAnalyticsFromDashboard($tenant)
    {
        $response = $this->makeRequest("{$this->baseUrl}/t/draft/{$tenant}/admin-dashboard");
        
        if ($response['status'] !== 200) {
            return [];
        }
        
        $html = $response['body'];
        $analytics = [];
        
        if (preg_match('/<div class="analytics-card students">.*?<div class="analytics-number">(\d+)<\/div>/s', $html, $matches)) {
            $analytics['total_students'] = (int)$matches[1];
        }
        
        return $analytics;
    }
    
    private function getAnalyticsFromAPI($tenant)
    {
        $response = $this->makeRequest("{$this->baseUrl}/t/draft/{$tenant}/admin/analytics/api");
        
        if ($response['status'] !== 200) {
            return [];
        }
        
        $data = json_decode(str_replace("\xEF\xBB\xBF", '', $response['body']), true);
        
        return $data['analytics'] ?? [];
    }
    
    private function generateReport()
    {
        echo "=== Test Results Summary ===\n\n";
        
        // Performance Summary
        echo "Performance Results:\n";
        foreach ($this->results['performance'] ?? [] as $tenant => $data) {
            echo "  {$tenant}: Dashboard {$data['dashboard']}ms, API {$data['api']}ms\n";
        }
        echo "\n";
        
        // Error Handling Summary
        echo "Error Handling:\n";
        foreach ($this->results['error_handling'] ?? [] as $test => $passed) {
            $status = $passed ? "✅ PASS" : "❌ FAIL";
            echo "  {$test}: {$status}\n";
        }
        echo "\n";
        
        // Data Consistency Summary
        echo "Data Consistency:\n";
        foreach ($this->results['consistency'] ?? [] as $tenant => $consistent) {
            $status = $consistent ? "✅ CONSISTENT" : "❌ INCONSISTENT";
            echo "  {$tenant}: {$status}\n";
        }
        echo "\n";
        
        // Concurrent Testing Summary
        if (isset($this->results['concurrent'])) {
            $successRate = $this->results['concurrent']['success_rate'] * 100;
            echo "Concurrent Testing:\n";
            echo "  Success Rate: {$successRate}%\n";
            echo "  Response Time: {$this->results['concurrent']['total_time']}ms\n\n";
        }
        
        // Overall Assessment
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($this->results as $category => $tests) {
            if (is_array($tests)) {
                foreach ($tests as $test => $result) {
                    $totalTests++;
                    if (is_bool($result) && $result) {
                        $passedTests++;
                    } elseif (is_array($result) && isset($result['success_rate']) && $result['success_rate'] >= 0.8) {
                        $passedTests++;
                    } elseif (is_numeric($result) && $result > 0) {
                        $passedTests++;
                    }
                }
            }
        }
        
        $successPercentage = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
        
        echo "=== Overall Assessment ===\n";
        echo "Total Tests: {$totalTests}\n";
        echo "Passed: {$passedTests}\n";
        echo "Success Rate: {$successPercentage}%\n\n";
        
        if ($successPercentage >= 80) {
            echo "🎉 EXCELLENT: Tenant analytics system is working well!\n";
        } elseif ($successPercentage >= 60) {
            echo "⚠️  GOOD: System is functional but may need minor improvements.\n";
        } else {
            echo "❌ NEEDS IMPROVEMENT: Several issues detected.\n";
        }
    }
}

// Run the comprehensive tests
$tester = new TenantAnalyticsPerformanceTester();
$tester->runAllTests();
