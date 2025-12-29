<?php

namespace HMsoft\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * =================================================================
 * CmsRoute Facade - The main entry point for the CMS routing system.
 * =================================================================
 *
 * This facade provides a fluent, expressive, and developer-friendly API
 * for registering and customizing all the API routes provided by the package.
 *
 * يوفر هذا الـ Facade واجهة برمجية سلسلة ومعبرة وسهلة للمطورين
 * لتسجيل وتخصيص كل مسارات الـ API التي توفرها الحزمة.
 *
 * @see \HMsoft\Cms\Routing\CmsRouteManager
 *
 * --- Advanced Features (الميزات المتقدمة) ---
 * @method static void version(string $version, \Closure $routes)
 * @method static void macro(string $name, object|callable $macro)
 * @method static bool hasMacro(string $name)
 *
 * --- Core Resource Routes (الموارد الأساسية) ---
 * @method static void blogs(\Closure $callback = null)
 * @method static void portfolios(\Closure $callback = null)
 * @method static void services(\Closure $callback = null)
 * @method static void sponsors(\Closure $callback = null)
 * @method static void organization(string $type, string $prefix, string $as, ?Closure $callback = null)
 * @method static void partners(\Closure $callback = null)
 * @method static void statistics(\Closure $callback = null)
 * @method static void sectors(\Closure $callback = null)
 * @method static void testimonials(\Closure $callback = null)
 * @method static void teams(\Closure $callback = null)
 * @method static void languages(\Closure $callback = null)
 *
 * --- Nested Resource Routes (الموارد المتداخلة) ---
 * @method static void features(string $parent, \Closure $callback = null)
 * @method static void downloads(string $parent, \Closure $callback = null)
 * @method static void faqs(string $parent, \Closure $callback = null)
 * @method static void plans(string $parent, \Closure $callback = null)
 * @method static void media(string $parent, \Closure $callback = null)
 * @method static void nestedBlogs(string $parent, \Closure $callback = null)
 * @method static void nestedServices(string $parent, \Closure $callback = null)
 *
 * --- Scoped Resource Routes (الموارد المحددة النطاق) ---
 * @method static void category(string $type, \Closure $callback = null)
 * @method static void attribute(string $type, \Closure $callback = null)
 *
 * --- Special & Singleton Routes (المسارات الخاصة والفردية) ---
 * @method static void legal(string $type, string $prefix, \Closure $callback = null)
 * @method static void legalMedia(string $pageType, string $prefix, \Closure $callback = null)
 * @method static void legalFeatures(string $pageType, \Closure $callback = null)
 * @method static void settings(\Closure $callback = null)
 * @method static void contactUs(\Closure $callback = null)
 * @method static void pagesMeta(\Closure $callback = null)
 * @method static void misc(\Closure $callback = null)
 *
 * --- Authorization Routes (المسارات الصلاحيات) ---
 * @method static void permissions(\Closure $callback = null)
 * @method static void roles(\Closure $callback = null)
 * @method static void ourValues(\Closure $callback = null)
 * 
 * --- Download Items Routes (المسارات الموارد التحميلات) ---
 * @method static void downloadItems(string $pluralName = 'downloadItems', \Closure $callback = null)
 *
 * --- Item Routes (المسارات الموارد العناصر) ---
 * @method static void items(string $pluralName = 'items', \Closure $callback = null)
 *
 * --- Item Addons Routes (المسارات الموارد الإضافات) ---
 * @method static void itemAddons(array $options = [])
 * @method static void itemVariations(array $options = [])
 */
class CmsRoute extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cms.route-manager';
    }
}
