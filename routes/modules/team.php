<?php

use HMsoft\Cms\Http\Controllers\Api\TeamController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => TeamController::class,
    'routes' => function (RouteRegistrar $registrar     ) {
        $registrar->post('/{team}/image', 'updateImage')->name('updateImage');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/', 'index')->name('index');
        $registrar->get('/{team}', 'show')->name('show');
        $registrar->put('/{team}', 'update')->name('update');
        $registrar->delete('/{team}', 'destroy')->name('destroy');
    }
];
