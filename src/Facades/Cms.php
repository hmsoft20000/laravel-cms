<?php

namespace HMsoft\Cms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void extend(\Closure $callback)
 * @method static string|null getExtendedFor(string $original)
 */
class Cms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \HMsoft\Cms\Cms::class;
    }
}
