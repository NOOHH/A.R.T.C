<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\Smartprep\User as SmartprepUser; // SmartPrep auth users use 'id' PK and 'name'
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::with('user')
            ->where('archived', false)
            ->orderByDesc('created_at')
            ->get();
        $archivedClients = Client::with('user')
            ->where('archived', true)
            ->orderByDesc('updated_at')
            ->get();

    // All users (for search + visibility), mark those already with a website
    $allUsers = SmartprepUser::orderBy('name')->limit(1000)->get();
    $clientsByUser = Client::whereNotNull('user_id')->get()->keyBy('user_id');

    return view('smartprep.admin.clients.index', compact('clients', 'archivedClients', 'allUsers', 'clientsByUser'));
    }
    
    public function create()
    {
        return view('smartprep.admin.clients.create');
    }
    
    public function edit($id)
    {
        $client = Client::with('user')->findOrFail($id);
        return view('smartprep.admin.clients.edit', ['client' => $client]);
    }
    
    public function archive($id)
    {
        $client = Client::findOrFail($id);
        $client->archived = true;
        $client->status = 'inactive';
        $client->save();

        // Reflect in tenants registry
        Tenant::where('slug', $client->slug)->update(['status' => 'archived']);
        return redirect()->route('smartprep.admin.clients')->with('success', 'Client archived successfully');
    }
    
    public function unarchive($id)
    {
        $client = Client::findOrFail($id);
        $client->archived = false;
        if ($client->status === 'inactive') {
            $client->status = 'active';
        }
        $client->save();

        Tenant::where('slug', $client->slug)->update(['status' => 'active']);
        return redirect()->route('smartprep.admin.clients')->with('success', 'Client unarchived successfully');
    }
    
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $dbName = $client->db_name;
        $slug = $client->slug;
        $client->delete();

        // Remove tenant registry and drop tenant database if it matches naming pattern
        Tenant::where('slug', $slug)->delete();
        if ($dbName && str_starts_with($dbName, 'smartprep_')) {
            try {
                TenantProvisioner::dropDatabase($dbName);
            } catch (\Throwable $e) {
                // Log and continue; UI deletion still succeeds
                logger()->warning('Failed to drop tenant database on client delete', [
                    'db' => $dbName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        return redirect()->route('smartprep.admin.clients')->with('success', 'Client deleted successfully');
    }

    /**
     * Provision a new client website for an existing platform user.
     */
    public function provision(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name'    => 'required|string|min:2|max:120',
        ]);

        // Ensure the user does not already have a website
    if (Client::where('user_id', $data['user_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user already has a client website.'
            ], 422);
        }

        // Generate unique slug (kebab-case). Keep adding numeric suffix until unique.
    $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug ?: ('client-' . $data['user_id']);
        $suffix = 2;
        while (Client::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
            if ($suffix > 50) { // fallback randomness
                $slug = $baseSlug . '-' . Str::lower(Str::random(4));
                break;
            }
        }

        try {
            // Provision tenant DB from sample dump
            $conn = TenantProvisioner::createDatabaseFromSqlDump($data['name']);

            // Create as draft so the admin can configure branding & content before going live
            $client = Client::create([
                'name'        => $data['name'],
                'slug'        => $slug,
                'domain'      => null,
                'db_name'     => $conn['db_name'] ?? null,
                'db_host'     => $conn['db_host'] ?? env('DB_HOST'),
                'db_port'     => $conn['db_port'] ?? env('DB_PORT', 3306),
                'db_username' => $conn['db_username'] ?? env('DB_USERNAME'),
                'db_password' => $conn['db_password'] ?? env('DB_PASSWORD'),
                'status'      => 'draft',
                'user_id'     => $data['user_id'],
                'archived'    => false,
            ]);

            // Ensure tenant registry row (if there is a Tenant model expectation elsewhere)
            Tenant::updateOrCreate(
                ['slug' => $client->slug],
                [
                    'name' => $client->name,
                    'database_name' => $client->db_name,
                    'domain' => $client->domain,
                    'status' => $client->status, // draft
                    'settings' => ['client_id' => $client->id]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Website draft created. Redirecting you to settings to finish setup...',
                'data' => [
                    'client' => $client,
                    'url' => url('/t/' . $client->slug),
                    'admin_url' => url('/t/' . $client->slug . '/admin/dashboard'),
                    'settings_url' => route('smartprep.admin.settings', ['client' => $client->slug])
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provisioning failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
