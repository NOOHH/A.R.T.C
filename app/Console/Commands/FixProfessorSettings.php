<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminSetting;

class FixProfessorSettings extends Command
{
    protected $signature = 'fix:professor-settings';
    protected $description = 'Fix professor feature settings and whitelist issues';

    public function handle()
    {
        $this->info('Fixing professor settings and whitelist issues...');

        // Create missing settings with proper defaults
        $settings = [
            'professor_announcement_management_enabled' => '1',
            'professor_announcement_management_whitelist' => '',
            'professor_module_management_enabled' => '1', 
            'professor_module_management_whitelist' => '',
            'meeting_creation_enabled' => '1',
            'meeting_creation_whitelist' => ''
        ];

        foreach ($settings as $key => $value) {
            AdminSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $value,
                    'setting_description' => 'Professor feature setting',
                    'is_active' => 1
                ]
            );
            $this->info("✓ Updated setting: {$key} = {$value}");
        }

        $this->info('All professor settings updated successfully!');
        return Command::SUCCESS;
    }
}
