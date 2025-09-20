<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Show login form
    public function loginForm()
    {
        // Redirect if already logged in
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
        }

        return view('admin.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && ($user->isAdmin() || $user->isSuperAdmin()) && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            // Redirect based on role
            if ($user->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            } else {
                return redirect()->route('shopkeeper.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not an admin account.',
        ])->withInput();
    }

    // Show dashboard - Always redirect to appropriate dashboard
    public function dashboard()
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access admin dashboard.');
        }

        // Redirect based on role
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        } elseif (auth()->user()->isAdmin()) {
            return redirect()->route('shopkeeper.dashboard');
        }

        // If not admin or superadmin, redirect to login
        return redirect()->route('admin.login')->with('error', 'Access denied. Admin privileges required.');
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('welcome');
    }

    // Show registration form
    public function registerForm()
    {
        // Redirect if already logged in
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->route('shopkeeper.dashboard');
            }
        }

        $isFirstAdmin = User::whereIn('role', ['admin', 'superadmin'])->count() === 0;
        return view('admin.register', compact('isFirstAdmin'));
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check if this is the first admin (make them admin by default)
        $isFirstAdmin = User::whereIn('role', ['admin', 'superadmin'])->count() === 0;
        $role = $isFirstAdmin ? 'admin' : 'admin'; // Regular admins only

        try {
            $userId = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'is_guest' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user = User::find($userId);
            Auth::login($user);

            return redirect()->route('shopkeeper.dashboard')->with('success', 'Admin account created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()]);
        }
    }
}
