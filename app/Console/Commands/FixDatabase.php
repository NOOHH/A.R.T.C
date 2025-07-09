<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix database issues for ARTC enrollment system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info("Starting database fixes...");
            
            // Fix 1: Update foreign key to reference student_batches instead of batches
            $this->info("1. Fixing batch_id foreign key constraint...");
            
            // Check if foreign key exists and drop it
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'enrollments' 
                AND COLUMN_NAME = 'batch_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE enrollments DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                $this->info("   Dropped foreign key: {$fk->CONSTRAINT_NAME}");
            }
            
            // Add correct foreign key
            DB::statement("
                ALTER TABLE enrollments 
                ADD CONSTRAINT enrollments_batch_id_foreign 
                FOREIGN KEY (batch_id) REFERENCES student_batches(batch_id) ON DELETE SET NULL
            ");
            $this->info("   Added correct foreign key to student_batches");
            
            // Fix 2: Add user_id column if it doesn't exist
            $this->info("2. Adding user_id column to enrollments...");
            
            $columns = DB::select("SHOW COLUMNS FROM enrollments LIKE 'user_id'");
            if (empty($columns)) {
                DB::statement("ALTER TABLE enrollments ADD COLUMN user_id INT NULL AFTER student_id");
                DB::statement("ALTER TABLE enrollments ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
                DB::statement("ALTER TABLE enrollments ADD INDEX idx_enrollments_user_id (user_id)");
                $this->info("   Added user_id column with foreign key");
            } else {
                $this->info("   user_id column already exists");
            }
            
            // Fix 3: Create payment_history table if it doesn't exist
            $this->info("3. Creating payment_history table...");
            
            $tables = DB::select("SHOW TABLES LIKE 'payment_history'");
            if (empty($tables)) {
                // Create table without foreign keys first
                DB::statement("
                    CREATE TABLE payment_history (
                        payment_history_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        enrollment_id INT NOT NULL,
                        user_id INT NULL,
                        student_id VARCHAR(255) NULL,
                        program_id INT UNSIGNED NOT NULL,
                        package_id INT UNSIGNED NOT NULL,
                        amount DECIMAL(10,2) NULL,
                        payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
                        payment_method ENUM('cash', 'card', 'bank_transfer', 'gcash', 'other') NULL,
                        payment_notes TEXT NULL,
                        payment_date TIMESTAMP NULL,
                        processed_by_admin_id INT NULL,
                        created_at TIMESTAMP NULL DEFAULT NULL,
                        updated_at TIMESTAMP NULL DEFAULT NULL,
                        
                        INDEX idx_payment_history_enrollment (enrollment_id, payment_status),
                        INDEX idx_payment_history_user (user_id),
                        INDEX idx_payment_history_student (student_id),
                        INDEX idx_payment_history_date (payment_date)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
                $this->info("   Created payment_history table");
                
                // Add foreign keys separately
                try {
                    DB::statement("ALTER TABLE payment_history ADD FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE");
                    $this->info("   Added enrollment_id foreign key");
                } catch (\Exception $e) {
                    $this->warn("   Could not add enrollment_id foreign key: " . $e->getMessage());
                }
                
                try {
                    DB::statement("ALTER TABLE payment_history ADD FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL");
                    $this->info("   Added user_id foreign key");
                } catch (\Exception $e) {
                    $this->warn("   Could not add user_id foreign key: " . $e->getMessage());
                }
                
                try {
                    DB::statement("ALTER TABLE payment_history ADD FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE");
                    $this->info("   Added program_id foreign key");
                } catch (\Exception $e) {
                    $this->warn("   Could not add program_id foreign key: " . $e->getMessage());
                }
                
                try {
                    DB::statement("ALTER TABLE payment_history ADD FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE");
                    $this->info("   Added package_id foreign key");
                } catch (\Exception $e) {
                    $this->warn("   Could not add package_id foreign key: " . $e->getMessage());
                }
                
                try {
                    DB::statement("ALTER TABLE payment_history ADD FOREIGN KEY (processed_by_admin_id) REFERENCES admins(admin_id) ON DELETE SET NULL");
                    $this->info("   Added processed_by_admin_id foreign key");
                } catch (\Exception $e) {
                    $this->warn("   Could not add processed_by_admin_id foreign key: " . $e->getMessage());
                }
            } else {
                $this->info("   payment_history table already exists");
            }
            
            // Fix 4: Update existing enrollments to include user_id where possible
            $this->info("4. Updating existing enrollments with user_id...");
            
            $updated = DB::statement("
                UPDATE enrollments e
                INNER JOIN students s ON e.student_id = s.student_id
                SET e.user_id = s.user_id
                WHERE e.user_id IS NULL AND s.user_id IS NOT NULL
            ");
            $this->info("   Updated existing enrollments with user_id");
            
            $this->info("Database fixes completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
