<?php

use App\Services\GeminiQuizService;

// Create a simple test script to debug the quiz generation
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $service = new GeminiQuizService();
    
    // Test with a simple content
    $testContent = "
    Cloud Computing Security involves multiple layers of protection. The Shared Responsibility Model is a fundamental concept where cloud providers handle infrastructure security while customers manage application-level security.
    
    Infrastructure as a Service (IaaS) provides the most customer responsibility. Platform as a Service (PaaS) reduces customer burden by managing the operating system. Software as a Service (SaaS) requires minimal customer security management.
    
    Data encryption is crucial in cloud environments. Encryption at rest protects stored data using algorithms like AES-256. Encryption in transit secures data moving between locations using protocols like TLS 1.2.
    
    Virtual Private Clouds (VPCs) provide network isolation. Security groups act as virtual firewalls at the instance level. Network Access Control Lists (NACLs) operate at the subnet level for additional security.
    
    Identity and Access Management (IAM) controls user permissions. Multi-factor authentication (MFA) adds an extra security layer. Role-based access control (RBAC) ensures users only access necessary resources.
    ";
    
    echo "Testing enhanced quiz generation...\n";
    echo "Content length: " . strlen($testContent) . " characters\n\n";
    
    $result = $service->generateQuiz($testContent, 3, 2);
    
    echo "Generation Result:\n";
    echo "MCQs: " . count($result['mcqs']) . "\n";
    echo "T/F: " . count($result['true_false']) . "\n";
    echo "Error: " . ($result['error'] ?? 'None') . "\n\n";
    
    if (!empty($result['mcqs'])) {
        echo "Sample MCQ:\n";
        $mcq = $result['mcqs'][0];
        echo "Q: " . $mcq['text'] . "\n";
        echo "A: " . $mcq['options']['A'] . "\n";
        echo "B: " . $mcq['options']['B'] . "\n";
        echo "C: " . $mcq['options']['C'] . "\n";
        echo "D: " . $mcq['options']['D'] . "\n\n";
        
        // Quality check
        $issues = [];
        if (strlen($mcq['text']) < 30) $issues[] = "Question too short";
        if (preg_match('/^What is \w+ What\?/i', $mcq['text'])) $issues[] = "Vague question pattern";
        foreach ($mcq['options'] as $letter => $option) {
            if (strlen($option) < 15) $issues[] = "Option {$letter} too short";
        }
        
        if (empty($issues)) {
            echo "✅ Quality check: PASSED\n";
        } else {
            echo "❌ Quality issues: " . implode(', ', $issues) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
