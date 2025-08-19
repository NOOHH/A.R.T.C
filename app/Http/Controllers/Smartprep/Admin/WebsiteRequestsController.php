<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebsiteRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Services\TenantProvisioner;

class WebsiteRequestsController extends Controller
{
    public function index()
    {
        $requests = WebsiteRequest::with('user')
            ->orderByDesc('created_at')
            ->paginate(25);

        $recentRequests = WebsiteRequest::with('user')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('smartprep.admin.website-requests', compact('requests', 'recentRequests'));
    }
    
    public function approve(Request $request, $requestId)
    {
        $req = WebsiteRequest::findOrFail($requestId);
        if ($req->status === 'completed') {
            return back()->with('info', 'Request already completed.');
        }
        // We cannot run CREATE DATABASE inside an open transaction (MySQL auto-commits DDL),
        // so first provision the database OUTSIDE the transaction, then wrap only our
        // application record mutations (client, tenant, request) in a transaction.
        $conn = null; $client = null;
        try {
            // 1. Provision DB (may throw). If subsequent steps fail we'll drop it.
            $conn = TenantProvisioner::createDatabaseFromSqlDump($req->business_name);

            // 2. Persist/associate records atomically
            DB::beginTransaction();
            $client = $req->client_id ? Client::lockForUpdate()->find($req->client_id) : null;
            if ($client) {
                $client->update([
                    'name' => $req->business_name,
                    'domain' => $req->domain_preference ?: $client->domain,
                    'db_name' => $conn['db_name'],
                    'db_host' => $conn['db_host'],
                    'db_port' => $conn['db_port'],
                    'db_username' => $conn['db_username'],
                    'db_password' => $conn['db_password'],
                    'status' => 'active',
                ]);
            } else {
                $client = Client::create([
                    'name' => $req->business_name,
                    'slug' => 'smartprep-' . Str::slug($req->business_name),
                    'domain' => $req->domain_preference ?: null,
                    'db_name' => $conn['db_name'],
                    'db_host' => $conn['db_host'],
                    'db_port' => $conn['db_port'],
                    'db_username' => $conn['db_username'],
                    'db_password' => $conn['db_password'],
                    'status' => 'active',
                    'user_id' => $req->user_id,
                    'archived' => false,
                ]);
            }

            \App\Models\Tenant::updateOrCreate(
                ['slug' => $client->slug],
                [
                    'name' => $client->name,
                    'database_name' => $client->db_name,
                    'domain' => $client->domain,
                    'status' => 'active',
                    'settings' => json_encode(['created_from_request' => $req->id])
                ]
            );

            // 3. Seed tenant-specific identity (admin email, branding) so sites are not tied to SmartPrep defaults
            try {
                if (!empty($client->db_name)) {
                    config(['database.connections.tenant.database' => $client->db_name]);
                    DB::purge('tenant');
                    $tenantConn = DB::connection('tenant');

                    // Update or insert primary admin email (keep existing password)
                    if (!empty($req->contact_email)) {
                        $updated = $tenantConn->table('admins')->where('admin_id', 1)->update([
                            'admin_name' => $client->name,
                            'email' => $req->contact_email,
                        ]);
                        if ($updated === 0) {
                            $tenantConn->table('admins')->insert([
                                'admin_id' => 1,
                                'admin_name' => $client->name,
                                'email' => $req->contact_email,
                                // Set a temporary password; the tenant admin can change it later
                                'password' => bcrypt('ChangeMe123!'),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        // Update UI settings inside the tenant DB (contact email, site title/brand)
                        $upsert = function (string $section, string $key, string $value, string $type = 'text') use ($tenantConn) {
                            $tenantConn->table('ui_settings')->updateOrInsert(
                                ['section' => $section, 'setting_key' => $key],
                                ['setting_value' => $value, 'setting_type' => $type, 'updated_at' => now()]
                            );
                        };
                        $upsert('general', 'contact_email', $req->contact_email, 'text');
                        $upsert('general', 'site_title', $client->name, 'text');
                        $upsert('general', 'site_name', $client->name, 'text');
                        $upsert('navbar', 'brand_name', $client->name, 'text');
                    }
                }
            } catch (\Throwable $seedEx) {
                logger()->warning('Tenant identity seeding failed', [
                    'tenant_db' => $client->db_name,
                    'error' => $seedEx->getMessage(),
                ]);
                // Continue without failing the approval
            }

            $req->approved_at = now();
            $req->approved_by = auth()->id();
            $req->client_id = $client->id;
            $req->status = 'completed';
            $req->save();
            DB::commit();
            return redirect()->route('smartprep.admin.website-requests')->with('success', 'Website provisioned and completed.');
        } catch (\Throwable $e) {
            // Roll back any open transaction (ignore if none due to earlier failure)
            try { if (DB::transactionLevel() > 0) { DB::rollBack(); } } catch (\Throwable $ignored) {}
            // Drop DB if it was created but we failed later
            if (isset($conn['db_name'])) {
                try { TenantProvisioner::dropDatabase($conn['db_name']); } catch (\Throwable $e2) { }
            }
            return redirect()->route('smartprep.admin.website-requests')
                ->with('error', 'Provision FAILED – request left pending: ' . $e->getMessage());
        }
    }
    
    public function reject(Request $request, $requestId)
    {
        // Placeholder for reject functionality
        $request->validate([
            'admin_notes' => 'required|string'
        ]);

        $req = WebsiteRequest::findOrFail($requestId);
        $req->status = 'rejected';
        $req->admin_notes = $request->input('admin_notes');
        $req->save();

        // If a client was provisioned for this request but is a ghost (no domain and archived/draft), remove it
        if ($req->client_id) {
            $client = Client::find($req->client_id);
            if ($client && in_array($client->status, ['draft', 'inactive'])) {
                // Attempt to drop its database and then delete the client row
                TenantProvisioner::dropDatabase($client->db_name ?? null);
                $client->delete();
                $req->client_id = null;
                $req->save();
            }
        }

        return redirect()->route('smartprep.admin.website-requests')->with('success', 'Website request rejected successfully');
    }

    /**
     * Hard delete ghost website records (admin utility). Ghosts = clients without domain and draft/inactive.
     */
    public function purgeGhosts(Request $request)
    {
        $ghosts = Client::whereNull('domain')
            ->whereIn('status', ['draft', 'inactive'])
            ->get();

        $count = 0;
        foreach ($ghosts as $client) {
            TenantProvisioner::dropDatabase($client->db_name ?? null);
            $client->delete();
            $count++;
        }

        return back()->with('success', "Purged {$count} ghost websites.");
    }

    /**
     * Remove previously approved/completed websites whose databases are incomplete (< expected table count).
     */
    public function purgeInvalid(Request $request)
    {
        $expected = (int) env('SAMPLE_DB_EXPECTED_TABLES', 57);
        $clients = Client::whereNotNull('db_name')->get();
        $removed = 0; $requestReset = 0;
        foreach ($clients as $client) {
            try {
                $count = $this->tableCount($client->db_name);
                if ($count > 0 && $count < $expected) {
                    TenantProvisioner::dropDatabase($client->db_name);
                    \App\Models\Tenant::where('slug', $client->slug)->delete();
                    // Reset linked request if any
                    $req = WebsiteRequest::where('client_id', $client->id)->first();
                    if ($req) { $req->status = 'pending'; $req->client_id = null; $req->admin_notes = trim(($req->admin_notes ? $req->admin_notes.' ' : '') . '[auto-cleaned invalid DB]'); $req->save(); $requestReset++; }
                    $client->delete();
                    $removed++;
                }
            } catch (\Throwable $e) {
                // ignore individual errors to continue sweep
            }
        }
        return back()->with('success', "Removed {$removed} invalid websites; reset {$requestReset} requests.");
    }

    private function tableCount(string $dbName): int
    {
        $row = DB::selectOne('SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = ?', [$dbName]);
        return (int) ($row->c ?? 0);
    }
}
