<?php

use HMsoft\Cms\Http\Controllers\Api\NestedBlogController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => NestedBlogController::class,
    'routes' => function (RouteRegistrar $route) {
        // List associated blogs
        $route->get('/', 'index')->name('index');

        // Create new blog AND attach it
        $route->post('/', 'store')->name('store');

        // Show specific associated blog
        $route->get('/{blog}', 'show')->name('show');

        // Update blog (globally)
        $route->post('/{blog}', 'update')->name('update'); // Using POST for update as per your pattern

        // Detach blog (Remove from list)
        $route->delete('/{blog}', 'destroy')->name('destroy');

        // Bulk Update (Optional)
        $route->post('/update-all', 'updateAll')->name('updateAll');
    },
];
