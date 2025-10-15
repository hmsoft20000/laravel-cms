<?php

use HMsoft\Cms\Http\Controllers\Api\LanguageController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => LanguageController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{lang}', 'show')->name('show');
        $registrar->put('/{lang}', 'update')->name('update');
        $registrar->delete('/{lang}', 'destroy')->name('destroy');
    }
];
