<?php

use HMsoft\Cms\Http\Controllers\Api\OrganizationController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => OrganizationController::class,

    /**
     * Routes for this module.
     * The prefix ('sponsors' or 'partners') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{organization}', 'show')->name('show');
        $registrar->put('/{organization}', 'update')->name('update');
        $registrar->delete('/{organization}', 'destroy')->name('destroy');
        $registrar->post('/{organization}/image', 'updateImage')->name('updateImage');
    }
];
