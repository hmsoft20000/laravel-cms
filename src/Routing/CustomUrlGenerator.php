<?php

namespace HMsoft\Cms\Routing;

use Illuminate\Routing\UrlGenerator;

class CustomUrlGenerator extends UrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        $route = $this->routes->getByName($name);
        if (!$route) {
            info([
                'name' => $name,
                'parameters' => $parameters
            ]);
            // abort(404);
        }
        $defaultLocale = config('app.fallback_locale', 'en');
        $allowedLocales = array_keys(config('cms.locales', []));

        // إذا $parameters مش مصفوفة مفتاحية، حوّلها
        if (!is_array($parameters)) {
            $parameters = [];
        } elseif (array_values($parameters) === $parameters) {
            // مصفوفة indexed => حولها لمصفوفة key => value
            $temp = [];
            foreach ($route->parameterNames() as $index => $key) {
                if (isset($parameters[$index])) {
                    $temp[$key] = $parameters[$index];
                }
            }
            $parameters = $temp;
        }

        // تعامل مع locale
        if ($route && in_array('locale', $route->parameterNames())) {
            if (!isset($parameters['locale']) || !in_array($parameters['locale'], $allowedLocales)) {
                $parameters['locale'] = app()->getLocale() ?: $defaultLocale;
            }
        }
        $route = parent::route($name, $parameters, $absolute);
        return $route;
    }
}
