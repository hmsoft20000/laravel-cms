<?php

namespace HMsoft\Cms\Services;

use Illuminate\Support\Facades\App;

/**
 * Model Extension Service
 * 
 * This service handles model extension through Laravel's Service Container
 * without modifying the original model methods.
 */
class ModelExtensionService
{
    /**
     * Register extended models from config
     */
    public static function registerExtendedModels(): void
    {
        $extendedModels = config('cms.extended_models', []);
        
        foreach ($extendedModels as $originalClass => $extendedClass) {
            if (class_exists($extendedClass)) {
                // Bind the extended class to the original class
                App::bind($originalClass, $extendedClass);
                
                // Also bind the extended class to itself
                App::bind($extendedClass, $extendedClass);
            }
        }
    }

    /**
     * Get the extended model class for a given original class
     */
    public static function getExtendedModelClass(string $originalClass): ?string
    {
        $extendedModels = config('cms.extended_models', []);
        return $extendedModels[$originalClass] ?? null;
    }

    /**
     * Check if a model has an extended version
     */
    public static function hasExtendedModel(string $originalClass): bool
    {
        return !is_null(self::getExtendedModelClass($originalClass));
    }

    /**
     * Create an instance of the extended model if available
     */
    public static function createInstance(string $originalClass, array $attributes = [], bool $exists = false)
    {
        $extendedClass = self::getExtendedModelClass($originalClass);
        
        if ($extendedClass && class_exists($extendedClass)) {
            return new $extendedClass($attributes, $exists);
        }
        
        return new $originalClass($attributes, $exists);
    }

    /**
     * Resolve a model instance from the container
     */
    public static function resolve(string $originalClass)
    {
        return App::make($originalClass);
    }
}
