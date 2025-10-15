<?php

use HMsoft\Cms\Http\Controllers\Api\NestedBlogController;
use HMsoft\Cms\Routing\RouteRegistrar;

/*
|--------------------------------------------------------------------------
| Nested Blog CRUD Routes
|--------------------------------------------------------------------------
|
| The {blog} parameter will be automatically resolved to the Blog model
| thanks to the type-hinting in the NestedBlogController methods.
|
*/

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => NestedBlogController::class,

    /**
     * Routes for this module.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{blog}', 'show')->name('show');
        $registrar->put('/{blog}', 'update')->name('update');
        $registrar->delete('/{blog}', 'destroy')->name('destroy');
    }
];
