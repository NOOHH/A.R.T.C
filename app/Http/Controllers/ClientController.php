<?php

namespace App\Http\Controllers;

use App\Console\Commands\ProvisionClient;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $activeClients = Client::query()->where('archived', false)->latest()->get();
        $archivedClients = Client::query()->where('archived', true)->latest()->get();
        $clients = $activeClients;

        // Append external A.R.T.C instance as a virtual client in the list
        $artcPath = 'C:/xampp/htdocs/A.R.T.C';
        if (is_dir($artcPath)) {
            $dbName = null;
            $envFile = $artcPath . '/.env';
            if (is_file($envFile)) {
                $envContents = @file_get_contents($envFile) ?: '';
                if (preg_match('/^DB_DATABASE=(.*)$/m', $envContents, $m)) {
                    $dbName = trim($m[1], "\r\n\"' ");
                }
            }
            $virtual = new Client([
                'name' => 'A.R.T.C',
                'slug' => 'artc',
                'domain' => null,
                'db_name' => $dbName,
                'db_host' => '127.0.0.1',
                'db_port' => 3306,
                'db_username' => 'root',
                'db_password' => '',
            ]);
            // Flag for the view - link via Apache (XAMPP) root, not artisan serve
            $virtual->setAttribute('external_url', 'http://localhost/A.R.T.C/public');
            // Put at top of the collection
            $clients = collect([$virtual])->merge($activeClients);
        }

        return view('admin.clients.index', compact('clients', 'archivedClients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'db' => 'nullable|string|max:255',
        ]);

        $name = $validated['name'];
        $slug = $validated['slug'] ?? Str::slug($name);
        $domain = $validated['domain'] ?? null;
        $db = $validated['db'] ?? null;

        $params = ['name' => $name, '--slug' => $slug];
        if ($domain) { $params['--domain'] = $domain; }
        if ($db) { $params['--db'] = $db; }

        Artisan::call('client:provision', $params);
        return redirect()->route('admin.clients.index')->with('status', 'Client provisioned: '.$name);
    }
    
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }
    
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:clients,slug,' . $client->id,
            'domain' => 'nullable|string|max:255',
            'db_name' => 'nullable|string|max:255',
        ]);
        
        $client->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'domain' => $validated['domain'] ?? $client->domain,
            'db_name' => $validated['db_name'] ?? $client->db_name,
        ]);
        
        return redirect()->route('admin.clients.index')->with('status', 'Client updated: '.$client->name);
    }
    
    public function archive(Client $client)
    {
        $client->update(['archived' => true]);
        return redirect()->route('admin.clients.index')->with('status', 'Client archived: '.$client->name);
    }
    
    public function unarchive(Client $client)
    {
        $client->update(['archived' => false]);
        return redirect()->route('admin.clients.index')->with('status', 'Client restored: '.$client->name);
    }
    
    public function destroy(Client $client)
    {
        // Store name before deletion for confirmation message
        $clientName = $client->name;
        
        // Get DB name for possible cleanup
        $dbName = $client->db_name;
        
        // Delete the client
        $client->delete();
        
        // We could add database cleanup code here if needed
        
        return redirect()->route('admin.clients.index')->with('status', 'Client deleted: '.$clientName);
    }
    
    public function showArchived()
    {
        $archivedClients = Client::query()->where('archived', true)->latest()->get();
        return view('admin.clients.archived', compact('archivedClients'));
    }
}



