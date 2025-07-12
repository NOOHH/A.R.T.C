<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PruneMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:prune 
                            {--days=30 : Number of days to keep messages}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old messages from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Pruning messages older than {$days} days (before {$cutoffDate->format('Y-m-d H:i:s')})");
        
        // Get messages to be pruned
        $messagesToPrune = Message::where('created_at', '<', $cutoffDate);
        
        if ($dryRun) {
            $count = $messagesToPrune->count();
            $this->info("DRY RUN: Would delete {$count} messages");
            
            if ($count > 0) {
                $this->table(
                    ['ID', 'Sender', 'Receiver', 'Content Preview', 'Created At'],
                    $messagesToPrune->with(['sender', 'receiver'])
                        ->limit(10)
                        ->get()
                        ->map(function ($message) {
                            return [
                                $message->id,
                                $message->sender->name ?? 'Unknown',
                                $message->receiver->name ?? 'Unknown',
                                Str::limit($message->content, 30),
                                $message->created_at->format('Y-m-d H:i:s')
                            ];
                        })
                        ->toArray()
                );
                
                if ($count > 10) {
                    $this->info("... and " . ($count - 10) . " more messages");
                }
            }
        } else {
            if ($this->confirm('Are you sure you want to permanently delete these messages?')) {
                $deletedCount = $messagesToPrune->forceDelete();
                $this->info("Successfully deleted {$deletedCount} messages");
                
                // Also prune soft-deleted messages older than 90 days
                $oldSoftDeleted = Message::onlyTrashed()
                    ->where('deleted_at', '<', Carbon::now()->subDays(90))
                    ->forceDelete();
                
                if ($oldSoftDeleted > 0) {
                    $this->info("Also permanently deleted {$oldSoftDeleted} soft-deleted messages older than 90 days");
                }
            } else {
                $this->info('Operation cancelled');
            }
        }
        
        return 0;
    }
}
