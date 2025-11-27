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
        'items' => \HMsoft\Cms\Models\Shop\Item::class,
    ],

];
