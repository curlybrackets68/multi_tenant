<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        $parts = explode('.', $host);

        $subdomain = $parts[0];

        $client = Client::on('master')
            ->where('subdomain', $subdomain)
            ->where('status', 1)
            ->first();

        if (! $client) {
            abort(404, 'Invalid Client');
        }

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

        DB::setDefaultConnection('tenant');

        app()->instance('client', $client);

        return $next($request);
    }
}
