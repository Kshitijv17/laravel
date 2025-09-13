<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permission::with(['roles']);

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $permissions = $query->orderBy('module')
                            ->orderBy('name')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully'
        ]);
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $permission = Permission::create($validated);

        return response()->json([
            'success' => true,
            'data' => $permission,
            'message' => 'Permission created successfully'
        ], 201);
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission): JsonResponse
    {
        $permission->load(['roles']);

        return response()->json([
            'success' => true,
            'data' => $permission,
            'message' => 'Permission retrieved successfully'
        ]);
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:permissions,name,' . $permission->id . '|max:255',
            'description' => 'nullable|string',
            'module' => 'sometimes|string|max:255',
            'is_active' => 'boolean'
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'data' => $permission,
            'message' => 'Permission updated successfully'
        ]);
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete permission that is assigned to roles'
            ], 400);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }

    /**
     * Get permissions grouped by module
     */
    public function getByModule(): JsonResponse
    {
        $permissions = Permission::active()
                                ->orderBy('module')
                                ->orderBy('name')
                                ->get()
                                ->groupBy('module');

        return response()->json([
            'success' => true,
            'data' => $permissions,
            'message' => 'Permissions grouped by module retrieved successfully'
        ]);
    }

    /**
     * Get all available modules
     */
    public function getModules(): JsonResponse
    {
        $modules = Permission::select('module')
                            ->distinct()
                            ->orderBy('module')
                            ->pluck('module');

        return response()->json([
            'success' => true,
            'data' => $modules,
            'message' => 'Available modules retrieved successfully'
        ]);
    }
}
