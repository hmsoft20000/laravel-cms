<?php

namespace HMsoft\Cms\Services;

use Illuminate\Support\Facades\Route;
use Closure;

class BindingService
{
    public static array $resolvers = [];

    /**
     * Register a custom resolver logic for a route parameter.
     *
     * @param string $parameter The route parameter name (e.g., 'post', 'category').
     * @param Closure $callback The function that contains the logic to find the model.
     */
    public static function resolver(string $parameter, Closure|null $callback): void
    {
        static::$resolvers[$parameter] = $callback;
    }

    /**
     * Apply all registered resolvers as Route Model Bindings.
     * This should be called from a Service Provider's boot method.
     */
    public static function boot(): void
    {
        foreach (static::$resolvers as $parameter => $callback) {
            Route::bind($parameter, $callback);
        }
    }
}
