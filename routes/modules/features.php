<?php

use HMsoft\Cms\Http\Controllers\Api\FeatureController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => FeatureController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'portfolios/{owner}/features') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->post('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{feature}', 'show')->name('show');
        $registrar->put('/{feature}', 'update')->name('update');
        $registrar->delete('/{feature}', 'destroy')->name('destroy');
        $registrar->post('/{feature}/image', 'updateImage')->name('updateImage');
    }
];
