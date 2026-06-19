<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TenantAuthController extends Controller
{
    public function showLogin()
    {
        $client = app('client');
        return view('tenant_login', compact('client'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Plaintext password comparison as requested by the user
        if ($user && $user->password === $request->password) {
            session(['tenant_user_id' => $user->id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logged in successfully!',
                    'redirect_url' => route('tenant.dashboard')
                ]);
            }

            return redirect()->route('tenant.dashboard');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        session()->forget('tenant_user_id');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('login')
            ]);
        }

        return redirect()->route('login');
    }
}
