<?php

/**
 * =================================================================
 * ملف تسجيل مسارات حزمة CMS - النظام الجديد
 * =================================================================
 * هذا الملف يحل محل مصفوفة 'routes' في config/cms.php.
 * استخدم الواجهة البرمجية السلسة لتعريف وتخصيص المسارات التي تحتاجها.
 */

use HMsoft\Cms\Facades\CmsRoute;
use HMsoft\Cms\Routing\CmsRouteBlueprint;

// 1. الوحدات الأساسية (Core Modules)
CmsRoute::portfolios();
CmsRoute::blogs();
CmsRoute::services();
CmsRoute::sponsors();
CmsRoute::partners();
CmsRoute::statistics();

// 2. المسارات المتداخلة الخاصة بـ Portfolio
CmsRoute::category('portfolio');
CmsRoute::attribute('portfolio');
CmsRoute::plans('portfolios');
CmsRoute::features('portfolios');
CmsRoute::downloads('portfolios');
CmsRoute::faqs('portfolios');
CmsRoute::nestedBlogs('portfolios');
CmsRoute::nestedServices('portfolios');
CmsRoute::media('portfolios', function (CmsRouteBlueprint $blueprint) {
    // تطبيق middleware مخصص كما كان في الإعدادات القديمة
    $blueprint->middleware(['api', 'auth:sanctum']);
});


// 3. المسارات المتداخلة الخاصة بـ Blog
CmsRoute::category('blog');
CmsRoute::attribute('blog');
CmsRoute::plans('blogs');
CmsRoute::features('blogs');
CmsRoute::downloads('blogs');
CmsRoute::faqs('blogs');
CmsRoute::media('blogs', function (CmsRouteBlueprint $blueprint) {
    $blueprint->middleware(['api', 'auth:sanctum']);
});


// 4. المسارات المتداخلة الخاصة بـ Service
CmsRoute::category('service');
CmsRoute::attribute('service');
CmsRoute::plans('services');
CmsRoute::features('services');
CmsRoute::downloads('services');
CmsRoute::faqs('services');
CmsRoute::media('services', function (CmsRouteBlueprint $blueprint) {
    $blueprint->middleware(['api', 'auth:sanctum']);
});


// 5. المسارات المتداخلة الخاصة بـ Statistics
CmsRoute::media('statistics');


// 6. الصفحات القانونية (Legal Pages) - تم تجميعها بشكل منطقي
CmsRoute::legal('legal', 'legals'); // المسار الأساسي

$legalPages = [
    'aboutUs',
    'privacyPolicy',
    'termsOfService',
    'termOfUse',
    'ourMission',
    'ourVision',
    'ourStory',
    'ourHistory',
    'ourValues'
];

foreach ($legalPages as $page) {
    // تسجيل الصفحة نفسها
    CmsRoute::legal($page, "legals/{$page}");

    // تسجيل الوسائط الخاصة بالصفحة
    CmsRoute::legalMedia($page, "legals/{$page}");

    // تسجيل الميزات الخاصة بالصفحة (مع الحفاظ على البنية المعقدة القديمة)
    CmsRoute::features($page, function (CmsRouteBlueprint $blueprint) use ($page) {
        $blueprint->prefix("legals")->name("api.legals.{$page}.features.");
    });
}


// 7. وحدات المشروع المخصصة (مثال Products) - معطلة افتراضيًا
/*
CmsRoute::features('products', function(CmsRouteBlueprint $blueprint) {
    $blueprint->name('api.features-products.');
});
CmsRoute::downloads('products', function(CmsRouteBlueprint $blueprint) {
    $blueprint->name('api.downloads-products.');
});
CmsRoute::faqs('products', function(CmsRouteBlueprint $blueprint) {
    $blueprint->name('api.faqs-products.');
});
CmsRoute::plans('products', function(CmsRouteBlueprint $blueprint) {
    $blueprint->name('api.plans-products.');
});
*/


// 8. المسارات العامة (كانت سابقًا في authorization.php و others.php)
// تم تقسيمها الآن لزيادة التحكم والوضوح
CmsRoute::authorization();

CmsRoute::sectors();
CmsRoute::testimonials();
CmsRoute::teams();
CmsRoute::languages();
CmsRoute::settings();
CmsRoute::contactUs();
CmsRoute::pagesMeta();
CmsRoute::misc(); // للمسارات المتفرقة مثل صلاحيات الزائر