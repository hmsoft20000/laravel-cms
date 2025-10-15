<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Traits\General\HasAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExampleController
{
    use HasAuthService;

    /**
     * Get current user info
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        // Check if user is authenticated
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Get user data
        $user = $this->getAuthenticatedUser($request);
        $userId = $this->getUserId($request);
        $userEmail = $this->getUserEmail($request);
        $userRoles = $this->getUserRoles($request);
        $userPermissions = $this->getUserPermissions($request);

        return response()->json([
            'user' => $user,
            'user_id' => $userId,
            'email' => $userEmail,
            'roles' => $userRoles,
            'permissions' => $userPermissions,
        ]);
    }

    /**
     * Check if user has specific permission
     */
    public function checkPermission(Request $request, string $permission): JsonResponse
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $hasPermission = $this->hasPermission($permission, $request);

        return response()->json([
            'permission' => $permission,
            'has_permission' => $hasPermission,
        ]);
    }

    /**
     * Check if user has specific role
     */
    public function checkRole(Request $request, string $role): JsonResponse
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $hasRole = $this->hasRole($role, $request);

        return response()->json([
            'role' => $role,
            'has_role' => $hasRole,
        ]);
    }

    /**
     * Get user's auth token
     */
    public function getToken(Request $request): JsonResponse
    {
        $token = $this->getAuthToken($request);

        if (!$token) {
            return response()->json(['error' => 'No token provided'], 400);
        }

        $isValid = $this->validateToken($token);

        return response()->json([
            'token' => $token,
            'is_valid' => $isValid,
        ]);
    }

    /**
     * Example of protected action that requires specific permission
     */
    public function protectedAction(Request $request): JsonResponse
    {
        // This method should be protected by middleware: cms.permission:manage-content
        // But we can also check manually if needed

        if (!$this->hasPermission('manage-content', $request)) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        // Your protected logic here
        return response()->json(['message' => 'Action completed successfully']);
    }
}
