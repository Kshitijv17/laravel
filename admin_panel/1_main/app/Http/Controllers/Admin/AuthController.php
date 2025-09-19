<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login form
    public function loginForm()
    {
        return view('admin.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->withInput();
    }

    // Show dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Handle logout
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('welcome');
    }

    // Show registration form
    public function registerForm()
    {
        $isFirstAdmin = \App\Models\Admin::count() === 0;
        $canCreateSuperAdmin = $isFirstAdmin || (auth('admin')->check() && auth('admin')->user()->isSuperAdmin());

        return view('admin.register', compact('isFirstAdmin', 'canCreateSuperAdmin'));
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'sometimes|in:admin,super_admin',
        ]);

        // Check if this is the first admin (make them super admin by default)
        $isFirstAdmin = \App\Models\Admin::count() === 0;

        // Only allow role selection for super admins or if no admins exist yet
        $role = $request->role ?? ($isFirstAdmin ? 'super_admin' : 'admin');

        // If not a super admin trying to set super_admin role, default to admin
        if ($role === 'super_admin' && (!auth('admin')->check() || !auth('admin')->user()->isSuperAdmin())) {
            $role = 'admin';
        }

        $admin = \App\Models\Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $role,
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    }
}
