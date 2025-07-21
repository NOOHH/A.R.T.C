<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DynamicColumnHandler;

class SyncDocumentColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:sync-columns {--force : Force column creation even if they exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync database columns for document requirements from education levels';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting document columns synchronization...');
        
        $handler = new DynamicColumnHandler();
        
        try {
            if ($handler->syncDocumentColumns()) {
                $this->info('✅ Document columns synchronized successfully!');
                
                // Show what columns exist now
                $tables = ['registrations', 'students', 'enrollments'];
                foreach ($tables as $table) {
                    $columns = $handler->getDocumentColumns($table);
                    $this->line("📋 {$table}: " . implode(', ', $columns));
                }
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Document columns synchronization failed!');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
