<?php

use HMsoft\Cms\Http\Controllers\Api\ServiceController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Define the default controller for this route module.
     * This can be overridden by the ->controller() method in the Blueprint.
     */
    'controller' => ServiceController::class,

    /**
     * Define the routes for this module.
     * The routes will be automatically wrapped in a group with the correct controller.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{service}', 'show')->name('show');
        $registrar->post('/{service}', 'update')->name('update');
        $registrar->delete('/{service}', 'destroy')->name('destroy');
    }
];
