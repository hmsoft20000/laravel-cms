<?php

use HMsoft\Cms\Http\Controllers\Api\Shop\ItemAddonController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => ItemAddonController::class,
    'routes' => function (RouteRegistrar $registrar) {
        // سيتم دمج هذا الملف تحت بادئة: items/{item}/addons

        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{addon}', 'show')->name('show');
        $registrar->put('/{addon}', 'update')->name('update');
        $registrar->delete('/{addon}', 'destroy')->name('destroy');

        // مسار إضافي لترتيب العناصر إذا احتجت له مستقبلاً
        $registrar->put('/reorder', 'reorder')->name('reorder');
    }
];
