<?php

namespace HMsoft\Cms\Routing;


/**
 * A helper class to collect route definitions before they are registered.
 * This allows for modification via the Blueprint (only, except, etc.).
 *
 * كلاس مساعد لتجميع تعريفات المسارات قبل تسجيلها الفعلي.
 * هذا يسمح بتعديلها عبر الـ Blueprint (مثل only, except).
 */
class RouteRegistrar
{
    protected array $routes = [];

    protected function addRoute(string $method, string $uri, $action, ?string $name = null): self
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'name' => $name, // The route name will be captured from ->name()
        ];
        return $this;
    }

    public function get(string $uri, $action): PendingRoute
    {
        return new PendingRoute('get', $uri, $action, $this->routes);
    }
    public function post(string $uri, $action): PendingRoute
    {
        return new PendingRoute('post', $uri, $action, $this->routes);
    }
    public function put(string $uri, $action): PendingRoute
    {
        return new PendingRoute('put', $uri, $action, $this->routes);
    }
    public function delete(string $uri, $action): PendingRoute
    {
        return new PendingRoute('delete', $uri, $action, $this->routes);
    }
    public function apiResource(string $uri, string $action): PendingRoute
    {
        return new PendingRoute('apiResource', $uri, $action, $this->routes);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
