<?php

/**
 * =================================================================
 * Extended Models Configuration
 * =================================================================
 * 
 * This file allows developers to extend CMS models by creating
 * their own model classes that extend the original CMS models.
 * Similar to how Laravel allows extending models.
 * 
 * Example:
 * - Create App\Models\Category that extends HMsoft\Cms\Models\Shared\Category
 * - Add custom relationships, methods, and properties
 * - Register the extended model here
 */

return [
    /**
     * =================================================================
     * Extended Models Mapping
     * =================================================================
     * 
     * 'OriginalModelClass' => 'ExtendedModelClass'
     * 
     * The extended model should extend the original model and can
     * add custom relationships, methods, scopes, etc.
     */
    
    // Example: Extend Category model
    // \HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
    
    // Example: Extend Post model
    // \HMsoft\Cms\Models\Content\Post::class => \App\Models\Post::class,
    
    // Example: Extend Sector model
    // \HMsoft\Cms\Models\Sector\Sector::class => \App\Models\Sector::class,
    
    // Example: Extend Organization model
    // \HMsoft\Cms\Models\Organizations\Organization::class => \App\Models\Organization::class,
    
    // Example: Extend Team model
    // \HMsoft\Cms\Models\Team\Team::class => \App\Models\Team::class,
    
    // Example: Extend Statistics model
    // \HMsoft\Cms\Models\Statistics\Statistics::class => \App\Models\Statistics::class,
];
