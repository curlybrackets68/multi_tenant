<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function dashboard()
    {
        $client = app('client');
        $users = User::orderBy('id', 'desc')->get();
        return view('tenant_dashboard', compact('client', 'users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json([
                'success' => true,
                'message' => "User '{$user->name}' created successfully in this tenant's isolated database!",
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to create user: " . $e->getMessage()
            ], 500);
        }
    }
}
