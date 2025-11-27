<?php

namespace HMsoft\Cms\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Closure;
use BadMethodCallException;


/**
 * The main engine for fluently registering CMS routes.
 * This class is the implementation behind the CmsRoute facade. It uses CmsRouteBlueprint
 * objects to provide a powerful and customizable routing API.
 *
 * المحرك الرئيسي لتسجيل مسارات نظام إدارة المحتوى بسلاسة.
 * هذا الكلاس هو التنفيذ الفعلي لواجهة CmsRoute. يستخدم كائنات CmsRouteBlueprint
 * لتوفير واجهة برمجية قوية وقابلة للتخصيص للمسارات.
 *
 * @see \HMsoft\Cms\Facades\CmsRoute
 * @see \HMsoft\Cms\Routing\CmsRouteBlueprint
 * @see \HMsoft\Cms\Routing\RouteRegistrar
 */
class CmsRouteManager
{
    /**
     * Storage for user-defined macros.
     * لتخزين الـ macros المعرفة من قبل المطور.
     * @var array
     */
    protected static array $macros = [];
    //======================================================================
    //== Developer-facing Helper Methods
    //======================================================================

    /**
     * Registers the core blog resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للمدونة.
     * @param \Closure|null $callback
     * @return void
     */
    public function blogs(?Closure $callback = null): void
    {
        $this->resource('blog', 'blogs', $callback);
    }

    /**
     * Registers the core portfolio resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للمشاريع.
     * @param \Closure|null $callback
     * @return void
     */
    public function portfolios(?Closure $callback = null): void
    {
        $this->resource('portfolio', 'portfolios', $callback);
    }

    /**
     * Registers the core service resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للخدمات.
     * @param \Closure|null $callback
     * @return void
     */
    public function services(?Closure $callback = null): void
    {
        $this->resource('service', 'services', $callback);
    }

    /**
     * Registers the core sponsor resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للرعاة.
     * @param \Closure|null $callback
     * @return void
     */
    public function sponsors(?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'organizations.php',
            'prefix' => 'sponsors',
            'as' => 'api.sponsors.',
            'middleware' => ['api'],
            'options' => ['type' => 'sponsor'] // <-- The crucial addition
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the core sponsor resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للرعاة.
     * @param \Closure|null $callback
     * @return void
     */
    public function organization(string $type, string $prefix, string $as, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'organizations.php',
            'prefix' => $prefix,
            'as' => $as,
            'middleware' => ['api'],
            'options' => ['type' => $type] // <-- The crucial addition
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the core partner resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للشركاء.
     * @param \Closure|null $callback
     * @return void
     */
    public function partners(?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'organizations.php',
            'prefix' => 'partners',
            'as' => 'api.partners.',
            'middleware' => ['api'],
            'options' => ['type' => 'partner'] // <-- The crucial addition
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the core statistics resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للإحصائيات.
     * @param \Closure|null $callback
     * @return void
     */
    public function statistics(?Closure $callback = null): void
    {
        $this->resource('statistics', 'statistics', $callback);
    }

    public function downloadItems(string $pluralName = 'downloadItems', ?Closure $callback = null): void
    {
        $this->resource('downloadItem', $pluralName, $callback);
    }

    public function items(string $pluralName = 'items', ?Closure $callback = null): void
    {
        $this->resource('items', $pluralName, $callback);
    }

    /**
     * Registers the core sector resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للقطاعات.
     * @param \Closure|null $callback
     * @return void
     */
    public function sectors(?Closure $callback = null): void
    {
        $this->resource('sector', 'sectors', $callback);
    }
    /**
     * Registers the core testimonial resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية لآراء العملاء.
     * @param \Closure|null $callback
     * @return void
     */
    public function testimonials(?Closure $callback = null): void
    {
        $this->resource('testimonial', 'testimonials', $callback);
    }
    /**
     * Registers the core team resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية لفرق العمل.
     * @param \Closure|null $callback
     * @return void
     */
    public function teams(?Closure $callback = null): void
    {
        $this->resource('team', 'teams', $callback);
    }
    /**
     * Registers the core language resource routes.
     * يقوم بتسجيل مسارات المورد الأساسية للغات.
     * @param \Closure|null $callback
     * @return void
     */
    public function languages(?Closure $callback = null): void
    {
        $this->resource('language', 'langs', $callback);
    }

    /**
     * Registers nested blog routes for a parent resource.
     * يقوم بتسجيل مسارات المدونات المتداخلة لمورد أب.
     * * ملاحظة: في النظام الجديد، هذه المسارات تستخدم لإدارة "العلاقة"
     * (عرض المرتبط، إضافة جديد وربطه، فك الارتباط).
     * * @param string $parent The plural name of the parent resource.
     * @param \Closure|null $callback
     * @return void
     */
    public function nestedBlogs(string $parent, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'nested_blog.php', // تأكد أن هذا الملف موجود في routes/modules
            // المسار سيصبح مثلاً: api/items/{item}/blogs
            'prefix' => "{$parent}/{_OWNER_BINDING_}/blogs",
            'as' => "api.{$parent}.blogs.",
            'middleware' => ['api'],
            // إضافة خيار لتحديد نوع الأب إذا احتجنا له لاحقاً
            'options' => ['parent_resource' => $parent]
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers nested service routes for a parent resource.
     * يقوم بتسجيل مسارات الخدمات المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function nestedServices(string $parent, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'nested_service.php',
            'prefix' => "{$parent}/{_OWNER_BINDING_}/services",
            'as' => "api.{$parent}.services.",
            'middleware' => ['api']
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers nested feature routes for a parent resource.
     * يقوم بتسجيل مسارات الميزات المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function features(string $parent, ?Closure $callback = null): void
    {
        $this->nestedResource('feature', $parent, $callback);
    }

    /**
     * Registers nested download routes for a parent resource.
     * يقوم بتسجيل مسارات التحميلات المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function downloads(string $parent, ?Closure $callback = null): void
    {
        $this->nestedResource('download', $parent, $callback);
    }



    /**
     * Registers nested FAQ routes for a parent resource.
     * يقوم بتسجيل مسارات الأسئلة الشائعة المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function faqs(string $parent, ?Closure $callback = null): void
    {
        $this->nestedResource('faq', $parent, $callback);
    }

    /**
     * Registers nested Plan routes for a parent resource.
     * يقوم بتسجيل مسارات الخطط المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function plans(string $parent, ?Closure $callback = null): void
    {
        $this->nestedResource('plan', $parent, $callback);
    }

    /**
     * Registers nested media routes for a parent resource.
     * يقوم بتسجيل مسارات الوسائط المتداخلة لمورد أب.
     * @param string $parent The plural name of the parent resource. | اسم الجمع للمورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function media(string $parent, ?Closure $callback = null): void
    {
        $this->nestedResource('media', $parent, $callback);
    }

    /**
     * Registers category routes for a specific type.
     * يقوم بتسجيل مسارات الأصناف لنوع معين.
     * @param string $type The type of the parent resource (e.g., 'blog', 'portfolio'). | نوع المورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function category(string $type, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'category.php',
            'prefix' => "{$type}-categories",
            'as' => "api.{$type}.categories.",
            'middleware' => ['api'],
            'options' => ['type' => $type]
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers attribute routes for a specific type.
     * يقوم بتسجيل مسارات السمات لنوع معين.
     * @param string $type The type of the parent resource (e.g., 'blog', 'portfolio'). | نوع المورد الأب.
     * @param \Closure|null $callback
     * @return void
     */
    public function attribute(string $type, ?Closure $callback = null): void
    {
        $defaults = ['file' => 'attribute.php', 'prefix' => "{$type}-attributes", 'as' => "api.{$type}.attributes.", 'middleware' => ['api'], 'options' => ['type' => $type]];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers a legal page resource route.
     * يقوم بتسجيل مسار مورد لصفحة قانونية.
     * @param string $type The type of the legal page (e.g., 'aboutUs'). | نوع الصفحة القانونية.
     * @param string $prefix The full URI prefix for the page. | البادئة الكاملة للمسار.
     * @param \Closure|null $callback
     * @return void
     */
    public function legal(string $type, string $prefix, ?Closure $callback = null): void
    {
        $defaults = ['file' => 'legals.php', 'prefix' => $prefix, 'as' => "api.legals.{$type}.", 'middleware' => ['api'], 'options' => ['type' => $type]];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers media routes for a singleton legal page.
     * يقوم بتسجيل مسارات الوسائط لصفحة قانونية فردية.
     *
     * @param string $pageType The type of the legal page (e.g., 'aboutUs'). | نوع الصفحة القانونية.
     * @param string $prefix The full URI prefix for the page's media. | البادئة الكاملة لمسار الوسائط.
     * @param \Closure|null $callback
     * @return void
     */
    public function legalMedia(string $pageType, string $prefix, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'legal_media.php',
            'prefix' => $prefix,
            'as' => "api.legals.{$pageType}.media.",
            'middleware' => ['api'],
            'options' => ['type' => $pageType]
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers nested feature routes for a singleton legal page.
     * This method automatically applies the 'inject.singleton.owner' middleware.
     *
     * يقوم بتسجيل مسارات الميزات المتداخلة لصفحة قانونية فردية.
     * تقوم هذه الدالة تلقائيًا بتطبيق middleware 'inject.singleton.owner'.
     *
     * @param string $pageType The type of the legal page (e.g., 'aboutUs').
     * @param \Closure|null $callback
     * @return void
     */
    public function legalFeatures(string $pageType, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'features.php',
            // The prefix does NOT contain a model ID placeholder
            'prefix' => "{$pageType}/features",
            'as' => "api.legals.{$pageType}.features.",
            // The middleware will be added automatically
            'middleware' => ['api', 'inject.singleton.legal.owner'],
            'options' => ['singleton_type' => $pageType] // Pass the type as metadata
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the business settings routes.
     * يقوم بتسجيل مسارات إعدادات النظام.
     *
     * @param \Closure|null $callback An optional closure for customization. | دالة اختيارية للتخصيص.
     * @return void
     */
    public function settings(?Closure $callback = null): void
    {
        $defaults = ['file' => 'settings.php', 'prefix' => 'settings', 'as' => 'api.settings.', 'middleware' => ['api']];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the contact-us message routes.
     * يقوم بتسجيل مسارات رسائل تواصل معنا.
     *
     * @param \Closure|null $callback An optional closure for customization. | دالة اختيارية للتخصيص.
     * @return void
     */
    public function contactUs(?Closure $callback = null): void
    {
        $defaults = ['file' => 'contact_us.php', 'prefix' => 'contact-us', 'as' => 'api.contact-us.', 'middleware' => ['api']];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers the pages' meta-information routes.
     * يقوم بتسجيل مسارات البيانات الوصفية للصفحات.
     *
     * @param \Closure|null $callback An optional closure for customization. | دالة اختيارية للتخصيص.
     * @return void
     */
    public function pagesMeta(?Closure $callback = null): void
    {
        $defaults = ['file' => 'pages_meta.php', 'prefix' => 'pages-meta', 'as' => 'api.pages-meta.', 'middleware' => ['api']];
        $this->registerRouteGroup($defaults, $callback);
    }


    /**
     * Registers the our value routes.
     * يقوم بتسجيل مسارات القيم التي نقدمها.
     * @param \Closure|null $callback
     * @return void
     * 
     * @param \Closure|null $callback An optional closure for customization. | دالة اختيارية للتخصيص.
     * @return void
     */
    public function ourValues(?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'our_value.php',
            'prefix' => 'ourValues',
            'as' => 'api.ourValues.',
            'middleware' => ['api']
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers miscellaneous standalone routes.
     * يقوم بتسجيل مسارات متنوعة ومستقلة.
     *
     * @param \Closure|null $callback An optional closure for customization. | دالة اختيارية للتخصيص.
     * @return void
     */
    public function misc(?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'misc.php',
            'prefix' => '',
            'as' => 'api.',
            'middleware' => ['api']
        ];
        $this->registerRouteGroup($defaults, $callback);
    }


    /**
     * Registers permission management routes.
     * يقوم بتسجيل مسارات إدارة الصلاحيات.
     * @param \Closure|null $callback
     * @return void
     */
    public function permissions(?Closure $callback = null): void
    {
        $defaults = ['file' => 'permissions.php', 'prefix' => 'permissions', 'as' => 'api.permissions.', 'middleware' => ['api']];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers role management routes.
     * يقوم بتسجيل مسارات إدارة الأدوار.
     * @param \Closure|null $callback
     * @return void
     */
    public function roles(?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'roles.php',
            'prefix' => 'roles',
            'as' => 'api.roles.',
            'middleware' => ['api']
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers nested authorization routes for a user resource.
     * يقوم بتسجيل مسارات الصلاحيات المتداخلة لمورد مستخدم.
     * @param string $parent The plural name of the parent user resource (e.g., 'users').
     * @param \Closure|null $callback
     * @return void
     */
    public function userAuthorizations(string $parent, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => 'user_authorizations.php',
            'prefix' => "{$parent}/{user}",
            'as' => "api.{$parent}.authorizations.",
            'middleware' => ['api']
        ];
        $this->registerRouteGroup($defaults, $callback);
    }


    //======================================================================
    //== Advanced Feature Implementations
    //======================================================================

    /**
     * Group routes within a specific API version.
     * تجميع المسارات ضمن إصدار API محدد.
     * @param string $version The version prefix (e.g., 'v1'). | بادئة الإصدار.
     * @param \Closure $routes The closure defining the routes for this version. | الـ Closure التي تعرف المسارات.
     * @return void
     */
    public function version(string $version, Closure $routes): void
    {
        Route::prefix($version)->group($routes);
    }

    /**
     * Register a custom macro.
     * تسجيل macro مخصص.
     * @param string $name The name of the macro. | اسم الماكرو.
     * @param object|callable $macro The macro callable. | الـ Macro.
     * @return void
     */
    public static function macro(string $name, object|callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    //======================================================================
    //== Core Registration Logic
    //======================================================================


    /**
     * The generic group registration logic that orchestrates the entire process.
     * It reads route definitions, applies all blueprint customizations (filtering, overrides,
     * permissions, docs), and registers the final routes.
     *
     * المنطق العام لتسجيل مجموعة المسارات الذي ينسق العملية بأكملها.
     * يقوم بقراءة تعريفات المسارات، تطبيق كل تخصيصات الـ Blueprint (الفلترة، التجاوزات،
     * الصلاحيات، التوثيق)، ومن ثم تسجيل المسارات النهائية.
     *
     * @param array $defaults Default configuration for the route group. | الإعدادات الافتراضية لمجموعة المسار.
     * @param \Closure|null $callback The developer's customization closure. | دالة التخصيص الخاصة بالمطور.
     * @return void
     */
    protected function registerRouteGroup(array $defaults, ?Closure $callback = null): void
    {
        $blueprint = new CmsRouteBlueprint($defaults);
        if ($callback) {
            $callback($blueprint);
        }

        if (!$blueprint->enabled) {
            return;
        }

        $routeDefinition = $this->loadRouteDefinition($blueprint->config['file']);
        if (!$routeDefinition || !is_callable($routeDefinition['routes'])) {
            return;
        }

        $controller = $blueprint->controller ?? $routeDefinition['controller'] ?? null;
        $groupOptions = $blueprint->config;

        // Prepare default parameters for all routes in this group.
        $automaticDefaults = $groupOptions['options'] ?? [];
        $customDefaults = $blueprint->getCustomDefaults();
        $finalDefaults = array_merge($automaticDefaults, $customDefaults);

        // Get the explicit binding name set by the developer (e.g., 'portfolio').
        $explicitBindingName = $blueprint->getOwnerBinding();

        if ($explicitBindingName) {
            // **THIS IS THE KEY REFINEMENT**
            // Pass the explicit binding name as metadata to the route. This allows
            // our custom 'owner' resolver to know which actual model parameter
            // (e.g., 'portfolio') to retrieve from the route object.
            $finalDefaults['_owner_binding_key'] = $explicitBindingName;
        }

        // The binding name used in the URL is either the explicit one or defaults to 'owner'.
        $bindingName = $explicitBindingName ?? 'owner';

        // Replace the placeholder in the prefix with the final binding name.
        if (isset($groupOptions['prefix'])) {
            $groupOptions['prefix'] = str_replace('{_OWNER_BINDING_}', "{{$bindingName}}", $groupOptions['prefix']);
        }

        unset($groupOptions['options'], $groupOptions['docs']);

        Route::group($groupOptions, function () use ($blueprint, $controller, $routeDefinition, $finalDefaults) {
            $registrar = new RouteRegistrar();
            ($routeDefinition['routes'])($registrar, $blueprint->config);
            $definedRoutes = $registrar->getRoutes();

            $routesToRegister = $this->filterRoutes($definedRoutes, $blueprint->getOnly(), $blueprint->getExcept());

            foreach ($routesToRegister as $routeData) {
                $routeName = $routeData['name'];

                $actionString = $blueprint->getRouteAction($routeName) ?? $routeData['action'];
                $finalName = $blueprint->getRouteName($routeName) ?? $routeName;
                $middlewareConfig = $blueprint->getRouteMiddleware($routeName);
                $permission = $blueprint->getRoutePermission($routeName);
                $routeDocs = $blueprint->getRouteDocs($routeName);

                $finalAction = (is_string($actionString) && $controller) ? [$controller, $actionString] : $actionString;

                $route = Route::{$routeData['method']}($routeData['uri'], $finalAction)->name($finalName);

                // Apply the combined defaults, which now includes our _owner_binding_key.
                if (!empty($finalDefaults)) {
                    $route->setDefaults($finalDefaults);
                }

                // Apply any chained method calls from the route definition file (e.g., ->where()).
                if (!empty($routeData['chained'])) {
                    foreach ($routeData['chained'] as $call) {
                        if ($call['method'] === 'defaults') {
                            $route->setDefaults(array_merge($route->defaults, $call['parameters'][0]));
                            continue;
                        }
                        $route->{$call['method']}(...$call['parameters']);
                    }
                }

                // Apply per-route middleware customizations.
                if (!empty($middlewareConfig['replace'])) $route->middleware($middlewareConfig['replace']);
                if (!empty($middlewareConfig['add'])) $route->middleware($middlewareConfig['add']);
                if (!empty($middlewareConfig['remove'])) $route->withoutMiddleware($middlewareConfig['remove']);

                // Apply permission middleware.
                if ($permission) $route->middleware("can:{$permission}");

                // Attach documentation metadata.
                if (!empty($routeDocs)) $route->defaults('_docs', $routeDocs);
            }

            // Register any additionally defined routes.
            foreach ($blueprint->addedRoutes as $route) {
                $addedRoute = Route::{$route['method']}($route['uri'], $route['action'])->name($route['name']);
                if ($controller && is_string($route['action'])) {
                    $addedRoute->uses([$controller, $route['action']]);
                }
            }

            // Allow tapping into registered routes for further modification.
            foreach ($blueprint->taps as $routeName => $cb) {
                $finalRouteName = $blueprint->getRouteName($routeName) ?? $routeName;
                $fullRouteName = $blueprint->config['as'] . $finalRouteName;
                if ($routeObject = Route::getRoutes()->getByName($fullRouteName)) {
                    $cb($routeObject);
                }
            }
        });
    }

    /**
     * Registers a primary resource group (e.g., blogs, portfolios).
     * يقوم بتسجيل مجموعة موارد أساسية (مثل المدونات، المشاريع).
     */
    public function resource(string $name, string $pluralName, ?Closure $callback = null): void
    {
        $defaults = [
            'file' => "{$name}.php",
            'prefix' => $pluralName,
            'as' => "api.{$pluralName}.",
            'middleware' => ['api'],
        ];
        $this->registerRouteGroup($defaults, $callback);
    }

    /**
     * Registers a nested resource group (e.g., features, downloads).
     * يقوم بتسجيل مجموعة موارد متداخلة (مثل الميزات، التحميلات).
     */
    public function nestedResource(string $childName, string $parentPluralName, ?Closure $callback = null): void
    {
        $childPluralName = Str::plural($childName);
        $defaults = [
            'file' => "{$childPluralName}.php",
            'prefix' => "{$parentPluralName}/{_OWNER_BINDING_}/{$childPluralName}",
            'as' => "api.{$parentPluralName}.{$childPluralName}.",
            'middleware' => ['api'],
            'options' => [
                'owner_url_name' => $parentPluralName
            ]
        ];
        $this->registerRouteGroup($defaults, $callback);
    }


    /**
     * Loads and returns the route definition array from a module file.
     * يقوم بتحميل وإرجاع مصفوفة تعريف المسار من ملف الوحدة.
     * @param string $fileName
     * @return array|null
     */
    protected function loadRouteDefinition(string $fileName): ?array
    {
        $filePath = __DIR__ . '/../../routes/modules/' . $fileName;
        if (file_exists($filePath)) {
            return require $filePath;
        }
        return null;
    }

    /**
     * Filters a list of route definitions based on 'only' and 'except' rules.
     * يقوم بفلترة قائمة تعريفات المسارات بناءً على قواعد 'only' و 'except'.
     */
    protected function filterRoutes(array $routes, array $only, array $except): array
    {
        if (!empty($only)) {
            return array_filter($routes, fn($route) => in_array($route['name'], $only));
        }

        if (!empty($except)) {
            return array_filter($routes, fn($route) => !in_array($route['name'], $except));
        }

        return $routes;
    }

    //======================================================================
    //== Macro Handling
    //======================================================================

    public function __call(string $method, array $parameters): mixed
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            ));
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            return $macro->call($this, ...$parameters);
        }

        return $macro(...$parameters);
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }
}
