<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Http\Request;

trait HasPolicyAuth
{
    /**
     * Get the authenticated user for policy from request
     */
    protected function getPolicyUser(Request|null $request = null)
    {
        $request = $request ?? request();

        if ($request && method_exists($request, 'user')) {
            $user = $request->user();
            if ($user) {
                return $user;
            }
        }

        // Fallback to Laravel's auth system
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user) {
                return $user;
            }
        } catch (\Exception $e) {
            // Ignore auth errors
        }

        // Last resort: try to get user from auth service
        try {
            $authService = app(\HMsoft\Cms\Contracts\AuthServiceInterface::class);
            return $authService->getAuthenticatedUser($request);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if user is authenticated for policy
     */
    protected function isPolicyUserAuthenticated(Request|null $request = null): bool
    {
        $user = $this->getPolicyUser($request);
        return $user !== null;
    }

    /**
     * Check if user has permission for policy
     */
    protected function policyUserHasPermission(string $permission, Request|null $request = null): bool
    {
        $user = $this->getPolicyUser($request);
        if (!$user) {
            return false;
        }

        // Check if user has the permission (using custom permission system)
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        // Fallback: check if user has the permission in a custom way
        return $this->checkCustomPermission($user, $permission);
    }

    /**
     * Check if user has role for policy
     */
    protected function policyUserHasRole(string $role, Request|null $request = null): bool
    {
        $user = $this->getPolicyUser($request);
        if (!$user) {
            return false;
        }

        // Check if user has the role (using Spatie Permission if available)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Fallback: check if user has the role in a custom way
        return $this->checkCustomRole($user, $role);
    }

    /**
     * Get user ID for policy
     */
    protected function getPolicyUserId(Request|null $request = null): ?int
    {
        $user = $this->getPolicyUser($request);
        return $user ? $user->id : null;
    }

    /**
     * Check custom permission (fallback method)
     */
    private function checkCustomPermission($user, string $permission): bool
    {
        // Implement your custom permission logic here
        // This is a fallback when Spatie Permission is not available
        return false;
    }

    /**
     * Check custom role (fallback method)
     */
    private function checkCustomRole($user, string $role): bool
    {
        // Implement your custom role logic here
        // This is a fallback when Spatie Permission is not available
        return false;
    }
}
