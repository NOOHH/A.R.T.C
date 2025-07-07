<?php
/*
 * Test script to verify unified login functionality
 * This script tests the automatic user type detection by email
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Student;
use App\Models\Professor;

echo "=== Unified Login Test ===\n\n";

// Test 1: Check if we can find students by email
echo "Test 1: Student email lookup\n";
$testStudentEmail = 'test@student.com'; // Replace with actual student email if needed
$student = Student::where('email', $testStudentEmail)->first();
if ($student) {
    echo "✓ Found student: {$student->full_name}\n";
} else {
    echo "✗ No student found with email: {$testStudentEmail}\n";
    
    // Try to find any student
    $anyStudent = Student::first();
    if ($anyStudent) {
        echo "✓ Sample student found: {$anyStudent->email}\n";
    } else {
        echo "✗ No students in database\n";
    }
}

echo "\n";

// Test 2: Check if we can find professors by email
echo "Test 2: Professor email lookup\n";
$testProfessorEmail = 'test@professor.com'; // Replace with actual professor email if needed
$professor = Professor::where('professor_email', $testProfessorEmail)->first();
if ($professor) {
    echo "✓ Found professor: {$professor->full_name}\n";
} else {
    echo "✗ No professor found with email: {$testProfessorEmail}\n";
    
    // Try to find any professor
    $anyProfessor = Professor::first();
    if ($anyProfessor) {
        echo "✓ Sample professor found: {$anyProfessor->professor_email}\n";
    } else {
        echo "✗ No professors in database\n";
    }
}

echo "\n";

// Test 3: Simulate the login logic
echo "Test 3: Unified login logic simulation\n";

function simulateLogin($email) {
    echo "Simulating login for: {$email}\n";
    
    // Check student first
    $student = Student::where('email', $email)->first();
    if ($student) {
        echo "  → Would login as STUDENT: {$student->full_name}\n";
        return 'student';
    }
    
    // Check professor second
    $professor = Professor::where('professor_email', $email)->first();
    if ($professor) {
        echo "  → Would login as PROFESSOR: {$professor->full_name}\n";
        return 'professor';
    }
    
    echo "  → No account found\n";
    return null;
}

// Test with some sample emails
$testEmails = [
    'student@example.com',
    'professor@example.com',
    'admin@example.com',
    'nonexistent@example.com'
];

foreach ($testEmails as $email) {
    simulateLogin($email);
}

echo "\n=== Test Complete ===\n";
?>
