<?php

require_once 'vendor/autoload.php';

use App\Services\GeminiQuizService;

// Test document content with real technical information
$testContent = "
The Shared Responsibility Model is a cloud security framework that divides security responsibilities between cloud service providers and customers. In this model, the cloud provider is responsible for the security OF the cloud infrastructure, including physical security, network controls, and host operating system patching. Customers are responsible for security IN the cloud, which includes data protection, identity and access management, application-level controls, and operating system updates for customer-launched instances.

Infrastructure as a Service (IaaS) provides the most customer responsibility, where customers manage everything from the operating system up. Platform as a Service (PaaS) reduces customer responsibility by handling the operating system and runtime, while Software as a Service (SaaS) provides the least customer responsibility, with the provider managing most security aspects except user access and data classification.

Network security in cloud environments involves multiple layers including Virtual Private Clouds (VPCs), security groups, and Network Access Control Lists (NACLs). Security groups act as virtual firewalls for instances, controlling inbound and outbound traffic at the instance level. NACLs provide an additional layer of security at the subnet level.

Data encryption is crucial in cloud security, involving encryption at rest and encryption in transit. Encryption at rest protects stored data using algorithms like AES-256, while encryption in transit protects data moving between locations using protocols like TLS 1.2 or higher.
";

echo "<h1>Testing Quiz Quality Improvements</h1>\n";
echo "<h2>Test Document Content Length: " . strlen($testContent) . " characters</h2>\n";

try {
    $geminiService = new GeminiQuizService();
    echo "<h2>Generating Quiz...</h2>\n";
    
    $result = $geminiService->generateQuiz($testContent, 5, 3);
    
    echo "<h2>Results:</h2>\n";
    echo "<h3>MCQs Generated: " . count($result['mcqs']) . "</h3>\n";
    echo "<h3>True/False Generated: " . count($result['true_false']) . "</h3>\n";
    
    if (isset($result['error'])) {
        echo "<p style='color: red;'>Error: " . $result['error'] . "</p>\n";
    }
    
    // Display first MCQ to check quality
    if (!empty($result['mcqs'])) {
        $firstMcq = $result['mcqs'][0];
        echo "<h3>Sample MCQ Quality Check:</h3>\n";
        echo "<p><strong>Question:</strong> " . $firstMcq['text'] . "</p>\n";
        echo "<p><strong>Question Length:</strong> " . strlen($firstMcq['text']) . " characters</p>\n";
        echo "<p><strong>Options:</strong></p>\n";
        foreach ($firstMcq['options'] as $letter => $option) {
            echo "<p>{$letter}. {$option} (Length: " . strlen($option) . ")</p>\n";
        }
        
        // Check for quality issues
        $issues = [];
        if (strlen($firstMcq['text']) < 20) {
            $issues[] = "Question too short";
        }
        if (preg_match('/^(What is \w+ What\?|What is .{1,10}\?)$/i', $firstMcq['text'])) {
            $issues[] = "Vague question detected";
        }
        foreach ($firstMcq['options'] as $letter => $option) {
            if (strlen($option) < 10) {
                $issues[] = "Option {$letter} too short";
            }
        }
        
        if (!empty($issues)) {
            echo "<p style='color: red;'><strong>Quality Issues Found:</strong> " . implode(', ', $issues) . "</p>\n";
        } else {
            echo "<p style='color: green;'><strong>Quality Check: PASSED</strong></p>\n";
        }
    }
    
    // Display first True/False to check quality
    if (!empty($result['true_false'])) {
        $firstTf = $result['true_false'][0];
        echo "<h3>Sample True/False Quality Check:</h3>\n";
        echo "<p><strong>Statement:</strong> " . $firstTf['statement'] . "</p>\n";
        echo "<p><strong>Statement Length:</strong> " . strlen($firstTf['statement']) . " characters</p>\n";
        
        if (strlen($firstTf['statement']) < 15) {
            echo "<p style='color: red;'><strong>Quality Issue:</strong> Statement too short</p>\n";
        } else {
            echo "<p style='color: green;'><strong>Quality Check: PASSED</strong></p>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>\n";
}

echo "<h2>Test Complete</h2>\n";
?>
