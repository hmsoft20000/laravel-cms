<?php

namespace HMsoft\Cms\Support;

use HMsoft\Cms\Cms;
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
        $originalClass = static::class;

        // --- التغيير الرئيسي هنا ---
        // استبدال قراءة ملف الإعدادات بالاستعلام من الكلاس المركزي
        $extendedClass = Cms::getExtendedFor($originalClass);

        if ($extendedClass && is_subclass_of($extendedClass, $originalClass)) {
            return app($extendedClass);
        }

        return app($originalClass);
    }

    /**
     * Ensure relationships automatically use extended models.
     */
    public function newRelatedInstance($class)
    {
        // --- التغيير الرئيسي هنا ---
        $extendedClass = Cms::getExtendedFor($class);

        if ($extendedClass && is_subclass_of($extendedClass, $class)) {
            $class = $extendedClass;
        }

        return app($class);
    }
}
