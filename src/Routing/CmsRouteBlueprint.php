<?php

namespace HMsoft\Cms\Routing;

use Closure;
use Illuminate\Routing\ControllerDispatcher;

/**
 * Represents a configurable blueprint for a route group.
 * An instance of this class is passed to the developer's closure to allow full customization.
 *
 * يمثل هذا الكلاس مخططًا قابلاً للتعديل لمجموعة مسارات.
 * يتم تمريره إلى الـ Closure الخاص بالمطور للسماح بالتخصيص الكامل.
 */
class CmsRouteBlueprint
{
    public bool $enabled = true;
    public array $config = [];
    public ?string $controller = null;

    // Properties for advanced control
    protected array $only = [];
    protected array $except = [];
    protected array $routeMiddleware = [];
    protected array $routeNames = [];
    protected array $routeActions = [];
    protected array $routePermissions = [];
    protected array $routeDocs = [];
    public array $addedRoutes = [];
    public array $taps = [];
    protected array $customDefaults = [];
    protected ?string $ownerBindingName = null;


    public function __construct(array $initialConfig)
    {
        $this->config = $initialConfig;
    }

    //== Group Level Control (التحكم على مستوى المجموعة) ==//
    public function prefix(string $prefix): self
    {
        $this->config['prefix'] = $prefix;
        return $this;
    }
    public function name(string $name): self
    {
        $this->config['as'] = $name;
        return $this;
    }
    public function middleware(array|string $middleware): self
    {
        $this->config['middleware'] = (array) $middleware;
        return $this;
    }
    public function addMiddleware(array|string $middleware): self
    {
        $current = $this->config['middleware'] ?? [];
        $this->config['middleware'] = array_unique(array_merge($current, (array) $middleware));
        return $this;
    }
    public function controller(string $controllerClass): self
    {
        $this->controller = $controllerClass;
        return $this;
    }
    public function disable(): self
    {
        $this->enabled = false;
        return $this;
    }

    //== Declarative Route Filtering (فلترة المسارات الوصفية) ==//
    /**
     * Register only the specified routes from the module file.
     * تسجيل المسارات المحددة فقط من ملف الوحدة.
     * @param array|string $routes Names like 'index', 'show', 'store'. | أسماء مثل 'index', 'show', 'store'.
     */
    public function only(array|string $routes): self
    {
        $this->only = (array) $routes;
        return $this;
    }

    /**
     * Register all routes from the module file except the specified ones.
     * تسجيل كل المسارات من ملف الوحدة ما عدا المحددة.
     * @param array|string $routes Names like 'destroy', 'updateAll'. | أسماء مثل 'destroy', 'updateAll'.
     */
    public function except(array|string $routes): self
    {
        $this->except = (array) $routes;
        return $this;
    }

    //== Per-Route Control (التحكم بالمسارات الفردية) ==//
    public function addMiddlewareToRoute(string $routeName, array|string $middleware): self
    {
        $this->routeMiddleware[$routeName]['add'] = (array) $middleware;
        return $this;
    }
    public function middlewareFor(string $routeName, array|string $middleware): self
    {
        $this->routeMiddleware[$routeName]['replace'] = (array) $middleware;
        return $this;
    }
    public function withoutMiddlewareFor(string $routeName, array|string $middleware): self
    {
        $this->routeMiddleware[$routeName]['remove'] = (array) $middleware;
        return $this;
    }
    public function nameFor(string $routeName, string $newName): self
    {
        $this->routeNames[$routeName] = $newName;
        return $this;
    }
    public function actionFor(string $routeName, array|string|Closure $action): self
    {
        $this->routeActions[$routeName] = $action;
        return $this;
    }
    public function addRoute(string $method, string $uri, string $action, string $name): self
    {
        $this->addedRoutes[] = compact('method', 'uri', 'action', 'name');
        return $this;
    }
    public function tap(string $routeName, Closure $callback): self
    {
        $this->taps[$routeName] = $callback;
        return $this;
    }


    /**
     * Set or merge default parameters for all routes in the group.
     * These will be merged with automatic defaults, with these taking precedence.
     *
     * تعيين أو دمج بارامترات افتراضية لكل المسارات في المجموعة.
     * سيتم دمجها مع البارامترات التلقائية، مع إعطاء الأولوية لهذه.
     *
     * @param array $defaults
     * @return self
     */
    public function defaults(array $defaults): self
    {
        $this->customDefaults = array_merge($this->customDefaults, $defaults);
        return $this;
    }


    //== Advanced Feature Integrations (تكامل الميزات المتقدمة) ==//
    /**
     * Applies a permission middleware to the entire group.
     * يطبق صلاحية على المجموعة بأكملها.
     * @param string $permission The permission string. | نص الصلاحية.
     */
    public function permission(string $permission): self
    {
        $this->addMiddleware("can:{$permission}");
        return $this;
    }

    /**
     * Applies a permission middleware to a specific route within the group.
     * يطبق صلاحية على مسار محدد داخل المجموعة.
     * @param string $routeName The name of the route (e.g., 'store'). | اسم المسار.
     * @param string $permission The permission string. | نص الصلاحية.
     */
    public function permissionFor(string $routeName, string $permission): self
    {
        $this->routePermissions[$routeName] = $permission;
        return $this;
    }

    /**
     * Attaches documentation metadata to the entire group.
     * يرفق بيانات وصفية للتوثيق بالمجموعة بأكملها.
     * @param array $docs Associative array of OpenAPI spec (e.g., ['tags' => ['Blogs']]). | مصفوفة بيانات.
     */
    public function docs(array $docs): self
    {
        $this->config['docs'] = $docs;
        return $this;
    }

    /**
     * Attaches documentation metadata to a specific route.
     * يرفق بيانات وصفية للتوثيق بمسار محدد.
     * @param string $routeName The name of the route (e.g., 'show'). | اسم المسار.
     * @param array $docs Associative array of OpenAPI spec. | مصفوفة بيانات.
     */
    public function docsFor(string $routeName, array $docs): self
    {
        $this->routeDocs[$routeName] = $docs;
        return $this;
    }

    /**
     * Explicitly define the route model binding parameter name for the parent/owner.
     * تعريف اسم متغير ربط الموديل للمالك بشكل صريح.
     *
     * @param string $name The parameter name to use in the URL (e.g., 'portfolio', 'blog').
     * @return self
     */
    public function ownerBinding(string $name): self
    {
        $this->ownerBindingName = $name;
        return $this;
    }

    //== Internal Getters (توابع داخلية لجلب البيانات) ==//
    public function getOnly(): array
    {
        return $this->only;
    }
    public function getExcept(): array
    {
        return $this->except;
    }
    public function getRouteMiddleware(string $routeName): array
    {
        return $this->routeMiddleware[$routeName] ?? [];
    }
    public function getRouteName(string $routeName): ?string
    {
        return $this->routeNames[$routeName] ?? null;
    }
    public function getRouteAction(string $routeName): array|string|Closure|null
    {
        return $this->routeActions[$routeName] ?? null;
    }
    public function getRoutePermission(string $routeName): ?string
    {
        return $this->routePermissions[$routeName] ?? null;
    }
    public function getRouteDocs(string $routeName): array
    {
        return $this->routeDocs[$routeName] ?? [];
    }
    public function getCustomDefaults(): array
    {
        return $this->customDefaults;
    }
    public function getOwnerBinding(): ?string
    {
        return $this->ownerBindingName;
    }
}
