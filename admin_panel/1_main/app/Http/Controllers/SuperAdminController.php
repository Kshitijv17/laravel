<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function registerForm()
    {
        // Redirect if already logged in as super admin
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('auth.super-admin-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            // Try a different approach - use DB facade directly
            $userId = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'superadmin',
                'is_guest' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get the user instance
            $user = User::find($userId);

            // Log the user in
            Auth::login($user);

            return redirect()->route('super-admin.dashboard')->with('success', 'Super Admin account created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()]);
        }
    }

    public function loginForm()
    {
        // Redirect if already logged in as super admin
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('auth.super-admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->isSuperAdmin() && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('super-admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not a Super Admin account.']);
    }

    public function dashboard()
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('super-admin.login')->with('error', 'Please login to access Super Admin dashboard.');
        }

        // Ensure only super admins can access
        if (!auth()->user()->isSuperAdmin()) {
            // If they're a regular admin, redirect to admin dashboard
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Access denied. Super Admin privileges required.');
            }
            // If they're not an admin at all, redirect to login
            return redirect()->route('super-admin.login')->with('error', 'Super Admin access required.');
        }

        return view('admin.dashboard');
    }
}
