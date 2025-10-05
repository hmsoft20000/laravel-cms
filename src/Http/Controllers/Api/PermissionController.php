<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Permission\UpdateAllPermissionRequest;
use HMsoft\Cms\Models\Permission;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user can view permissions
        // $this->authorize('manage', Permission::class);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: new Permission(),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($request) {
                // Filter by module if provided
                if ($request->has('module') && $request->module) {
                    $query->where('module', $request->module);
                }
            },
        );

        $result['data'] = collect($result['data'])->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'description' => $permission->description,
                'module' => $permission->module,
                'roles_count' => $permission->roles()->count(),
                'users_count' => $permission->users()->count(),
                'created_at' => $permission->created_at,
                'updated_at' => $permission->updated_at,
            ];
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can manage permissions
        // $this->authorize('manage', Permission::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
        ]);

        $permission = Permission::create($validated);

        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'description' => $permission->description,
                'module' => $permission->module,
                'created_at' => $permission->created_at,
            ],
        );
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        // Check if user can view permissions
        // $this->authorize('manage', Permission::class);

        return successResponse(data: [
            'id' => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->slug,
            'description' => $permission->description,
            'module' => $permission->module,
            'roles' => $permission->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                ];
            }),
            'users' => $permission->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }),
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
        ]);
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        // Check if user can manage permissions
        // $this->authorize('manage', Permission::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
            'module' => 'nullable|string|max:100',
        ]);

        $permission->update($validated);

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'description' => $permission->description,
                'module' => $permission->module,
                'updated_at' => $permission->updated_at,
            ],
        );
    }

    public function updateAll(UpdateAllPermissionRequest $request): JsonResponse
    {
        // $this->authorize('manage', Permission::class);

        $updatedPermissions = [];
        foreach ($request->all() as $permissionData) {
            if (isset($permissionData['id'])) {
                $permission = Permission::findOrFail($permissionData['id']);
                
                $permission->update([
                    'name' => $permissionData['name'] ?? $permission->name,
                    'slug' => $permissionData['slug'] ?? $permission->slug,
                    'description' => $permissionData['description'] ?? $permission->description,
                    'module' => $permissionData['module'] ?? $permission->module,
                ]);

                $updatedPermissions[] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'description' => $permission->description,
                    'module' => $permission->module,
                    'updated_at' => $permission->updated_at,
                ];
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: $updatedPermissions
        );
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        // Check if user can manage permissions
        // $this->authorize('manage', Permission::class);

        // Check if permission is being used
        if ($permission->roles()->count() > 0 || $permission->users()->count() > 0) {
            return errorResponse(
                message: 'Cannot delete permission that is assigned to roles or users',
                state: 400
            );
        }

        $permission->delete();

        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }

    /**
     * Get permissions grouped by module.
     */
    public function getByModule(): JsonResponse
    {
        // Check if user can view permissions
        // $this->authorize('manage', Permission::class);

        $permissions = Permission::all()->groupBy('module')->map(function ($modulePermissions, $module) {
            return [
                'module' => $module ?: 'general',
                'permissions' => $modulePermissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                    ];
                })
            ];
        })->values();

        return successResponse(data: $permissions);
    }

    /**
     * Get all unique modules.
     */
    public function getModules(): JsonResponse
    {
        // Check if user can view permissions
        // $this->authorize('manage', Permission::class);

        $modules = Permission::distinct('module')
            ->whereNotNull('module')
            ->pluck('module')
            ->map(function ($module) {
                return [
                    'name' => $module,
                    'permissions_count' => Permission::where('module', $module)->count(),
                ];
            });

        return successResponse(data: $modules);
    }

    /**
     * Bulk assign permissions to roles.
     */
    public function bulkAssignToRole(Request $request): JsonResponse
    {
        // Check if user can manage permissions
        // $this->authorize('manage', Permission::class);

        $validated = $request->validate([
            'role' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = \HMsoft\Cms\Models\Role::findOrFail($validated['role']);
        $role->permissions()->sync($validated['permission_ids']);

        return successResponse(
            message: 'Permissions assigned to role successfully',
            data: [
                'role' => $role->name,
                'permissions_count' => count($validated['permission_ids']),
            ],
        );
    }
}
