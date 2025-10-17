<?php

namespace HMsoft\Cms\Helpers;

use HMsoft\Cms\Contracts\AuthServiceInterface;
use HMsoft\Cms\Helpers\UserModelHelper;
use Illuminate\Support\Facades\Auth;

/**
 * Authorization Helper Functions
 *
 * This class provides convenient helper functions for permission and role checking
 */
class AuthorizationHelper
{
    protected static ?AuthServiceInterface $authService = null;

    /**
     * Get the auth service instance
     */
    protected static function getAuthService(): AuthServiceInterface
    {
        if (self::$authService === null) {
            self::$authService = app(AuthServiceInterface::class);
        }
        return self::$authService;
    }
    /**
     * Check if the current user (or guest) has a specific permission
     * DISABLED: Authorization logic commented out
     */
    public static function hasPermission(string $permission): bool
    {
        return true; // DISABLED: Always allow for now
        // return true; // DISABLED
    }

    /**
     * Check if the current user (or guest) has any of the given permissions
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        $authService = self::getAuthService();
        foreach ($permissions as $permission) {
            if ($authService->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the current user (or guest) has all of the given permissions
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        $authService = self::getAuthService();
        foreach ($permissions as $permission) {
            if (!$authService->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the current user (or guest) has a specific role
     */
    public static function hasRole(string $role): bool
    {
        return true; // DISABLED
    }

    /**
     * Check if the current user (or guest) has any of the given roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        $authService = self::getAuthService();
        foreach ($roles as $role) {
            if ($authService->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the current user (or guest) has all of the given roles
     */
    public static function hasAllRoles(array $roles): bool
    {
        $authService = self::getAuthService();
        foreach ($roles as $role) {
            if (!$authService->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the current user (or guest) is an admin
     */
    public static function isAdmin(): bool
    {
        return self::getAuthService()->hasRole('admin') || self::getAuthService()->hasRole('super-admin');
    }

    /**
     * Check if the current user (or guest) is a super admin
     */
    public static function isSuperAdmin(): bool
    {
        return true; // DISABLED
    }

    /**
     * Get the current user (or guest) permissions as an array
     */
    public static function getUserPermissions(): array
    {
        return self::getAuthService()->getUserPermissions();
    }

    /**
     * Get the current user (or guest) roles as an array
     */
    public static function getUserRoles(): array
    {
        return self::getAuthService()->getUserRoles();
    }

    /**
     * Check if current user (or guest) can perform an action on a model
     */
    public static function can(string $ability, $model = null): bool
    {
        $user = self::getAuthService()->getAuthenticatedUser();
        if (!$user) {
            return false;
        }

        // Use Laravel's built-in authorization if available
        if (method_exists($user, 'can')) {
            return $user->can($ability, $model);
        }

        return false;
    }

    /**
     * Check if current user (or guest) owns a resource (for ownership-based permissions)
     */
    public static function isOwner($resource, string $ownerField = 'user_id'): bool
    {
        $userId = self::getAuthService()->getUserId();
        if (!$userId || !$resource) {
            return false;
        }

        return $resource->{$ownerField} === $userId;
    }

    /**
     * Get permission-based menu items for the current user (or guest)
     */
    public static function getAllowedMenuItems(array $menuItems): array
    {
        return array_filter($menuItems, function ($item) {
            // If no permission required, show the item
            if (!isset($item['permission'])) {
                return true;
            }

            // Check if user has the required permission
            if (is_array($item['permission'])) {
                return self::hasAnyPermission($item['permission']);
            }

            return self::hasPermission($item['permission']);
        });
    }

    /**
     * Check if current user is guest
     */
    public static function isGuest(): bool
    {
        return !self::getAuthService()->isAuthenticated();
    }

    /**
     * Get guest permissions
     */
    public static function getGuestPermissions(): array
    {
        return UserModelHelper::getGuestPermissions();
    }
}
