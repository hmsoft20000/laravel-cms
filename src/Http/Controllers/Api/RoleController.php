<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\Role\UpdateAllRoleRequest;
use HMsoft\Cms\Models\Role;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Role::class),
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query) use ($request) {
                $query->with(['parent', 'permissions']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'level' => $role->level,
                'parent' => $role->parent ? [
                    'id' => $role->parent->id,
                    'name' => $role->parent->name,
                    'slug' => $role->parent->slug,
                ] : null,
                'permissions_count' => $role->permissions->count(),
                'users_count' => $role->users()->count(),
                'children_count' => $role->children()->count(),
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ];
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Get roles in tree structure.
     */
    public function tree(): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $tree = Role::getTree();

        return successResponse(data: $tree);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1|max:100',
            'parent_id' => 'nullable|exists:roles,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        // Prevent circular reference
        if ($validated['parent_id']) {
            $this->validateParentRelationship($validated['parent_id']);
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'],
        ]);

        // Assign permissions if provided
        if (isset($validated['permission_ids'])) {
            $role->permissions()->sync($validated['permission_ids']);
        }

        return successResponse(
            message: translate('cms::messages.added_successfully'),
            data: [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'level' => $role->level,
                'parent_id' => $role->parent_id,
                'permissions_count' => $role->permissions()->count(),
                'created_at' => $role->created_at,
            ],
        );
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $role->load(['parent', 'children', 'permissions', 'users']);

        return successResponse(data: [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'level' => $role->level,
            'parent' => $role->parent ? [
                'id' => $role->parent->id,
                'name' => $role->parent->name,
                'slug' => $role->parent->slug,
            ] : null,
            'children' => $role->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'level' => $child->level,
                ];
            }),
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'module' => $permission->module,
                ];
            }),
            'users' => $role->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }),
            'ancestors' => $role->ancestors()->map(function ($ancestor) {
                return [
                    'id' => $ancestor->id,
                    'name' => $ancestor->name,
                    'slug' => $ancestor->slug,
                ];
            }),
            'descendants' => $role->descendants->map(function ($descendant) {
                return $descendant->toTreeArray();
            }),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1|max:100',
            'parent_id' => 'nullable|exists:roles,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] && $validated['parent_id'] != $role->parent_id) {
            $this->validateParentRelationship($validated['parent_id'], $role->id);
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'],
        ]);

        // Update permissions if provided
        if (isset($validated['permission_ids'])) {
            $role->permissions()->sync($validated['permission_ids']);
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'level' => $role->level,
                'parent_id' => $role->parent_id,
                'permissions_count' => $role->permissions()->count(),
                'updated_at' => $role->updated_at,
            ],
        );
    }

    public function updateAll(UpdateAllRoleRequest $request): JsonResponse
    {
        // $this->authorize('manage', Role::class);

        $updatedRoles = [];
        foreach ($request->all() as $roleData) {
            if (isset($roleData['id'])) {
                $role = Role::findOrFail($roleData['id']);

                // Prevent circular reference
                if (isset($roleData['parent_id']) && $roleData['parent_id'] != $role->parent_id) {
                    $this->validateParentRelationship($roleData['parent_id'], $role->id);
                }

                $role->update([
                    'name' => $roleData['name'] ?? $role->name,
                    'slug' => $roleData['slug'] ?? $role->slug,
                    'description' => $roleData['description'] ?? $role->description,
                    'level' => $roleData['level'] ?? $role->level,
                    'parent_id' => $roleData['parent_id'] ?? $role->parent_id,
                ]);

                // Update permissions if provided
                if (isset($roleData['permission_ids'])) {
                    $role->permissions()->sync($roleData['permission_ids']);
                }

                $updatedRoles[] = [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'level' => $role->level,
                    'parent_id' => $role->parent_id,
                    'permissions_count' => $role->permissions()->count(),
                    'updated_at' => $role->updated_at,
                ];
            }
        }

        return successResponse(
            message: translate('cms::messages.updated_successfully'),
            data: $updatedRoles
        );
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        // Check if role has children
        if ($role->children()->count() > 0) {
            return errorResponse(
                message: 'Cannot delete role that has child roles',
                state: 400
            );
        }

        // Check if role is assigned to users
        if ($role->users()->count() > 0) {
            return errorResponse(
                message: 'Cannot delete role that is assigned to users',
                state: 400
            );
        }

        $role->delete();

        return successResponse(
            message: translate('cms::messages.deleted_successfully'),
        );
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permission_ids']);

        return successResponse(
            message: 'Permissions assigned to role successfully',
            data: [
                'role' => $role->name,
                'permissions_count' => count($validated['permission_ids']),
            ],
        );
    }

    /**
     * Remove permissions from role.
     */
    public function removePermissions(Request $request, Role $role): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->detach($validated['permission_ids']);

        return successResponse(
            message: 'Permissions removed from role successfully',
            data: [
                'role' => $role->name,
                'removed_count' => count($validated['permission_ids']),
            ],
        );
    }

    /**
     * Get available parent roles for a role.
     */
    public function availableParents(Role $role = null): JsonResponse
    {
        // Check if user can manage roles
        // $this->authorize('manage', Role::class);

        $query = Role::query();

        // Exclude the role itself and its descendants
        if ($role) {
            $descendantIds = collect($role->descendants)->pluck('id')->push($role->id);
            $query->whereNotIn('id', $descendantIds);
        }

        $parents = $query->orderBy('level')->orderBy('name')->get(['id', 'name', 'slug', 'level']);

        return successResponse(data: $parents);
    }

    /**
     * Validate parent relationship to prevent circular references.
     */
    private function validateParentRelationship(int $parentId, int $excludeId = null): void
    {
        $parent = Role::find($parentId);

        if (!$parent) {
            return; // Let Laravel's exists validation handle this
        }

        $currentId = $excludeId;
        $checkedIds = [];

        while ($parent) {
            if (in_array($parent->id, $checkedIds)) {
                throw new \Illuminate\Validation\ValidationException(
                    \Illuminate\Validation\Validator::make([], [], [], [], [])
                        ->errors()
                        ->add('parent_id', 'Circular reference detected in role hierarchy')
                );
            }

            $checkedIds[] = $parent->id;

            if ($currentId && $parent->id === $currentId) {
                throw new \Illuminate\Validation\ValidationException(
                    \Illuminate\Validation\Validator::make([], [], [], [], [])
                        ->errors()
                        ->add('parent_id', 'Cannot set role as its own ancestor')
                );
            }

            $parent = $parent->parent;
        }
    }
}
