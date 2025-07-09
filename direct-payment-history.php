<?php
// Simple test page for admin payment history - bypassing middleware
$host = 'localhost';
$dbname = 'artc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query paid enrollments directly
    $stmt = $pdo->query("
        SELECT 
            e.enrollment_id,
            e.user_id,
            e.student_id,
            e.program_id,
            e.package_id,
            e.payment_status,
            e.created_at,
            e.updated_at,
            u.user_name as user_name,
            u.email as user_email,
            s.firstname,
            s.lastname,
            s.email as student_email,
            p.program_name,
            pkg.package_name,
            pkg.price
        FROM enrollments e
        LEFT JOIN users u ON e.user_id = u.user_id
        LEFT JOIN students s ON e.student_id = s.student_id
        LEFT JOIN programs p ON e.program_id = p.program_id
        LEFT JOIN packages pkg ON e.package_id = pkg.package_id
        WHERE e.payment_status = 'paid'
        ORDER BY e.updated_at DESC
    ");
    
    $paidEnrollments = $stmt->fetchAll();
    $count = count($paidEnrollments);
    
    echo "<!DOCTYPE html>";
    echo "<html><head><title>Direct Admin Payment History</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
    echo "</head><body>";
    echo "<div class='container mt-4'>";
    echo "<h1>Direct Payment History Test</h1>";
    echo "<div class='alert alert-info'>Found {$count} paid enrollments directly from database</div>";
    
    if ($count > 0) {
        echo "<table class='table table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Student Name</th>";
        echo "<th>Email</th>";
        echo "<th>Program</th>";
        echo "<th>Package</th>";
        echo "<th>Amount</th>";
        echo "<th>Enrollment Date</th>";
        echo "<th>Payment Date</th>";
        echo "<th>Status</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($paidEnrollments as $enrollment) {
            $studentName = trim($enrollment['firstname'] . ' ' . $enrollment['lastname']);
            if (empty($studentName) && !empty($enrollment['user_name'])) {
                $studentName = $enrollment['user_name'];
            }
            
            $email = $enrollment['student_email'] ?: $enrollment['user_email'];
            $amount = $enrollment['price'] ? '₱' . number_format($enrollment['price'], 2) : 'N/A';
            
            echo "<tr>";
            echo "<td>{$enrollment['enrollment_id']}</td>";
            echo "<td>{$studentName}</td>";
            echo "<td>{$email}</td>";
            echo "<td>{$enrollment['program_name']}</td>";
            echo "<td>{$enrollment['package_name']}</td>";
            echo "<td>{$amount}</td>";
            echo "<td>" . date('M d, Y', strtotime($enrollment['created_at'])) . "</td>";
            echo "<td>" . date('M d, Y', strtotime($enrollment['updated_at'])) . "</td>";
            echo "<td><span class='badge bg-success'>{$enrollment['payment_status']}</span></td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning'>No paid enrollments found</div>";
    }
    
    echo "</div>";
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
