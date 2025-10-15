<?php

use HMsoft\Cms\Http\Controllers\Api\OurValueController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => OurValueController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{ourValue}', 'show')->name('show');
        $registrar->put('/{ourValue}', 'update')->name('update');
        $registrar->delete('/{ourValue}', 'destroy')->name('destroy');
        $registrar->post('/{ourValue}/image', 'updateImage')->name('updateImage');
    }
];
