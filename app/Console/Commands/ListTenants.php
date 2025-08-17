<?php

namespace App\Console\Commands;

use App\Services\TenantService;
use Illuminate\Console\Command;

class ListTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tenants';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenants = $this->tenantService->getAllTenants();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return 0;
        }

        $this->info('All Tenants:');
        $this->table(
            ['ID', 'Name', 'Domain', 'Database', 'Created At'],
            $tenants->map(function ($tenant) {
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $tenant->database_name,
                    $tenant->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );

        return 0;
    }
}
