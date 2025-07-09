<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Starting database fixes...\n";
    
    // Fix 1: Update foreign key to reference student_batches instead of batches
    echo "1. Fixing batch_id foreign key constraint...\n";
    
    // Check if foreign key exists and drop it
    $foreignKeys = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'artc' 
        AND TABLE_NAME = 'enrollments' 
        AND COLUMN_NAME = 'batch_id' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    foreach ($foreignKeys as $fk) {
        DB::statement("ALTER TABLE enrollments DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        echo "   Dropped foreign key: {$fk->CONSTRAINT_NAME}\n";
    }
    
    // Add correct foreign key
    DB::statement("
        ALTER TABLE enrollments 
        ADD CONSTRAINT enrollments_batch_id_foreign 
        FOREIGN KEY (batch_id) REFERENCES student_batches(batch_id) ON DELETE SET NULL
    ");
    echo "   Added correct foreign key to student_batches\n";
    
    // Fix 2: Add user_id column if it doesn't exist
    echo "2. Adding user_id column to enrollments...\n";
    
    $columns = DB::select("SHOW COLUMNS FROM enrollments LIKE 'user_id'");
    if (empty($columns)) {
        DB::statement("ALTER TABLE enrollments ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER student_id");
        DB::statement("ALTER TABLE enrollments ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        DB::statement("ALTER TABLE enrollments ADD INDEX idx_enrollments_user_id (user_id)");
        echo "   Added user_id column with foreign key\n";
    } else {
        echo "   user_id column already exists\n";
    }
    
    // Fix 3: Create payment_history table if it doesn't exist
    echo "3. Creating payment_history table...\n";
    
    $tables = DB::select("SHOW TABLES LIKE 'payment_history'");
    if (empty($tables)) {
        DB::statement("
            CREATE TABLE payment_history (
                payment_history_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                enrollment_id INT NOT NULL,
                user_id BIGINT UNSIGNED NULL,
                student_id VARCHAR(255) NULL,
                program_id INT UNSIGNED NOT NULL,
                package_id INT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NULL,
                payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
                payment_method ENUM('cash', 'card', 'bank_transfer', 'gcash', 'other') NULL,
                payment_notes TEXT NULL,
                payment_date TIMESTAMP NULL,
                processed_by_admin_id BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                
                FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
                FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE,
                FOREIGN KEY (processed_by_admin_id) REFERENCES admins(admin_id) ON DELETE SET NULL,
                
                INDEX idx_payment_history_enrollment (enrollment_id, payment_status),
                INDEX idx_payment_history_user (user_id),
                INDEX idx_payment_history_student (student_id),
                INDEX idx_payment_history_date (payment_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "   Created payment_history table\n";
    } else {
        echo "   payment_history table already exists\n";
    }
    
    // Fix 4: Update existing enrollments to include user_id where possible
    echo "4. Updating existing enrollments with user_id...\n";
    
    $updated = DB::statement("
        UPDATE enrollments e
        INNER JOIN students s ON e.student_id = s.student_id
        SET e.user_id = s.user_id
        WHERE e.user_id IS NULL AND s.user_id IS NOT NULL
    ");
    echo "   Updated existing enrollments with user_id\n";
    
    echo "Database fixes completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
