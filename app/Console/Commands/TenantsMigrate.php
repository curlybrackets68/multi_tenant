<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantsMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate {--fresh : Whether to run migrate:fresh instead of migrate}';
    // Example: php artisan tenants:migrate
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations on all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clients = Client::where('status', true)->get();

        if ($clients->isEmpty()) {
            $this->info('No active tenants found to migrate.');
            return Command::SUCCESS;
        }

        $isFresh = $this->option('fresh');
        $action = $isFresh ? 'migrate:fresh' : 'migrate';

        foreach ($clients as $client) {
            $this->info("----------------------------------");
            $this->info("Migrating Tenant: {$client->name} ({$client->subdomain})");
            $this->info("Database: {$client->db_name}");

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
                $exitCode = Artisan::call($action, [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenant',
                    '--force' => true,
                ]);

                if ($exitCode === 0) {
                    $this->info("Tenant {$client->subdomain} migrated successfully.");
                } else {
                    $this->error("Tenant {$client->subdomain} migration failed.");
                }

                $this->line(Artisan::output());

            } catch (\Exception $e) {
                $this->error("Failed to connect or migrate: " . $e->getMessage());
            }
        }

        $this->info("----------------------------------");
        $this->info('Tenant migrations run completed.');

        return Command::SUCCESS;
    }
}
