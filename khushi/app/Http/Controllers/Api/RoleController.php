<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::with(['admins', 'permissions']);

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $roles = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $roles,
            'message' => 'Roles retrieved successfully'
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean'
        ]);

        $role = Role::create($validated);

        // Attach permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load(['permissions']);

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Role created successfully'
        ], 201);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role): JsonResponse
    {
        $role->load(['admins', 'permissions']);

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Role retrieved successfully'
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $role->id . '|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean'
        ]);

        $role->update($validated);

        // Update permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load(['permissions']);

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Role updated successfully'
        ]);
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if role has admins assigned
        if ($role->admins()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role that has admins assigned'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($validated['permissions']);
        $role->load(['permissions']);

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Permissions assigned successfully'
        ]);
    }

    /**
     * Remove permissions from role
     */
    public function removePermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->detach($validated['permissions']);
        $role->load(['permissions']);

        return response()->json([
            'success' => true,
            'data' => $role,
            'message' => 'Permissions removed successfully'
        ]);
    }
}
