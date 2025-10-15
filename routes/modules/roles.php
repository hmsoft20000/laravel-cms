<?php

use HMsoft\Cms\Http\Controllers\Api\RoleController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => RoleController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/tree', 'tree')->name('tree');
        $registrar->get('/{role}', 'show')->name('show');
        $registrar->put('/{role}', 'update')->name('update');
        $registrar->delete('/{role}', 'destroy')->name('destroy');
        $registrar->post('/{role}/permissions', 'assignPermissions')->name('assign-permissions');
        $registrar->delete('/{role}/permissions', 'removePermissions')->name('remove-permissions');
        $registrar->get('/available-parents/{role?}', 'availableParents')->name('available-parents');
    }
];
