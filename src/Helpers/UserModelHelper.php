<?php

namespace HMsoft\Cms\Helpers;

/**
 * User Model Helper - Static Class
 * 
 * This class provides static methods to work with user models
 * without breaking the isolation of the CMS library
 */
class UserModelHelper
{
    /**
     * Get the user model class from Laravel's auth config
     */
    public static function getUserModelClass(): string
    {
        return config('auth.providers.users.model', \App\Models\User::class);
    }

    /**
     * Get user model instance by ID
     */
    public static function findUser(int $userId): ?object
    {
        $userModelClass = self::getUserModelClass();
        return $userModelClass::find($userId);
    }

    /**
     * Check if user has permission
     */
    public static function userHasPermission(int $userId, string $permission): bool
    {
        $user = self::findUser($userId);
        if (!$user || !method_exists($user, 'hasPermission')) {
            return false;
        }
        
        return $user->hasPermission($permission);
    }

    /**
     * Check if user has role
     */
    public static function userHasRole(int $userId, string $role): bool
    {
        $user = self::findUser($userId);
        if (!$user || !method_exists($user, 'hasRole')) {
            return false;
        }
        
        return $user->hasRole($role);
    }

    /**
     * Get user roles
     */
    public static function getUserRoles(int $userId): array
    {
        $user = self::findUser($userId);
        if (!$user || !method_exists($user, 'getRoles')) {
            return [];
        }
        
        return $user->getRoles()->pluck('name')->toArray();
    }

    /**
     * Get user permissions
     */
    public static function getUserPermissions(int $userId): array
    {
        $user = self::findUser($userId);
        if (!$user || !method_exists($user, 'getAllPermissions')) {
            return [];
        }
        
        return $user->getAllPermissions();
    }

    /**
     * Check if user is guest
     */
    public static function userIsGuest(int $userId): bool
    {
        $user = self::findUser($userId);
        if (!$user || !method_exists($user, 'isGuest')) {
            return true; // If user not found, consider as guest
        }
        
        return $user->isGuest();
    }

    /**
     * Get current user or guest
     */
    public static function currentOrGuest(): object
    {
        $userModelClass = self::getUserModelClass();
        
        // Try to get current authenticated user
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            return $user;
        }
        
        // Return guest user if no authenticated user
        return new class {
            public function isGuest(): bool { return true; }
            public function hasPermission(string $permission): bool { return false; }
            public function hasRole(string $role): bool { return false; }
            public function getAllPermissions(): array { return []; }
            public function getRoles(): array { return []; }
        };
    }

    /**
     * Get guest permissions
     */
    public static function getGuestPermissions(): array
    {
        return [
            'posts.view',
            'categories.view',
            'sectors.view',
            'legal.view',
            'pages.view',
            'faqs.view',
            'features.view',
            'plans.view'
        ];
    }
}
