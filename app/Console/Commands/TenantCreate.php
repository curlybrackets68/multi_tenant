<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name : The name of the client/tenant} {subdomain : The subdomain for the client}';
    // Example: php artisan tenant:create "Pinank Pvt Ltd" pinank 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant, create their database, and run migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $subdomain = Str::slug($this->argument('subdomain')); // Ensure URL-friendly, lowercase alphanumeric

        $dbName = 'sub_domain_' . str_replace('-', '_', $subdomain);

        $this->info("Creating tenant: {$name} with subdomain: {$subdomain}");

        // Check if subdomain already exists
        if (Client::where('subdomain', $subdomain)->exists()) {
            $this->error("Error: The subdomain '{$subdomain}' is already taken.");
            return Command::FAILURE;
        }

        // 1. Create the MySQL database
        $this->info("Creating database: {$dbName}");
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $this->info("Database '{$dbName}' created or already exists.");
        } catch (\Exception $e) {
            $this->error("Failed to create database '{$dbName}': " . $e->getMessage());
            return Command::FAILURE;
        }

        // 2. Insert the client record
        $this->info("Saving client record in master database...");
        try {
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
            $this->info("Client record saved with ID: {$client->id}");
        } catch (\Exception $e) {
            $this->error("Failed to save client record: " . $e->getMessage());
            return Command::FAILURE;
        }

        // 3. Migrate the tenant database
        $this->info("Running tenant migrations for database: {$dbName}...");
        
        // Dynamically set connection configuration
        config([
            'database.connections.tenant.driver' => 'mysql',
            'database.connections.tenant.host' => $client->db_host,
            'database.connections.tenant.port' => $client->db_port,
            'database.connections.tenant.database' => $client->db_name,
            'database.connections.tenant.username' => $client->db_username,
            'database.connections.tenant.password' => $client->db_password,
        ]);

        try {
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Run the migrations
            $exitCode = Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            if ($exitCode === 0) {
                $this->info("Tenant database migrated successfully.");
                $this->line(Artisan::output());
            } else {
                $this->error("Tenant database migration failed.");
                $this->line(Artisan::output());
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("Failed to migrate tenant database: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("Tenant {$name} ({$subdomain}) created successfully!");
        $this->info("Access it at: http://{$subdomain}.localhost:8000");

        return Command::SUCCESS;
    }
}
