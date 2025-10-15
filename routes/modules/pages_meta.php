<?php

use HMsoft\Cms\Http\Controllers\Api\PagesMetaController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => PagesMetaController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put("/", 'updateAll')->name('updateAll');
        $registrar->get('/{pageMeta}', 'show')->name('show');
        $registrar->put('/{pageMeta}', 'update')->name('update');
        $registrar->delete('/{pageMeta}', 'destroy')->name('destroy');
    }
];
