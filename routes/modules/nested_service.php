<?php

use HMsoft\Cms\Http\Controllers\Api\NestedServiceController;
use HMsoft\Cms\Routing\RouteRegistrar;

/*
|--------------------------------------------------------------------------
| Nested Service CRUD Routes
|--------------------------------------------------------------------------
*/

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => NestedServiceController::class,

    /**
     * Routes for this module.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{service}', 'show')->name('show');
        $registrar->put('/{service}', 'update')->name('update');
        $registrar->delete('/{service}', 'destroy')->name('destroy');
    }
];
