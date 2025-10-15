<?php

namespace HMsoft\Cms\Models;

use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Traits\General\IsAutoFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permission Model
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $module
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Permission extends Model implements AutoFilterable
{
    use HasFactory, IsAutoFilterable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
    ];

    protected $hidden = [
        'pivot',
    ];

    /**
     * Get all roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get all users that have this permission through roles
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\HMsoft\Cms\Helpers\UserModelHelper::getUserModelClass(), 'user_permissions');
    }


    /**
     * Scope for filtering by module
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for filtering by slug
     */
    public function scopeSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
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
            'roles' => 'roles',
            'users' => 'users',
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
            'module' => 'module',
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
            'module',
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
            'module',
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
            'module',
        ];
    }
}
