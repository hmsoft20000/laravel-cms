<?php

/**
 * =================================================================
 * CMS Configuration Example for End-Developers
 * =================================================================
 * 
 * This file shows how to configure the CMS for your custom models.
 * Copy the relevant sections to your config/cms.php file.
 */

return [
    /**
     * =================================================================
     * Adding Custom Models to Morph Map
     * =================================================================
     * 
     * To make your custom models work with morph relations (features, downloads, plans, faqs),
     * you need to add them to the morph_map. This tells Laravel which model class to use
     * when it encounters a specific owner_type.
     */
    'morph_map' => [
        // Built-in CMS models (already configured)
        'post' => \HMsoft\Cms\Models\Content\Post::class,
        'legal' => \HMsoft\Cms\Models\Legal\Legal::class,
        'portfolio' => \HMsoft\Cms\Models\Content\Post::class,
        'blog' => \HMsoft\Cms\Models\Content\Post::class,
        'service' => \HMsoft\Cms\Models\Content\Post::class,
        
        // Add your custom models here
        'product' => \App\Models\Product::class,
        'course' => \App\Models\Course::class,
        'event' => \App\Models\Event::class,
        'article' => \App\Models\Article::class,
        'project' => \App\Models\Project::class,
    ],

    /**
     * =================================================================
     * Configuring Morph Relations for Custom Models
     * =================================================================
     * 
     * Once you've added your model to the morph_map, you can configure
     * which morph relations are available for it by adding route configurations.
     */
    'routes' => [
        // Example: Product model with all morph relations
        'product' => [
            'enabled' => true,
            'file' => 'content.php', // or your custom route file
            'prefix' => 'products',
            'middleware' => ['api'],
            'as' => 'api.products.',
            'options' => ['type' => 'product'],
        ],

        // Product morph relations
        'product-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.features-products.',
            'options' => [
                'owner_type' => 'product',
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],
        'product-downloads' => [
            'enabled' => true,
            'file' => 'downloads.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.downloads-products.',
            'options' => [
                'owner_type' => 'product',
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],
        'product-plans' => [
            'enabled' => true,
            'file' => 'plans.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.plans-products.',
            'options' => [
                'owner_type' => 'product',
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],
        'product-faqs' => [
            'enabled' => true,
            'file' => 'faqs.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.faqs-products.',
            'options' => [
                'owner_type' => 'product',
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],

        // Example: Course model with selected morph relations
        'course' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'courses',
            'middleware' => ['api'],
            'as' => 'api.courses.',
            'options' => ['type' => 'course'],
        ],

        // Course morph relations (only features and downloads)
        'course-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.features-courses.',
            'options' => [
                'owner_type' => 'course',
                'owner_url_prefix' => 'course',
                'owner_url_name' => 'course',
            ],
        ],
        'course-downloads' => [
            'enabled' => true,
            'file' => 'downloads.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.downloads-courses.',
            'options' => [
                'owner_type' => 'course',
                'owner_url_prefix' => 'course',
                'owner_url_name' => 'course',
            ],
        ],
        // Note: No course-plans or course-faqs - these relations are disabled for courses
    ],
];

/**
 * =================================================================
 * Required Model Setup
 * =================================================================
 * 
 * For your custom models to work with morph relations, they need:
 * 
 * 1. Morph relations defined in the model:
 * 
 *    class Product extends Model
 *    {
 *        public function features(): MorphMany
 *        {
 *            return $this->morphMany(Feature::class, 'owner')->orderBy('sort_number');
 *        }
 *        
 *        public function downloads(): MorphMany
 *        {
 *            return $this->morphMany(Download::class, 'owner')->orderBy('sort_number');
 *        }
 *        
 *        public function plans(): MorphMany
 *        {
 *            return $this->morphMany(Plan::class, 'owner')->orderBy('sort_number');
 *        }
 *        
 *        public function faqs(): MorphMany
 *        {
 *            return $this->morphMany(Faq::class, 'owner')->orderBy('sort_number');
 *        }
 *    }
 * 
 * 2. Migration for the morph tables (if not already exists):
 * 
 *    Schema::create('features', function (Blueprint $table) {
 *        $table->id();
 *        $table->morphs('owner'); // Creates owner_id and owner_type columns
 *        $table->integer('sort_number')->default(0);
 *        $table->timestamps();
 *    });
 * 
 * 3. The morph tables should have owner_id and owner_type columns
 *    to store the polymorphic relationship.
 * 
 * =================================================================
 * Available Route Patterns
 * =================================================================
 * 
 * Once configured, your routes will be available as:
 * 
 * GET    /api/product-features          - List all product features
 * POST   /api/product-features          - Create new product feature
 * GET    /api/product-features/{id}     - Show specific product feature
 * PUT    /api/product-features/{id}     - Update product feature
 * DELETE /api/product-features/{id}     - Delete product feature
 * POST   /api/product-features/updateAll - Bulk update product features
 * POST   /api/product-features/{id}/image - Update feature image
 * 
 * Same pattern applies to downloads, plans, and faqs.
 * 
 * =================================================================
 * Filtering and Scoping
 * =================================================================
 * 
 * The controllers automatically scope results by owner_type, so:
 * - /api/product-features will only return features where owner_type = 'product'
 * - /api/course-features will only return features where owner_type = 'course'
 * 
 * You can also filter by owner_id:
 * - /api/product-features?product_id=123 will return features for product with ID 123
 * - /api/course-features?course_id=456 will return features for course with ID 456
 */

