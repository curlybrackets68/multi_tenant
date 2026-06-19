<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Redirect to dashboard if the user already has an active session
        if (session()->has('tenant_user_id')) {
            return redirect()->route('tenant.dashboard');
        }

        return $next($request);
    }
}
