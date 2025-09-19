<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionManagementController extends Controller
{
    /**
     * Display a listing of permissions and users
     */
    public function index()
    {
        // Only super admins can access permission management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $admins = User::whereIn('role', ['admin', 'superadmin'])
                     ->with('permissions')
                     ->orderBy('role', 'desc')
                     ->orderBy('name')
                     ->get();
        
        $permissions = Permission::all()->groupBy('module');
        $modules = Permission::getModules();
        
        return view('admin.permissions.index', compact('admins', 'permissions', 'modules'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $modules = Permission::getModules();
        return view('admin.permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:100',
        ]);

        Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission created successfully!');
    }

    /**
     * Display the specified permission
     */
    public function show($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permission = Permission::findOrFail($id);
        $usersWithPermission = $permission->users()->get();
        
        return view('admin.permissions.show', compact('permission', 'usersWithPermission'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permission = Permission::findOrFail($id);
        $modules = Permission::getModules();
        
        return view('admin.permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Show user permissions for assignment
     */
    public function showUser(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permissions = Permission::all()->groupBy('module');
        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('admin.permissions.user', compact('user', 'permissions', 'userPermissions'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permission = Permission::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:100',
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission updated successfully!');
    }

    /**
     * Update user permissions
     */
    public function updateUser(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Assign the selected permissions
        $user->assignPermissions($request->permissions ?? []);

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permissions updated successfully for ' . $user->name);
    }

    /**
     * Remove the specified permission
     */
    public function destroy($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permission = Permission::findOrFail($id);
        
        // Check if permission is assigned to any users
        if ($permission->users()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                            ->with('error', 'Cannot delete permission that is assigned to users. Remove from users first.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permission deleted successfully!');
    }

    /**
     * Bulk update permissions for multiple users
     */
    public function bulkUpdate(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->assignPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully for ' . $user->name
        ]);
    }

    /**
     * Bulk assign permission to multiple users
     */
    public function bulkAssign(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        $users = User::whereIn('id', $request->user_ids)->get();
        
        foreach ($users as $user) {
            $user->permissions()->syncWithoutDetaching([$permission->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission assigned to ' . count($users) . ' users successfully'
        ]);
    }

    /**
     * Remove permission from user
     */
    public function removeFromUser(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_id' => 'required|exists:permissions,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $permission = Permission::findOrFail($request->permission_id);
        
        $user->permissions()->detach($permission->id);

        return response()->json([
            'success' => true,
            'message' => 'Permission removed from ' . $user->name . ' successfully'
        ]);
    }

    /**
     * Get permissions data for AJAX requests
     */
    public function getPermissions()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $permissions = Permission::with('users')->get()->groupBy('module');
        
        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Get user permissions data for AJAX requests
     */
    public function getUserPermissions(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $userPermissions = $user->permissions->pluck('name')->toArray();
        
        return response()->json([
            'success' => true,
            'permissions' => $userPermissions,
            'user' => $user
        ]);
    }
}
