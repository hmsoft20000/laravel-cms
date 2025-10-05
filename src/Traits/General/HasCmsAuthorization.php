<?php

namespace HMsoft\Cms\Traits\General;

use HMsoft\Cms\Models\Permission;
use HMsoft\Cms\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;

trait HasCmsAuthorization
{
    // استخدم الـ Trait الخاص بـ Spatie كأساس إذا كنت تريد التوافق مستقبلًا
    // حاليًا، سنستخدم الدوال المخصصة التي كتبتها
    // use SpatieHasRoles;s

    // =================================================================
    // AUTHORIZATION RELATIONSHIPS & METHODS
    // =================================================================

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->count() === count($roles);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->permissions()->where('slug', $permission)->exists()) {
            return true;
        }
        if ($this->roles()->whereHas('permissions', fn($q) => $q->where('slug', $permission))->exists()) {
            return true;
        }
        if ($this->isGuest()) {
            return $this->hasGuestPermission($permission);
        }
        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->permissions()->whereIn('slug', $permissions)->exists()) {
            return true;
        }
        return $this->roles()->whereHas('permissions', fn($q) => $q->whereIn('slug', $permissions))->exists();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function assignRole(string|Role $role): void
    {
        $roleId = $role instanceof Role ? $role->id : Role::where('slug', $role)->first()->id;
        $this->roles()->attach($roleId);
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            return $role instanceof Role ? $role->id : Role::where('slug', $role)->first()->id;
        })->toArray();
        $this->roles()->sync($roleIds);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('super-admin');
    }

    public function getAllPermissions()
    {
        $directPermissions = $this->permissions->pluck('slug')->toArray();
        $rolePermissions = $this->roles->load('permissions')->pluck('permissions')->flatten()->pluck('slug')->unique()->toArray();
        return array_unique(array_merge($directPermissions, $rolePermissions));
    }

    // =================================================================
    // GUEST USER HANDLING
    // =================================================================

    public function isGuest(): bool
    {
        return !$this->exists || is_null($this->id);
    }

    public function hasGuestPermission(string $permission): bool
    {
        $guestRole = Role::where('slug', 'guest')->first();
        return $guestRole ? $guestRole->permissions()->where('slug', $permission)->exists() : false;
    }

    public static function getGuestPermissions(): array
    {
        $guestRole = Role::where('slug', 'guest')->first();
        return $guestRole ? $guestRole->permissions->pluck('slug')->toArray() : [];
    }
}
