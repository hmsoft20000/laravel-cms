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

    /*
    |--------------------------------------------------------------------------
    | AutoFilterable Interface Implementation (The New Advanced Way)
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     * This is the most important new method. It tells the JoinManager which
     * relationships are available for joining. The key is the API-friendly name,
     * and the value is the actual Eloquent method name on this model.
     */
    public function defineRelationships(): array
    {
        return [
            // 'Public API Name' => 'eloquentMethodName'
            'roles' => 'roles',
            'users' => 'users',
        ];
    }

    /**
     * {@inheritdoc}
     * The field selection map is now much simpler.
     * It just maps an API field name to either a base table column or a
     * 'relationship.column' string. The service handles the rest.
     */
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

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be specifically filtered.
     */
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

    /**
     * {@inheritdoc}
     * Defines the whitelist of attributes that can be sorted.
     */
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

    /**
     * {@inheritdoc}
     * Defines columns from the main table for the global search.
     */
    public function defineGlobalSearchBaseAttributes(): array
    {
        return [
            'name',
            'slug',
            'description',
            'module',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines columns from the translation table for the global search.
     */
    public function defineGlobalSearchTranslationAttributes(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * Specifies the name of the translation table.
     */
    public function defineTranslationTableName(): ?string
    {
        return null; // Permission doesn't have translations
    }

    /**
     * {@inheritdoc}
     * Specifies the foreign key in the translation table.
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return null; // Permission doesn't have translations
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
}
