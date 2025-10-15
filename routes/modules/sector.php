<?php

use HMsoft\Cms\Http\Controllers\Api\SectorController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => SectorController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->post('/{sector}/image', 'updateImage')->name('updateImage');
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{sector}', 'show')->name('show');
        $registrar->put('/{sector}', 'update')->name('update');
        $registrar->delete('/{sector}', 'destroy')->name('destroy');
    }
];
