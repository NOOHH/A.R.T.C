<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoadDatabaseSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:load-schema {--file=mysql-schema.sql : The schema file to load}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load database schema from SQL file using Laravel database connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schemaFile = $this->option('file');
        $schemaPath = database_path("schema/{$schemaFile}");

        if (!file_exists($schemaPath)) {
            $this->error("Schema file not found: {$schemaPath}");
            return 1;
        }

        $this->info("Loading schema from: {$schemaPath}");

        try {
            // Read the SQL file
            $sql = file_get_contents($schemaPath);
            
            // Split the SQL into individual statements
            $statements = $this->splitSqlStatements($sql);
            
            $this->info("Found " . count($statements) . " SQL statements to execute");
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($statements as $index => $statement) {
                $statement = trim($statement);
                
                // Skip empty statements and comments
                if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, '/*') === 0) {
                    continue;
                }
                
                try {
                    DB::unprepared($statement);
                    $successCount++;
                    
                    if ($this->output->isVerbose()) {
                        $this->line("✓ Executed statement " . ($index + 1));
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("✗ Error in statement " . ($index + 1) . ": " . $e->getMessage());
                    
                    if ($this->output->isVerbose()) {
                        $this->line("Failed statement: " . substr($statement, 0, 100) . "...");
                    }
                }
            }
            
            $this->info("Schema loading completed:");
            $this->info("  ✓ Successful statements: {$successCount}");
            $this->info("  ✗ Failed statements: {$errorCount}");
            
            if ($errorCount > 0) {
                $this->warn("Some statements failed to execute. Check the errors above.");
                return 1;
            }
            
            $this->info("Database schema loaded successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to load schema: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Split SQL file into individual statements
     *
     * @param string $sql
     * @return array
     */
    private function splitSqlStatements($sql)
    {
        // Remove comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Split by semicolon, but be careful with semicolons in strings
        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = '';
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (!$inString && ($char === "'" || $char === '"')) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && $sql[$i - 1] !== '\\') {
                $inString = false;
            }
            
            if (!$inString && $char === ';') {
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            } else {
                $currentStatement .= $char;
            }
        }
        
        // Add the last statement if it's not empty
        if (trim($currentStatement) !== '') {
            $statements[] = trim($currentStatement);
        }
        
        return array_filter($statements);
    }
}
