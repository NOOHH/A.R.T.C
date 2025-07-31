<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminSetting;

class UpdateAnnouncementSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:announcement-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix professor announcement management settings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fixing announcement management settings...');

        // Enable the feature
        AdminSetting::updateOrCreate(
            ['setting_key' => 'professor_announcement_management_enabled'],
            ['setting_value' => '1']
        );

        $this->info('✓ Professor announcement management enabled');

        // Check current whitelist
        $whitelist = AdminSetting::getValue('professor_announcement_management_whitelist', '');
        $this->info('Current whitelist: ' . ($whitelist ?: 'EMPTY'));

        // Verify the setting
        $enabled = AdminSetting::getValue('professor_announcement_management_enabled', '0');
        $this->info('Feature enabled: ' . ($enabled === '1' ? 'YES' : 'NO'));

        $this->info('Done!');
        
        return Command::SUCCESS;
    }
}
