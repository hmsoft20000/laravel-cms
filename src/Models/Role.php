<?php

namespace HMsoft\Cms\Models;

use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Traits\General\IsAutoFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role Model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Role extends Model implements AutoFilterable
{
    use HasFactory, IsAutoFilterable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'parent_id',
    ];

    protected $hidden = [
        'pivot',
    ];

    /**
     * Get all permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get the parent role
     */
    public function parent()
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    /**
     * Get all child roles
     */
    public function children()
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();

        $parent = $this->parent;
        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all users with this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\HMsoft\Cms\Helpers\UserModelHelper::getUserModelClass(), 'user_roles');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }

    /**
     * Give permission to this role
     */
    public function givePermissionTo(string|Permission $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : Permission::where('slug', $permission)->first()->id;
        $this->permissions()->attach($permissionId);
    }

    /**
     * Revoke permission from this role
     */
    public function revokePermission(string|Permission $permission): void
    {
        $permissionId = $permission instanceof Permission ? $permission->id : Permission::where('slug', $permission)->first()->id;
        $this->permissions()->detach($permissionId);
    }

    /**
     * Sync permissions for this role
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            return $permission instanceof Permission ? $permission->id : Permission::where('slug', $permission)->first()->id;
        })->toArray();

        $this->permissions()->sync($permissionIds);
    }

    /**
     * Get role tree structure for frontend
     */
    public static function getTree()
    {
        return static::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with('children');
            }])
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                return $role->toTreeArray();
            });
    }

    /**
     * Convert role to tree array format
     */
    public function toTreeArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'parent_id' => $this->parent_id,
            'permissions_count' => $this->permissions()->count(),
            'users_count' => $this->users()->count(),
            'children' => $this->children->map(function ($child) {
                return $child->toTreeArray();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get all permissions including inherited from parent roles
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Add own permissions
        $permissions = $permissions->merge($this->permissions);

        // Add permissions from ancestors
        $ancestors = $this->ancestors();
        foreach ($ancestors as $ancestor) {
            $permissions = $permissions->merge($ancestor->permissions);
        }

        return $permissions->unique('id');
    }

    /**
     * Check if this role is a descendant of another role
     */
    public function isDescendantOf(Role $role): bool
    {
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id === $role->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }

    /**
     * Check if this role is an ancestor of another role
     */
    public function isAncestorOf(Role $role): bool
    {
        return $role->isDescendantOf($this);
    }

    /**
     * Get the depth level in the hierarchy
     */
    public function getDepth(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * Get all roles at the same level (siblings)
     */
    public function getSiblings()
    {
        return static::where('parent_id', $this->parent_id)
            ->where('id', '!=', $this->id)
            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
    */

    public function defineRelationships(): array
    {
        return [
            // 'Public API Name' => 'eloquentMethodName'
            'permissions' => 'permissions',
            'users' => 'users',
            'parent' => 'parent',
            'children' => 'children',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            // 'Public API Name' => 'relationship_name.column_name' OR 'base_column'
            'name' => 'name',
            'slug' => 'slug',
            'description' => 'description',
            'level' => 'level',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        return [
            'id',
            'name',
            'slug',
            'description',
            'level',
            'created_at',
            'updated_at',
        ];
    }

    public function defineSortableAttributes(): array
    {
        return [
            'id',
            'name',
            'slug',
            'description',
            'level',
            'created_at',
            'updated_at',
        ];
    }

    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'name',
            'slug',
            'description',
        ];
    }
}
