<?php

return [
    /**
     * =================================================================
     * الإعدادات العامة
     * =================================================================
     */

    // البادئة العامة لكل مسارات الـ API الخاصة بالحزمة.
    // يمكن للمطور تغييرها لتجنب أي تضارب.
    'api_prefix' => '',

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
        'statistics' => \HMsoft\Cms\Models\Statistics\Statistics::class,
        'sectors' => \HMsoft\Cms\Models\Sector\Sector::class,

        // The developer can add their own models like this:
        'products' => \App\Models\Product::class, // Example for Product model
        'product' => \App\Models\Product::class,   // Alias for products
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
        'LegalsMediaController'      => \HMsoft\Cms\Http\Controllers\Api\LegalsMediaController::class,
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
        'NestedPostController'       => \HMsoft\Cms\Http\Controllers\Api\NestedPostController::class,
    ],

    /**
     * =================================================================
     * Define Route Modules
     * =================================================================
     * all routes are separated into different files and can be controlled.
     */
    'routes' => [

        /*
        * =================================================================
         * Organizations Modules
         * =================================================================
         * please note that the prefix is the same as the type
         * the binding name is the same as the type
         * the media is the same as the type
         * the options are the same as the type
        */
        'sponsor' => [
            'enabled' => true,
            'file' => 'organizations.php',
            'prefix' => 'sponsors',
            'middleware' => ['api'],
            'as' => 'api.sponsors.',
            'options' => [
                'type' => 'sponsor',
                'binding_name' => 'sponsor'
            ],
        ],

        'partner' => [
            'enabled' => true,
            'file' => 'organizations.php',
            'prefix' => 'partners',
            'middleware' => ['api'],
            'as' => 'api.partners.',
            'options' => [
                'type' => 'partner',
                'binding_name' => 'partner'
            ],
        ],


        /*
        * =================================================================
         * Portfolio Modules
         * =================================================================
         * please note that the prefix is the same as the type
         * the binding name is the same as the type
         * the media is the same as the type
         * the options are the same as the type
        */
        'portfolio' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'portfolios',
            'middleware' => ['api'],
            'as' => 'api.portfolios.',
            'options' => [
                'type' => 'portfolio',
                'binding_name' => 'post'
            ],
        ],
        'portfolio-category' => [
            'enabled' => true,
            'file' => 'category.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.',
            'options' => [
                'type' => 'portfolio',
            ],
        ],
        'portfolio-attributes' => [
            'enabled' => true,
            'file' => 'attribute.php', // <-- use the general attribute template file
            'prefix' => '', // <-- empty because the prefix is built dynamically
            'middleware' => ['api'],
            'as' => 'api.', // it will be merged with the route name
            'options' => [
                // this is the most important part that defines the scope
                'type' => 'portfolio',
            ],
        ],
        'portfolio-plans' => [
            'enabled' => true,
            'file' => 'plans.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'portfolios' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'portfolios' في morph_map.
             */
            'as' => 'api.portfolios.plans.', // الـ binding سيقرأ 'portfolios' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف plans.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /portfolios/{owner}/plans
                 */
                'owner_url_name' => 'portfolios',
            ],
        ],
        'portfolio-features' => [
            'enabled' => true,
            'file' => 'features.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'portfolios' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'portfolios' في morph_map.
             */
            'as' => 'api.portfolios.features.', // الـ binding سيقرأ 'portfolios' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف features.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /portfolios/{owner}/features
                 */
                'owner_url_name' => 'portfolios',
            ],
        ],
        'portfolio-downloads' => [
            'enabled' => true,
            'file' => 'downloads.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'portfolios' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'portfolios' في morph_map.
             */
            'as' => 'api.portfolios.downloads.', // الـ binding سيقرأ 'portfolios' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف downloads.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /portfolios/{owner}/downloads
                 */
                'owner_url_name' => 'portfolios',
            ],
        ],
        'portfolio-faqs' => [
            'enabled' => true,
            'file' => 'faqs.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'portfolios' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'portfolios' في morph_map.
             */
            'as' => 'api.portfolios.faqs.', // الـ binding سيقرأ 'portfolios' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف faqs.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /portfolios/{owner}/faqs
                 */
                'owner_url_name' => 'portfolios',
            ],
        ],
        'portfolio-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.portfolios.media.',
            'options' => [
                'owner_url_name' => 'portfolios',
            ],
        ],
        'portfolio-blogs' => [
            'enabled' => true,
            'file' => 'nested_content.php',
            'prefix' => 'portfolios/{owner}/blogs',
            'middleware' => ['api'],
            'as' => 'api.portfolios.blogs.',
            'options' => [
                'type' => 'blog',
            ],
        ],
        'portfolio-services' => [
            'enabled' => true,
            'file' => 'nested_content.php',
            'prefix' => 'portfolios/{owner}/services',
            'middleware' => ['api'],
            'as' => 'api.portfolios.services.',
            'options' => [
                'type' => 'service',
            ],
        ],


        /*
        * =================================================================
         * Legal Pages Modules
         * =================================================================
         * please note that the prefix is the same as the type
         * the binding name is the same as the type
         * the media is the same as the type
         * the options are the same as the type
         * **important:** it must match 'aboutUs' with the key in morph_map, 'privacyPolicy' with the key in morph_map, 'termsOfService' with the key in morph_map, 'termOfUse' with the key in morph_map, 'ourMission' with the key in morph_map, 'ourVision' with the key in morph_map, 'ourStory' with the key in morph_map, 'ourHistory' with the key in morph_map, 'ourValues' with the key in morph_map
        */
        'legal' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals',
            'middleware' => ['api'],
            'as' => 'api.legals.',
            'options' => ['type' => 'legal'],
        ],
        // about us
        'aboutUs' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/aboutUs',
            'middleware' => ['api'],
            'as' => 'api.legals.aboutUs.',
            'options' => [
                'type' => 'aboutUs'
            ],
        ],
        'aboutUs-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            // 'prefix' => 'legals/aboutUs',
            'prefix' => 'legals/aboutUs',
            'middleware' => ['api'],
            'as' => 'api.legals.aboutUs.media.', // <-- **important:** it must match 'aboutUs' with the key in morph_map
            'options' => [
                'type' => 'aboutUs', // <-- to build the path: /api/legals/aboutUs/media
            ],
        ],
        'aboutUs-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.aboutUs.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'aboutUs', // <-- لبناء المسار: /api/about-us/{owner}/features
            ],
        ],
        // privacy policy
        'privacyPolicy' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/privacyPolicy',
            'middleware' => ['api'],
            'as' => 'api.legals.privacyPolicy.',
            'options' => [
                'type' => 'privacyPolicy'
            ],
        ],
        'privacyPolicy-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/privacyPolicy',
            'middleware' => ['api'],
            'as' => 'api.legals.privacyPolicy.media.', // <-- **important:** it must match 'privacyPolicy' with the key in morph_map
            'options' => [
                'type' => 'privacyPolicy', // <-- to build the path: /api/legals/privacyPolicy/media
            ],
        ],
        'privacyPolicy-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.privacyPolicy.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'privacyPolicy', // <-- لبناء المسار: /api/privacy-policy/{owner}/features
            ],
        ],
        // terms of service
        'termsOfService' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/termsOfService',
            'middleware' => ['api'],
            'as' => 'api.legals.termsOfService.',
            'options' => [
                'type' => 'termsOfService'
            ],
        ],
        'termsOfService-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/termsOfService',
            'middleware' => ['api'],
            'as' => 'api.legals.termsOfService.media.', // <-- **important:** it must match 'termsOfService' with the key in morph_map
            'options' => [
                'type' => 'termsOfService', // <-- to build the path: /api/legals/termsOfService/media
            ],
        ],
        'termsOfService-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.termsOfService.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'termsOfService', // <-- لبناء المسار: /api/terms-of-service/{owner}/features
            ],
        ],
        // term of use
        'termOfUse' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/termOfUse',
            'middleware' => ['api'],
            'as' => 'api.legals.termOfUse.',
            'options' => [
                'type' => 'termOfUse'
            ],
        ],
        'termOfUse-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/termOfUse',
            'middleware' => ['api'],
            'as' => 'api.legals.termOfUse.media.', // <-- **important:** it must match 'termOfUse' with the key in morph_map
            'options' => [
                'type' => 'termOfUse', // <-- to build the path: /api/legals/termOfUse/media
            ],
        ],
        'termOfUse-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.termOfUse.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'termOfUse', // <-- لبناء المسار: /api/term-of-use/{owner}/features
            ],
        ],
        // our mission
        'ourMission' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/ourMission',
            'middleware' => ['api'],
            'as' => 'api.legals.ourMission.',
            'options' => [
                'type' => 'ourMission'
            ],
        ],
        'ourMission-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/ourMission',
            'middleware' => ['api'],
            'as' => 'api.legals.ourMission.media.', // <-- **important:** it must match 'ourMission' with the key in morph_map
            'options' => [
                'type' => 'ourMission', // <-- to build the path: /api/legals/ourMission/media
            ],
        ],
        'ourMission-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.ourMission.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'ourMission', // <-- لبناء المسار: /api/our-mission/{owner}/features
            ],
        ],
        // our vision
        'ourVision' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/ourVision',
            'middleware' => ['api'],
            'as' => 'api.legals.ourVision.',
            'options' => [
                'type' => 'ourVision'
            ],
        ],
        'ourVision-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/ourVision',
            'middleware' => ['api'],
            'as' => 'api.legals.ourVision.media.', // <-- **important:** it must match 'ourVision' with the key in morph_map
            'options' => [
                'type' => 'ourVision', // <-- to build the path: /api/legals/ourVision/media
            ],
        ],
        'ourVision-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.ourVision.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'ourVision', // <-- لبناء المسار: /api/our-vision/{owner}/features
            ],
        ],
        // our story
        'ourStory' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/ourStory',
            'middleware' => ['api'],
            'as' => 'api.legals.ourStory.',
            'options' => [
                'type' => 'ourStory'
            ],
        ],
        'ourStory-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/ourStory',
            'middleware' => ['api'],
            'as' => 'api.legals.ourStory.media.', // <-- **important:** it must match 'ourStory' with the key in morph_map
            'options' => [
                'type' => 'ourStory', // <-- to build the path: /api/legals/ourStory/media
            ],
        ],
        'ourStory-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.ourStory.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'ourStory', // <-- لبناء المسار: /api/our-story/{owner}/features
            ],
        ],
        // our history
        'ourHistory' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/ourHistory',
            'middleware' => ['api'],
            'as' => 'api.legals.ourHistory.',
            'options' => [
                'type' => 'ourHistory'
            ],
        ],
        'ourHistory-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/ourHistory',
            'middleware' => ['api'],
            'as' => 'api.legals.ourHistory.media.', // <-- **important:** it must match 'ourHistory' with the key in morph_map
            'options' => [
                'type' => 'ourHistory', // <-- to build the path: /api/legals/ourHistory/media
            ],
        ],
        'ourHistory-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.ourHistory.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'ourHistory', // <-- لبناء المسار: /api/our-history/{owner}/features
            ],
        ],
        // our values
        'ourValues' => [
            'enabled' => true,
            'file' => 'legals.php',
            'prefix' => 'legals/ourValues',
            'middleware' => ['api'],
            'as' => 'api.legals.ourValues.',
            'options' => [
                'type' => 'ourValues'
            ],
        ],
        'ourValues-media' => [
            'enabled' => true,
            'file' => 'media.php', // <-- use the general media template file
            'prefix' => 'legals/ourValues',
            'middleware' => ['api'],
            'as' => 'api.legals.ourValues.media.', // <-- **important:** it must match 'ourValues' with the key in morph_map
            'options' => [
                'type' => 'ourValues', // <-- to build the path: /api/legals/ourValues/media
            ],
        ],
        'ourValues-features' => [
            'enabled' => true,
            'file' => 'features.php', // <-- استخدام ملف القالب العام للميزات
            'prefix' => '/legals',
            'middleware' => ['api'],
            'as' => 'api.legals.ourValues.features.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'ourValues', // <-- لبناء المسار: /api/our-values/{owner}/features
            ],
        ],






        /*
        * =================================================================
         * Blog Modules
         * =================================================================
         * please note that the prefix is the same as the type
         * the binding name is the same as the type
         * the media is the same as the type
         * the options are the same as the type
        */
        'blog' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'blogs',
            'middleware' => ['api'],
            'as' => 'api.blogs.',
            'options' => [
                'type' => 'blog',
                'binding_name' => 'post'
            ],
        ],
        'blog-category' => [
            'enabled' => true,
            'file' => 'category.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.',
            'options' => [
                'type' => 'blog',
            ],
        ],
        'blog-attributes' => [
            'enabled' => true,
            'file' => 'attribute.php', // <-- use the general attribute template file
            'prefix' => '', // <-- empty because the prefix is built dynamically
            'middleware' => ['api'],
            'as' => 'api.', // it will be merged with the route name
            'options' => [
                // this is the most important part that defines the scope
                'type' => 'blog',
            ],
        ],
        'blog-plans' => [
            'enabled' => true,
            'file' => 'plans.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'blogs' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'blogs' في morph_map.
             */
            'as' => 'api.blogs.plans.', // الـ binding سيقرأ 'blogs' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف plans.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /blogs/{owner}/plans
                 */
                'owner_url_name' => 'blogs',
            ],
        ],
        'blog-features' => [
            'enabled' => true,
            'file' => 'features.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'blogs' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'blogs' في morph_map.
             */
            'as' => 'api.blogs.features.', // الـ binding سيقرأ 'blogs' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف features.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /blogs/{owner}/features
                 */
                'owner_url_name' => 'blogs',
            ],
        ],
        'blog-downloads' => [
            'enabled' => true,
            'file' => 'downloads.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'blogs' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'blogs' في morph_map.
             */
            'as' => 'api.blogs.downloads.', // الـ binding سيقرأ 'blogs' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف downloads.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /blogs/{owner}/downloads
                 */
                'owner_url_name' => 'blogs',
            ],
        ],
        'blog-faqs' => [
            'enabled' => true,
            'file' => 'faqs.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'blogs' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'blogs' في morph_map.
             */
            'as' => 'api.blogs.faqs.', // الـ binding سيقرأ 'blogs' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف faqs.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /blogs/{owner}/faqs
                 */
                'owner_url_name' => 'blogs',
            ],
        ],
        'blog-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.blogs.media.',
            'options' => [
                'owner_url_name' => 'blogs',
            ],
        ],


        // service
        'service' => [
            'enabled' => true,
            'file' => 'content.php',
            'prefix' => 'services',
            'middleware' => ['api'],
            'as' => 'api.services.',
            'options' => [
                'type' => 'service',
                'binding_name' => 'post'
            ],
        ],
        'service-category' => [
            'enabled' => true,
            'file' => 'category.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.',
            'options' => [
                'type' => 'service',
            ],
        ],
        'service-attributes' => [
            'enabled' => true,
            'file' => 'attribute.php', // <-- use the general attribute template file
            'prefix' => '', // <-- empty because the prefix is built dynamically
            'middleware' => ['api'],
            'as' => 'api.', // it will be merged with the route name
            'options' => [
                // this is the most important part that defines the scope
                'type' => 'service',
            ],
        ],
        'service-features' => [
            'enabled' => true,
            'file' => 'features.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'services' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'services' في morph_map.
             */
            'as' => 'api.services.features.', // الـ binding سيقرأ 'services' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف features.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /services/{owner}/features
                 */
                'owner_url_name' => 'services',
            ],
        ],
        'service-downloads' => [
            'enabled' => true,
            'file' => 'downloads.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'services' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'services' في morph_map.
             */
            'as' => 'api.services.downloads.', // الـ binding سيقرأ 'services' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف downloads.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /services/{owner}/downloads
                 */
                'owner_url_name' => 'services',
            ],
        ],
        'service-faqs' => [
            'enabled' => true,
            'file' => 'faqs.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'services' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'services' في morph_map.
             */
            'as' => 'api.services.faqs.', // الـ binding سيقرأ 'services' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف faqs.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /services/{owner}/faqs
                 */
                'owner_url_name' => 'services',
            ],
        ],
        'service-plans' => [
            'enabled' => true,
            'file' => 'plans.php', // نفس ملف القالب العام
            'prefix' => '', // لا نحتاج بادئة هنا
            'middleware' => ['api'],

            /**
             * هذا هو الجزء الأهم الآن.
             * الـ Route Model Binding الذكي سيقرأ 'services' من هذا الاسم
             * ليعرف أنه يجب أن يبحث عن نموذج مرتبط بـ 'services' في morph_map.
             */
            'as' => 'api.services.plans.', // الـ binding سيقرأ 'services' من هنا

            'options' => [
                /**
                 * الخيار الوحيد الذي يحتاجه ملف plans.php الآن
                 * هو الجزء الأول من عنوان URL.
                 * سيتم استخدامه لبناء: /services/{owner}/plans
                 */
                'owner_url_name' => 'services',
            ],
        ],
        'service-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api', 'auth:sanctum'],
            'as' => 'api.services.media.',
            'options' => [
                'owner_url_name' => 'services',
            ],
        ],


        // product
        'product-features' => [
            'enabled' => true,
            'file' => 'features.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.features-products.',
            'options' => [
                'type' => 'products',
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
                'type' => 'products',
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
                'type' => 'products',
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
                'type' => 'products',
                'owner_type' => 'product',
                'owner_url_prefix' => 'product',
                'owner_url_name' => 'product',
            ],
        ],





        // authorization and roles module
        'authorization' => [
            'enabled' => true,
            'file' => 'authorization.php',
            'middleware' => ['api'],
        ],

        // miscellaneous routes module
        'others' => [
            'enabled' => true,
            'file' => 'others.php',
            'middleware' => ['api'],
            'as' => 'api.',
            'prefix' => ''
        ],


        'statistics' => [
            'enabled' => true,
            'file' => 'statistics.php',
            'prefix' => 'statistics',
            'middleware' => ['api'],
            'as' => 'api.statistics.',
            'options' => [],
        ],
        'statistics-media' => [
            'enabled' => true,
            'file' => 'media.php',
            'prefix' => '',
            'middleware' => ['api'],
            'as' => 'api.statistics.media.', // <-- للتوافق مع morph_map
            'options' => [
                'owner_url_name' => 'statistics', // <-- لبناء المسار: /api/statistics/{owner}/media
            ],
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
