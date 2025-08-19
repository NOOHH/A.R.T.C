<?php

namespace App\Http\Controllers\Smartprep\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tenant;
use App\Services\TenantProvisioner;
use Illuminate\Support\Facades\Auth;

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

        return view('smartprep.admin.clients.index', compact('clients', 'archivedClients'));
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
}
