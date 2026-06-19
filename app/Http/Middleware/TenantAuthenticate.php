<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class TenantAuthenticate
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
        // Check if there is a logged-in user in session
        if (!session()->has('tenant_user_id')) {
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        // Fetch user from the currently active tenant database
        $user = User::find(session('tenant_user_id'));

        if (!$user) {
            session()->forget('tenant_user_id');
            return $request->expectsJson() 
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        // Share authenticated user with views and bind to request
        view()->share('authUser', $user);
        
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
