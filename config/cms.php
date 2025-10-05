<?php

return [
    /**
     * =================================================================
     * الإعدادات العامة
     * =================================================================
     */

    // البادئة العامة لكل مسارات الـ API الخاصة بالحزمة.
    // يمكن للمطور تغييرها لتجنب أي تضارب.
    'api_prefix' => 'cms-api',

    'locales' => [
        'en' => 'English',
        'ar' => 'العربية',
        // Add other supported locales here
    ],


    /**
     * =================================================================
     * The Polymorphic Map
     * =================================================================
     * This maps a URL-friendly string to a fully qualified model class.
     * This allows the media routes to work for any model.
     * The end-developer can add their own models here.
     */
    'morph_map' => [
        'posts'   => \HMsoft\Cms\Models\Content\Post::class,
        'post'    => \HMsoft\Cms\Models\Content\Post::class, // Alias for 'posts'
        'legals'  => \HMsoft\Cms\Models\Legal\Legal::class,
        'portfolios' => \HMsoft\Cms\Models\Content\Portfolio::class,
        'blogs' => \HMsoft\Cms\Models\Content\Blog::class,
        'services' => \HMsoft\Cms\Models\Content\Service::class,
        'sponsors' => \HMsoft\Cms\Models\Organizations\Organization::class,
        'partners' => \HMsoft\Cms\Models\Organizations\Organization::class,
        'legals' => \HMsoft\Cms\Models\Legal\Legal::class,
        'aboutUs' => \HMsoft\Cms\Models\Legal\Legal::class,
        'privacyPolicy' => \HMsoft\Cms\Models\Legal\Legal::class,
        'termsOfService' => \HMsoft\Cms\Models\Legal\Legal::class,
        'termOfUse' => \HMsoft\Cms\Models\Legal\Legal::class,
        'ourValues' => \HMsoft\Cms\Models\Legal\Legal::class,
        'ourHistory' => \HMsoft\Cms\Models\Legal\Legal::class,
        'ourMission' => \HMsoft\Cms\Models\Legal\Legal::class,
        'ourVision' => \HMsoft\Cms\Models\Legal\Legal::class,
        'ourStory' => \HMsoft\Cms\Models\Legal\Legal::class,

        // The developer can add their own models like this:
        'products' => \App\Models\Product::class, // Example for Product model
        'product' => \App\Models\Product::class,   // Alias for products
    ],

    /**
     * =================================================================
     * Owner Field Mapping
     * =================================================================
     * Maps content types to their owner field names.
     * This allows the frontend to send different field names (like portfolio_id, blog_id)
     * while the backend expects owner_id for polymorphic relationships.
     */
    'owner_field_mapping' => [
        'portfolios' => 'portfolio_id',
        'blogs' => 'blog_id',
        'posts' => 'post_id',
        'services' => 'service_id',
        'legals' => 'legal_id',
        'sponsors' => 'sponsor_id',
        'partners' => 'partner_id',
        'aboutUs' => 'legal_id',
        'privacyPolicy' => 'legal_id',
        'termsOfService' => 'legal_id',
        'termOfUse' => 'legal_id',
        'ourValues' => 'legal_id',
        'ourHistory' => 'legal_id',
        'ourMission' => 'legal_id',
        'ourVision' => 'legal_id',
        'ourStory' => 'legal_id',
        'products' => 'product_id', // For Product model
    ],

    /**
     * =================================================================
     * The Customizable Controllers
     * =================================================================
     * The developer can change any class here to use their own Controller from their project.
     */
    'controllers' => [
        'AuthController'             => \HMsoft\Cms\Http\Controllers\Api\AuthController::class,
        'PostController'             => \HMsoft\Cms\Http\Controllers\Api\PostController::class,
        'MediaController'            => \HMsoft\Cms\Http\Controllers\Api\MediaController::class,
        'CategoryController'         => \HMsoft\Cms\Http\Controllers\Api\CategoryController::class,
        'AttributeController'        => \HMsoft\Cms\Http\Controllers\Api\AttributeController::class,
        'FeatureController'          => \HMsoft\Cms\Http\Controllers\Api\FeatureController::class,
        'DownloadController'         => \HMsoft\Cms\Http\Controllers\Api\DownloadController::class,
        'FaqController'              => \HMsoft\Cms\Http\Controllers\Api\FaqController::class,
        'PlanController'             => \HMsoft\Cms\Http\Controllers\Api\PlanController::class,
        'ContactUsController'        => \HMsoft\Cms\Http\Controllers\Api\ContactUsController::class,
        'SectorController'           => \HMsoft\Cms\Http\Controllers\Api\SectorController::class,
        'OrganizationController'     => \HMsoft\Cms\Http\Controllers\Api\OrganizationController::class,
        'BusinessSettingController'  => \HMsoft\Cms\Http\Controllers\Api\BusinessSettingController::class,
        'LegalsController'           => \HMsoft\Cms\Http\Controllers\Api\LegalsController::class,
        'PagesMetaController'        => \HMsoft\Cms\Http\Controllers\Api\PagesMetaController::class,
        'PermissionController'       => \HMsoft\Cms\Http\Controllers\Api\PermissionController::class,
        'RoleController'             => \HMsoft\Cms\Http\Controllers\Api\RoleController::class,
        'UserPermissionController'   => \HMsoft\Cms\Http\Controllers\Api\UserPermissionController::class,
        'TestimonialController'      => \HMsoft\Cms\Http\Controllers\Api\TestimonialController::class,
        'TeamController'             => \HMsoft\Cms\Http\Controllers\Api\TeamController::class,
        'StatisticsController'       => \HMsoft\Cms\Http\Controllers\Api\StatisticsController::class,
        'LanguageController'         => \HMsoft\Cms\Http\Controllers\Api\LanguageController::class,
    ],

    /**
     * =================================================================
     * Define Route Modules
     * =================================================================
     * all routes are separated into different files and can be controlled.
     */
    'routes' => [
        // auth module
        'auth' => [
            'enabled' => true,
            'file' => 'auth.php',
            'prefix' => 'auth',
            'middleware' => ['api'],
            'as' => 'api.auth.',
        ],

        // reusable content modules
        'portfolio' => [
            'enabled' => true,
            'file' => 'portfolio.php',
            'prefix' => 'portfolios',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.portfolios.',
            'options' => ['type' => 'portfolio'],
        ],
        'portfolio-category' => [
            'enabled' => true,
            'file' => 'category.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.category-portfolios.',
            'options' => [
                'type' => 'portfolio',
            ],
        ],
        'portfolio-attributes' => [
            'enabled' => true,
            'file' => 'attribute.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.attributes-portfolios.',
            'options' => [
                'type' => 'portfolio',
            ],
        ],

        //start  polymorphic
        'portfolio-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.features-portfolios.',
            'options' => [
                'type' => 'portfolios',
                'owner_type' => 'post',
                'owner_url_prefix' => 'portfolio',
                'owner_url_name' => 'portfolio',
            ],
        ],
        'blog-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.features-blogs.',
            'options' => [
                'type' => 'blogs',
                'owner_type' => 'post',
                'owner_url_prefix' => 'blog',
                'owner_url_name' => 'blog',
            ],
        ],
        'service-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.features-services.',
            'options' => [
                'type' => 'services',
                'owner_type' => 'post',
                'owner_url_prefix' => 'service',
                'owner_url_name' => 'service',
            ],
        ],
        'product-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.features-products.',
            'options' => [
                'type' => 'products',
                'owner_type' => 'product', // Different from 'post'
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],
        'portfolio-download' => [
            'enabled' => true,
            'file' => 'download.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.download-portfolios.',
            'options' => [
                'owner_type' => 'portfolio',
                'owner_url_prefix' => 'portfolio',
                'owner_url_name' => 'portfolio',
            ],
        ],
        'portfolio-faq' => [
            'enabled' => true,
            'file' => 'faq.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.faq-portfolios.',
            'options' => [
                'owner_type' => 'portfolio',
                'owner_url_prefix' => 'portfolio',
                'owner_url_name' => 'portfolio',
            ],
        ],
        'portfolio-plan' => [
            'enabled' => true,
            'file' => 'plan.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.plan-portfolios.',
            'options' => [
                'owner_type' => 'portfolio',
                'owner_url_prefix' => 'portfolio',
                'owner_url_name' => 'portfolio',
            ],
        ],
        'portfolio-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.media-portfolios.',
            'options' => [
                'owner_type' => 'portfolio',
                'owner_url_prefix' => 'portfolio',
                'owner_url_name' => 'portfolio',
            ],
        ],

        'media' => [
            'enabled' => true,
            'file' => 'media.php',
            // The route prefix is polymorphic
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.media.',
        ],
        // end polymorphic

        'blog' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'blogs',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.blogs.',
            'options' => ['type' => 'blog'],
        ],
        'blog-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.media-blogs.',
            'options' => [
                'owner_type' => 'blogs',
                'owner_url_prefix' => 'blog',
                'owner_url_name' => 'blog',
            ],
        ],
        'service' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'services',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.services.',
            'options' => ['type' => 'service'],
        ],
        'service-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.media-services.',
            'options' => [
                'owner_type' => 'services',
                'owner_url_prefix' => 'service',
                'owner_url_name' => 'service',
            ],
        ],

        // organizations modules
        'sponsor' => [
            'enabled' => true,
            'file' => 'organizations.php',
            'prefix' => 'sponsors',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.sponsors.',
            'options' => ['type' => 'sponsor'],
        ],
        'partner' => [
            'enabled' => true,
            'file' => 'organizations.php',
            'prefix' => 'partners',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.partners.',
            'options' => ['type' => 'partner'],
        ],

        // legal pages modules
        'legal' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.legals.',
            'options' => ['type' => 'legal'],
        ],
        'aboutUs' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'about-us',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.about-us.',
            'options' => ['type' => 'aboutUs'],
        ],
        'privacyPolicy' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'privacy-policy',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.privacy-policy.',
            'options' => ['type' => 'privacyPolicy'],
        ],
        // ... (you can add other legal pages here)

        // authorization and roles module
        'authorization' => [
            'enabled' => true,
            'file' => 'authorization.php',
            'middleware' => ['api', 'auth:sanctum'],
        ],

        // miscellaneous routes module
        'others' => [
            'enabled' => true,
            'file' => 'others.php',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.',
            'prefix' => ''
        ],
    ],

    /**
     * =================================================================
     * Overrides Section
     * =================================================================
     * This is the place where the developer can customize the routes precisely.
     * Leave it empty by default.
     */
    'overrides' => [
        // example for explanation only:
        // 'api.auth.login' => [
        //     'uri' => 'signin', // change URI
        //     'middleware' => ['throttle:5,1'], // add middleware
        //     'action' => [\App\Http\Controllers\MyCustomAuthController::class, 'login'], // change Controller
        // ],
        // 'api.auth.register' => [
        //     'enabled' => false, // disable login route
        // ],
    ],
];
