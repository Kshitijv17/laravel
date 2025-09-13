<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of admins
     */
    public function index(Request $request): JsonResponse
    {
        $query = Admin::with(['roles']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $admins = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $admins,
            'message' => 'Admins retrieved successfully'
        ]);
    }

    /**
     * Store a newly created admin
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string',
            'permissions' => 'nullable|array',
            'status' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $admin = Admin::create($validated);
        $admin->load(['roles']);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'Admin created successfully'
        ], 201);
    }

    /**
     * Display the specified admin
     */
    public function show(Admin $admin): JsonResponse
    {
        $admin->load(['roles', 'auditTrails']);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'Admin retrieved successfully'
        ]);
    }

    /**
     * Update the specified admin
     */
    public function update(Request $request, Admin $admin): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admins,email,' . $admin->id,
            'password' => 'sometimes|string|min:8',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string',
            'permissions' => 'nullable|array',
            'status' => 'boolean'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $admin->update($validated);
        $admin->load(['roles']);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'Admin updated successfully'
        ]);
    }

    /**
     * Remove the specified admin
     */
    public function destroy(Admin $admin): JsonResponse
    {
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully'
        ]);
    }

    /**
     * Admin login
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$admin->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 401);
        }

        // Update last login
        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'admin' => $admin,
                'token' => $token
            ],
            'message' => 'Login successful'
        ]);
    }

    /**
     * Get admin profile
     */
    public function profile(): JsonResponse
    {
        $admin = Auth::user();
        $admin->load(['roles']);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'Profile retrieved successfully'
        ]);
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $admin = Auth::user();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string'
        ]);

        $admin->update($validated);

        return response()->json([
            'success' => true,
            'data' => $admin,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $admin = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if (!Hash::check($validated['current_password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        $admin->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
