<?php

use HMsoft\Cms\Http\Controllers\Api\BusinessSettingController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => BusinessSettingController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get("/", 'index')->name('index');
        $registrar->put("/", 'update')->name('update');
        $registrar->get("/schema", 'schema')->name('schema');
    }
];
