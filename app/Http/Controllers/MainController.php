<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('id', 'desc')->get();
        return view('main_landing', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|alpha_dash|max:50|unique:clients,subdomain',
        ]);

        $name = $request->name;
        $subdomain = Str::slug($request->subdomain);

        $dbName = 'sub_domain_' . str_replace('-', '_', $subdomain);

        try {
            // 1. Create the MySQL database
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // 2. Create client record
            $client = Client::create([
                'name' => $name,
                'subdomain' => $subdomain,
                'db_host' => env('DB_HOST', '127.0.0.1'),
                'db_port' => env('DB_PORT', 3306),
                'db_name' => $dbName,
                'db_username' => env('DB_USERNAME', 'root'),
                'db_password' => env('DB_PASSWORD', '') ?? '',
                'status' => true,
            ]);

            // 3. Migrate the tenant database dynamically
            config([
                'database.connections.tenant.driver' => 'mysql',
                'database.connections.tenant.host' => $client->db_host,
                'database.connections.tenant.port' => $client->db_port,
                'database.connections.tenant.database' => $client->db_name,
                'database.connections.tenant.username' => $client->db_username,
                'database.connections.tenant.password' => $client->db_password,
            ]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            // Construct client url (resolves locally to localhost)
            $port = $request->getPort();
            $portSuffix = ($port && $port != 80 && $port != 443) ? ":{$port}" : "";
            $redirectUrl = $request->getScheme() . "://{$subdomain}.localhost" . $portSuffix . "/dashboard";

            return response()->json([
                'success' => true,
                'message' => "Tenant '{$name}' created and migrated successfully!",
                'subdomain' => $subdomain,
                'redirect_url' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to create tenant: " . $e->getMessage()
            ], 500);
        }
    }
}
