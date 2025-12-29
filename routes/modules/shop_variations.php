<?php

use HMsoft\Cms\Http\Controllers\Api\Shop\ItemVariationController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => ItemVariationController::class,
    'routes' => function (RouteRegistrar $registrar) {
        // المسار سيكون: api/items/{item}/variations

        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{variation}', 'show')->name('show');
        $registrar->put('/{variation}', 'update')->name('update');
        $registrar->delete('/{variation}', 'destroy')->name('destroy');
    }
];
