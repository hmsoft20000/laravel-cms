<?php

use HMsoft\Cms\Http\Controllers\Api\PermissionController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => PermissionController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{permission}', 'show')->name('show');
        $registrar->put('/{permission}', 'update')->name('update');
        $registrar->delete('/{permission}', 'destroy')->name('destroy');
        $registrar->get('/modules/list', 'getModules')->name('modules');
        $registrar->get('/modules/grouped', 'getByModule')->name('grouped');
        $registrar->post('/bulk-assign-role', 'bulkAssignToRole')->name('bulk-assign-role');
    }
];
