<?php

use HMsoft\Cms\Http\Controllers\Api\Shop\ItemController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Define the default controller for this route module.
     */
    'controller' => ItemController::class,

    /**
     * Define the routes for this module.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{item}', 'show')->name('show');
        $registrar->post('/{item}', 'update')->name('update');
        $registrar->delete('/{item}', 'destroy')->name('destroy');

        //  <-- هذا هو المسار المخصص الذي أضفته -->
        $registrar->post('/{item}/attach-downloads', 'attachDownloads')->name('attachDownloads');
    }
];
