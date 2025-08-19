<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PromoteClientToTenant extends Command
{
    protected $signature = 'tenant:promote {slug} {--force}';
    protected $description = 'Create a tenants row from an existing clients row (slug, db_name)';

    public function handle(): int
    {
        $slug = $this->argument('slug');
        $client = DB::table('clients')->where('slug',$slug)->first();
        if (!$client) {
            $this->error("Client with slug {$slug} not found.");
            return 1;
        }
        $exists = DB::table('tenants')->where('slug',$slug)->first();
        if ($exists && !$this->option('force')) {
            $this->warn('Tenant already exists. Use --force to overwrite.');
            return 0;
        }
        if ($exists) {
            DB::table('tenants')->where('id',$exists->id)->delete();
        }
        DB::table('tenants')->insert([
            'name' => $client->name,
            'slug' => $client->slug,
            'database_name' => $client->db_name,
            'domain' => $client->domain ?? null,
            'status' => $client->status ?? 'active',
            'settings' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->info('Promoted client to tenant: '.$slug.' -> '.$client->db_name);
        return 0;
    }
}
