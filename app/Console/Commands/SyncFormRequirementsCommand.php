<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FormRequirement;

class SyncFormRequirementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'form-requirements:sync 
                            {--dry-run : Show what would be done without making changes}
                            {--show-orphaned : Show orphaned columns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync form requirements with database columns in registrations and students tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Form Requirements Database Sync...');
        $this->newLine();

        if ($this->option('show-orphaned')) {
            $this->showOrphanedColumns();
            $this->newLine();
        }

        if ($this->option('dry-run')) {
            $this->info('DRY RUN MODE - No changes will be made');
            $this->newLine();
            $this->simulateSync();
        } else {
            $this->performSync();
        }

        $this->newLine();
        $this->info('Form Requirements Database Sync completed!');
    }

    /**
     * Show orphaned columns
     */
    private function showOrphanedColumns()
    {
        $this->info('Checking for orphaned columns...');
        
        $orphaned = FormRequirement::getOrphanedColumns();
        
        if ($orphaned['total_orphaned'] > 0) {
            $this->warn("Found {$orphaned['total_orphaned']} orphaned columns:");
            
            if (!empty($orphaned['registrations'])) {
                $this->line('  Registrations table:');
                foreach ($orphaned['registrations'] as $column) {
                    $this->line("    - {$column}");
                }
            }
            
            if (!empty($orphaned['students'])) {
                $this->line('  Students table:');
                foreach ($orphaned['students'] as $column) {
                    $this->line("    - {$column}");
                }
            }
            
            $this->newLine();
            $this->comment('Orphaned columns have data but no active form requirement.');
            $this->comment('They are preserved to prevent data loss.');
        } else {
            $this->info('No orphaned columns found.');
        }
    }

    /**
     * Simulate the sync process
     */
    private function simulateSync()
    {
        $formRequirements = FormRequirement::active()->get();
        
        $this->info("Would process {$formRequirements->count()} active form requirements:");
        
        foreach ($formRequirements as $formRequirement) {
            if ($formRequirement->field_type === 'section') {
                $this->line("  SKIP: {$formRequirement->field_name} (section)");
                continue;
            }
            
            $regExists = FormRequirement::columnExists($formRequirement->field_name, 'registrations');
            $studExists = FormRequirement::columnExists($formRequirement->field_name, 'students');
            
            if (!$regExists || !$studExists) {
                $actions = [];
                if (!$regExists) $actions[] = 'registrations';
                if (!$studExists) $actions[] = 'students';
                
                $this->line("  CREATE: {$formRequirement->field_name} ({$formRequirement->field_type}) in " . implode(', ', $actions));
            } else {
                $this->line("  EXISTS: {$formRequirement->field_name}");
            }
        }
    }

    /**
     * Perform the actual sync
     */
    private function performSync()
    {
        $this->info('Performing database sync...');
        
        $result = FormRequirement::syncAllFormRequirementsWithDatabase();
        
        $this->newLine();
        $this->info("Sync Results:");
        $this->line("  Total Processed: {$result['total_processed']}");
        $this->line("  Successful: {$result['successful']}");
        
        if ($result['failed'] > 0) {
            $this->error("  Failed: {$result['failed']}");
            $this->newLine();
            $this->warn('Check the Laravel logs for detailed error information.');
        } else {
            $this->info("  Failed: {$result['failed']}");
        }
    }
}
