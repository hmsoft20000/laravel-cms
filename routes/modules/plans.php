<?php

use HMsoft\Cms\Http\Controllers\Api\PlanController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => PlanController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'portfolios/{owner}/plans') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{plan}', 'show')->name('show');
        $registrar->put('/{plan}', 'update')->name('update');
        $registrar->delete('/{plan}', 'destroy')->name('destroy');
    }
];
