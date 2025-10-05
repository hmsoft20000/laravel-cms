<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Helpers\UserModelHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserPermissionController extends Controller
{
    /**
     * Get user's permissions grouped by source
     */
    public function getUserPermissions(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's permissions
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $permissions = $user->getPermissionsGrouped();

        return successResponse(data: $permissions);
    }

    /**
     * Assign direct permission to user
     */
    public function assignPermission(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's permissions
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'permission_slug' => 'required|string|exists:permissions,slug',
        ]);

        $user->givePermissionTo($validated['permission_slug']);

        return successResponse(
            message: 'Permission assigned to user successfully',
            data: [
                'user' => $user->name,
                'permission' => $validated['permission_slug'],
            ],
        );
    }

    /**
     * Remove direct permission from user
     */
    public function revokePermission(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's permissions
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'permission_slug' => 'required|string|exists:permissions,slug',
        ]);

        $user->revokePermission($validated['permission_slug']);

        return successResponse(
            message: 'Permission revoked from user successfully',
            data: [
                'user' => $user->name,
                'permission' => $validated['permission_slug'],
            ],
        );
    }

    /**
     * Sync user's direct permissions
     */
    public function syncPermissions(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's permissions
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'permission_slugs' => 'required|array',
            'permission_slugs.*' => 'string|exists:permissions,slug',
        ]);

        $user->syncPermissions($validated['permission_slugs']);

        return successResponse(
            message: 'User permissions synced successfully',
            data: [
                'user' => $user->name,
                'permissions_count' => count($validated['permission_slugs']),
            ],
        );
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's roles
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'role_slug' => 'required|string|exists:roles,slug',
        ]);

        $user->assignRole($validated['role_slug']);

        return successResponse(
            message: 'Role assigned to user successfully',
            data: [
                'user' => $user->name,
                'role' => $validated['role_slug'],
            ],
        );
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's roles
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'role_slug' => 'required|string|exists:roles,slug',
        ]);

        $user->removeRole($validated['role_slug']);

        return successResponse(
            message: 'Role removed from user successfully',
            data: [
                'user' => $user->name,
                'role' => $validated['role_slug'],
            ],
        );
    }

    /**
     * Sync user's roles
     */
    public function syncRoles(Request $request, $userId): JsonResponse
    {
        // Check if current user can manage this user's roles
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $validated = $request->validate([
            'role_slugs' => 'required|array',
            'role_slugs.*' => 'string|exists:roles,slug',
        ]);

        $user->syncRoles($validated['role_slugs']);

        return successResponse(
            message: 'User roles synced successfully',
            data: [
                'user' => $user->name,
                'roles_count' => count($validated['role_slugs']),
            ],
        );
    }

    /**
     * Get user's complete authorization profile
     */
    public function getAuthorizationProfile($userId): JsonResponse
    {
        // Check if current user can view this user's profile
        // $this->authorize('manage', $user);

        $user = UserModelHelper::findUser($userId);
        if (!$user) {
            return errorResponse('User not found', 404);
        }

        $profile = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ],
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'level' => $role->level,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'slug' => $permission->slug,
                            'module' => $permission->module,
                        ];
                    }),
                ];
            }),
            'direct_permissions' => $user->getDirectPermissions()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'module' => $permission->module,
                ];
            }),
            'all_permissions' => collect($user->getAllPermissions())->map(function ($slug) {
                $permission = \HMsoft\Cms\Models\Permission::where('slug', $slug)->first();
                return $permission ? [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'module' => $permission->module,
                ] : null;
            })->filter(),
            'permission_summary' => [
                'total_roles' => $user->roles->count(),
                'total_direct_permissions' => $user->getDirectPermissions()->count(),
                'total_effective_permissions' => count($user->getAllPermissions()),
            ],
        ];

        return successResponse(data: $profile);
    }

    /**
     * Bulk assign permissions to multiple users
     */
    public function bulkAssignPermissions(Request $request): JsonResponse
    {
        // Only super admins can do bulk operations
        // $this->authorize('manage', User::class);

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'permission_slugs' => 'required|array',
            'permission_slugs.*' => 'string|exists:permissions,slug',
            'operation' => 'required|in:assign,revoke,sync',
        ]);

        $userModelClass = UserModelHelper::getUserModelClass();
        $users = $userModelClass::whereIn('id', $validated['user_ids'])->get();
        $affectedCount = 0;

        foreach ($users as $user) {
            try {
                switch ($validated['operation']) {
                    case 'assign':
                        foreach ($validated['permission_slugs'] as $permissionSlug) {
                            $user->givePermissionTo($permissionSlug);
                        }
                        break;
                    case 'revoke':
                        foreach ($validated['permission_slugs'] as $permissionSlug) {
                            $user->revokePermission($permissionSlug);
                        }
                        break;
                    case 'sync':
                        $user->syncPermissions($validated['permission_slugs']);
                        break;
                }
                $affectedCount++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                Log::error("Failed to {$validated['operation']} permissions for user {$user->id}: " . $e->getMessage());
            }
        }

        return successResponse(
            message: "Permissions {$validated['operation']}d for {$affectedCount} out of " . count($validated['user_ids']) . ' users',
            data: [
                'operation' => $validated['operation'],
                'permissions_count' => count($validated['permission_slugs']),
                'affected_users' => $affectedCount,
                'total_users' => count($validated['user_ids']),
            ],
        );
    }

    /**
     * Bulk assign roles to multiple users
     */
    public function bulkAssignRoles(Request $request): JsonResponse
    {
        // Only super admins can do bulk operations
        // $this->authorize('manage', User::class);

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role_slugs' => 'required|array',
            'role_slugs.*' => 'string|exists:roles,slug',
            'operation' => 'required|in:assign,remove,sync',
        ]);

        $userModelClass = UserModelHelper::getUserModelClass();
        $users = $userModelClass::whereIn('id', $validated['user_ids'])->get();
        $affectedCount = 0;

        foreach ($users as $user) {
            try {
                switch ($validated['operation']) {
                    case 'assign':
                        foreach ($validated['role_slugs'] as $roleSlug) {
                            $user->assignRole($roleSlug);
                        }
                        break;
                    case 'remove':
                        foreach ($validated['role_slugs'] as $roleSlug) {
                            $user->removeRole($roleSlug);
                        }
                        break;
                    case 'sync':
                        $user->syncRoles($validated['role_slugs']);
                        break;
                }
                $affectedCount++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                Log::error("Failed to {$validated['operation']} roles for user {$user->id}: " . $e->getMessage());
            }
        }

        return successResponse(
            message: "Roles {$validated['operation']}d for {$affectedCount} out of " . count($validated['user_ids']) . ' users',
            data: [
                'operation' => $validated['operation'],
                'roles_count' => count($validated['role_slugs']),
                'affected_users' => $affectedCount,
                'total_users' => count($validated['user_ids']),
            ],
        );
    }
}
