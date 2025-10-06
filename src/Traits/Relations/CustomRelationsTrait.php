<?php

namespace HMsoft\Cms\Traits\Relations;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Custom Relations Trait
 * 
 * This trait allows developers to add custom relationships to CMS models
 * without modifying the original model files.
 */
trait CustomRelationsTrait
{
    /**
     * Store custom relationships for each model
     */
    protected static $customRelations = [];

    /**
     * Boot the trait and register custom relationships
     */
    protected static function bootCustomRelationsTrait()
    {
        // Register custom relationships when the model is booted
        static::registerCustomRelations();
    }

    /**
     * Register custom relationships defined in config
     */
    protected static function registerCustomRelations()
    {
        $customRelations = config('cms.custom_relations', []);
        $modelClass = static::class;
        
        if (isset($customRelations[$modelClass])) {
            foreach ($customRelations[$modelClass] as $relationName => $relationConfig) {
                static::addCustomRelation($relationName, $relationConfig);
            }
        }
    }

    /**
     * Add a custom relationship to the model
     */
    public static function addCustomRelation(string $relationName, array $config)
    {
        $modelClass = static::class;
        
        // Store the relation config for later use
        if (!isset(static::$customRelations)) {
            static::$customRelations = [];
        }
        
        static::$customRelations[$relationName] = $config;
        
        // Add the relation method dynamically
        static::macro($relationName, function () use ($config) {
            return $this->buildCustomRelation($config);
        });
    }

    /**
     * Build a custom relationship based on configuration
     */
    protected function buildCustomRelation(array $config)
    {
        $relationType = $config['type'];
        $relatedModel = $config['related'];
        $foreignKey = $config['foreign_key'] ?? null;
        $localKey = $config['local_key'] ?? 'id';
        $ownerKey = $config['owner_key'] ?? 'id';
        $table = $config['table'] ?? null;
        $pivotColumns = $config['pivot_columns'] ?? [];

        switch ($relationType) {
            case 'hasMany':
                return $this->hasMany($relatedModel, $foreignKey, $localKey);
                
            case 'belongsTo':
                return $this->belongsTo($relatedModel, $foreignKey, $ownerKey);
                
            case 'hasOne':
                return $this->hasOne($relatedModel, $foreignKey, $localKey);
                
            case 'belongsToMany':
                return $this->belongsToMany($relatedModel, $table, $foreignKey, $config['related_foreign_key'] ?? null, $localKey, $ownerKey)
                    ->withPivot($pivotColumns);
                    
            case 'morphMany':
                return $this->morphMany($relatedModel, $config['morph_name'] ?? 'morphable', $config['morph_type'] ?? 'morphable_type', $config['morph_id'] ?? 'morphable_id');
                
            case 'morphTo':
                return $this->morphTo($config['morph_name'] ?? 'morphable', $config['morph_type'] ?? 'morphable_type', $config['morph_id'] ?? 'morphable_id');
                
            case 'morphOne':
                return $this->morphOne($relatedModel, $config['morph_name'] ?? 'morphable', $config['morph_type'] ?? 'morphable_type', $config['morph_id'] ?? 'morphable_id');
                
            default:
                throw new \InvalidArgumentException("Unsupported relation type: {$relationType}");
        }
    }

    /**
     * Get all custom relationships for this model
     */
    public static function getCustomRelations(): array
    {
        return static::$customRelations ?? [];
    }

    /**
     * Check if a custom relationship exists
     */
    public static function hasCustomRelation(string $relationName): bool
    {
        return isset(static::$customRelations[$relationName]);
    }

    /**
     * Get custom relation configuration
     */
    public static function getCustomRelationConfig(string $relationName): ?array
    {
        return static::$customRelations[$relationName] ?? null;
    }
}
