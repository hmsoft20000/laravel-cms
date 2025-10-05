<?php

namespace HMsoft\Cms\Helpers;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;

class PolicyHelper
{
    protected static AuthServiceInterface $authService;

    /**
     * Get the authenticated user for policy
     */
    public static function getAuthenticatedUser(Request|null $request = null): mixed
    {
        $request = $request ?? request();
        
        if (!$request) {
            return null;
        }
        // Try to get user from request first
        if (method_exists($request, 'user')) {
            $user = $request->user();
            if ($user) {
                return $user;
            }
        }

        // Try Laravel's auth system
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user) {
                return $user;
            }
        } catch (\Exception $e) {
            // Ignore auth errors
        }

        // Last resort: use auth service
        try {
            if (!isset(self::$authService)) {
                self::$authService = app(AuthServiceInterface::class);
            }
            
            return self::$authService->getAuthenticatedUser($request);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(Request|null $request = null): bool
    {
        return self::getAuthenticatedUser($request) !== null;
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission(string $permission, Request|null $request = null): bool
    {
        $user = self::getAuthenticatedUser($request);
        if (!$user) {
            return false;
        }

        // Check if user has the permission (using custom permission system)
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        return false;
    }

    /**
     * Check if user has role
     */
    public static function hasRole(string $role, Request|null $request = null): bool
    {
        $user = self::getAuthenticatedUser($request);
        if (!$user) {
            return false;
        }

        // Check if user has the role (using Spatie Permission if available)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return false;
    }

    /**
     * Check if user has any of the given roles
     */
    public static function hasAnyRole(array $roles, Request|null $request = null): bool
    {
        $user = self::getAuthenticatedUser($request);
        if (!$user) {
            return false;
        }

        foreach ($roles as $role) {
            if (self::hasRole($role, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given roles
     */
    public static function hasAllRoles(array $roles, Request|null $request = null): bool
    {
        $user = self::getAuthenticatedUser($request);
        if (!$user) {
            return false;
        }

        foreach ($roles as $role) {
            if (!self::hasRole($role, $request)) {
                return false;
            }
        }

        return true;
    }
}
