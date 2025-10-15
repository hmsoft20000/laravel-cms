<?php

use HMsoft\Cms\Http\Controllers\Api\TestimonialController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => TestimonialController::class,
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->post('/{testimonial}/image', 'updateImage')->name('updateImage');
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->get('/{testimonial}', 'show')->name('show');
        $registrar->put('/{testimonial}', 'update')->name('update');
        $registrar->delete('/{testimonial}', 'destroy')->name('destroy');
    }
];
