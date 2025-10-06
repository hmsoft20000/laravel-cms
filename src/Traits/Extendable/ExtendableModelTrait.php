<?php

namespace HMsoft\Cms\Traits\Extendable;

use Illuminate\Database\Eloquent\Model;

/**
 * Extendable Model Trait
 * 
 * This trait allows developers to extend CMS models by creating
 * their own model classes that extend the original CMS models.
 * Similar to how Laravel allows extending models.
 */
trait ExtendableModelTrait
{
    /**
     * Boot the trait and register extended models
     */
    protected static function bootExtendableModelTrait()
    {
        // Register extended models when the model is booted
        static::registerExtendedModels();
    }

    /**
     * Register extended models defined in config
     */
    protected static function registerExtendedModels()
    {
        $extendedModels = config('cms.extended_models', []);
        $originalClass = static::class;
        
        if (isset($extendedModels[$originalClass])) {
            $extendedClass = $extendedModels[$originalClass];
            
            // Replace the original model with the extended one
            app()->bind($originalClass, $extendedClass);
            
            // Also bind the extended class to itself
            app()->bind($extendedClass, $extendedClass);
        }
    }

    /**
     * Get the extended model class for this model
     */
    public static function getExtendedModelClass(): ?string
    {
        $extendedModels = config('cms.extended_models', []);
        return $extendedModels[static::class] ?? null;
    }

    /**
     * Check if this model has an extended version
     */
    public static function hasExtendedModel(): bool
    {
        return !is_null(static::getExtendedModelClass());
    }

    /**
     * Get the original model class (before extension)
     */
    public static function getOriginalModelClass(): string
    {
        return static::class;
    }

    /**
     * Create a new instance using the extended model if available
     */
    public static function newInstance($attributes = [], $exists = false)
    {
        $extendedClass = static::getExtendedModelClass();
        
        if ($extendedClass && class_exists($extendedClass)) {
            return new $extendedClass($attributes, $exists);
        }
        
        return parent::newInstance($attributes, $exists);
    }

    /**
     * Create a new query builder for the model
     */
    public function newQuery()
    {
        $extendedClass = static::getExtendedModelClass();
        
        if ($extendedClass && class_exists($extendedClass)) {
            $model = new $extendedClass();
            $model->setConnection($this->getConnectionName());
            return $model->newQuery();
        }
        
        return parent::newQuery();
    }

    /**
     * Get a new query builder for the model's table
     */
    public function newModelQuery()
    {
        $extendedClass = static::getExtendedModelClass();
        
        if ($extendedClass && class_exists($extendedClass)) {
            $model = new $extendedClass();
            $model->setConnection($this->getConnectionName());
            return $model->newModelQuery();
        }
        
        return parent::newModelQuery();
    }
}
