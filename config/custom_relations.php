<?php

/**
 * =================================================================
 * Custom Relations Configuration
 * =================================================================
 * 
 * This file allows developers to add custom relationships to CMS models
 * without modifying the original model files.
 * 
 * Example usage:
 * - Add properties relationship to Category model
 * - Add custom relationships to any CMS model
 * - Define polymorphic relationships
 */

return [
    /**
     * =================================================================
     * Category Model Custom Relations
     * =================================================================
     */
    \HMsoft\Cms\Models\Shared\Category::class => [
        // Example: Add properties relationship
        'properties' => [
            'type' => 'hasMany',
            'related' => \App\Models\Property::class, // Developer's custom model
            'foreign_key' => 'category_id',
            'local_key' => 'id',
        ],
        
        // Example: Add products relationship
        'products' => [
            'type' => 'hasMany',
            'related' => \App\Models\Product::class,
            'foreign_key' => 'category_id',
            'local_key' => 'id',
        ],
        
        // Example: Add many-to-many relationship
        'tags' => [
            'type' => 'belongsToMany',
            'related' => \App\Models\Tag::class,
            'table' => 'category_tag',
            'foreign_key' => 'category_id',
            'related_foreign_key' => 'tag_id',
            'local_key' => 'id',
            'owner_key' => 'id',
            'pivot_columns' => ['created_at', 'updated_at'],
        ],
    ],

    /**
     * =================================================================
     * Post Model Custom Relations
     * =================================================================
     */
    \HMsoft\Cms\Models\Content\Post::class => [
        // Example: Add comments relationship
        'comments' => [
            'type' => 'hasMany',
            'related' => \App\Models\Comment::class,
            'foreign_key' => 'post_id',
            'local_key' => 'id',
        ],
        
        // Example: Add polymorphic relationship
        'reviews' => [
            'type' => 'morphMany',
            'related' => \App\Models\Review::class,
            'morph_name' => 'reviewable',
            'morph_type' => 'reviewable_type',
            'morph_id' => 'reviewable_id',
        ],
    ],

    /**
     * =================================================================
     * Sector Model Custom Relations
     * =================================================================
     */
    \HMsoft\Cms\Models\Sector\Sector::class => [
        // Example: Add custom relationship
        'custom_data' => [
            'type' => 'hasOne',
            'related' => \App\Models\SectorData::class,
            'foreign_key' => 'sector_id',
            'local_key' => 'id',
        ],
    ],

    /**
     * =================================================================
     * Organization Model Custom Relations
     * =================================================================
     */
    \HMsoft\Cms\Models\Organizations\Organization::class => [
        // Example: Add contacts relationship
        'contacts' => [
            'type' => 'hasMany',
            'related' => \App\Models\Contact::class,
            'foreign_key' => 'organization_id',
            'local_key' => 'id',
        ],
        
        // Example: Add many-to-many relationship
        'services' => [
            'type' => 'belongsToMany',
            'related' => \App\Models\Service::class,
            'table' => 'organization_services',
            'foreign_key' => 'organization_id',
            'related_foreign_key' => 'service_id',
            'local_key' => 'id',
            'owner_key' => 'id',
        ],
    ],
];
