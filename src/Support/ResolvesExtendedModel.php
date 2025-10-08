<?php

namespace HMsoft\Cms\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait ResolvesExtendedModel
{
    /**
     * When calling static methods like Category::query(),
     * this ensures the correct (possibly extended) model is used.
     */
    public static function query(): Builder
    {
        return static::resolveExtendedInstance()->newQuery();
    }

    /**
     * Handle static method calls like Category::ofType(), Category::where(), etc.
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = static::resolveExtendedInstance();

        if (method_exists($instance, $method)) {
            return $instance->$method(...$parameters);
        }

        return $instance->$method(...$parameters);
    }

    /**
     * Resolve to developer’s extended model if registered.
     */
    protected static function resolveExtendedInstance(): Model
    {
        $class = static::class;
        $extendedModels = config('cms.extended_models', []);

        if (isset($extendedModels[$class])) {
            $extended = $extendedModels[$class];

            if (is_subclass_of($extended, $class)) {
                return app($extended);
            }
        }

        return app($class);
    }

    /**
     * Ensure relationships automatically use extended models.
     */
    public function newRelatedInstance($class)
    {
        $extendedModels = config('cms.extended_models', []);

        if (isset($extendedModels[$class]) && is_subclass_of($extendedModels[$class], $class)) {
            $class = $extendedModels[$class];
        }

        return app($class);
    }
}
