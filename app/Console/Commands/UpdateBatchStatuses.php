<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentBatch;
use Carbon\Carbon;

class UpdateBatchStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batches:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update batch statuses based on start and end dates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating batch statuses...');
        
        $batches = StudentBatch::whereIn('batch_status', ['pending', 'available', 'ongoing'])
            ->get();
        
        $updated = 0;
        
        foreach ($batches as $batch) {
            $oldStatus = $batch->batch_status;
            $batch->updateStatusBasedOnDates();
            
            if ($batch->batch_status !== $oldStatus) {
                $updated++;
                $this->line("Batch '{$batch->batch_name}' status changed from {$oldStatus} to {$batch->batch_status}");
            }
        }
        
        $this->info("Updated {$updated} batch(es) status.");
        
        return Command::SUCCESS;
    }
}
