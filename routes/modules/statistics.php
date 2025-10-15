<?php

use HMsoft\Cms\Http\Controllers\Api\StatisticsController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Define the default controller for this route module.
     * This can be overridden by the ->controller() method in the Blueprint.
     */
    'controller' => StatisticsController::class,

    /**
     * Define the routes for this module.
     * The routes will be automatically wrapped in a group with the correct controller.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{statistics}', 'show')->name('show');
        $registrar->put('/{statistics}', 'update')->name('update');
        $registrar->delete('/{statistics}', 'destroy')->name('destroy');
        $registrar->post('/{statistics}/image', 'updateImage')->name('updateImage');
    }
];
