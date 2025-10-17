<?php

use HMsoft\Cms\Http\Controllers\Api\CategoryController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * The controller for category-related actions.
     */
    'controller' => CategoryController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'blog-categories') and name prefix (e.g., 'api.blogs.categories.')
     * are now applied by the CmsRouteManager for consistency.
     */
    'routes' => function (RouteRegistrar $registrar, array $config) {


        // The generic CategoryController still needs to know which 'type' of category
        // it is working with (e.g., 'blog', 'portfolio').
        $type = $config['options']['type'];

        $registrar->get('/', 'index')->name('index')->defaults('type', $type);
        $registrar->post('/', 'store')->name('store')->defaults('type', $type);
        $registrar->get('/{category}', 'show')->name('show')->defaults('type', $type);
        $registrar->put('/{category}', 'update')->name('update')->defaults('type', $type);
        $registrar->post('/updateAll', 'updateAll')->name('updateAll')->defaults('type', $type);
        $registrar->delete('/{category}', 'destroy')->name('destroy')->defaults('type', $type);
        $registrar->post('/{category}/image', 'updateImage')->name('updateImage')->defaults('type', $type);
    }
];
