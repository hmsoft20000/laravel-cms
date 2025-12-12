<?php

use HMsoft\Cms\Http\Controllers\Api\NestedOrganizationController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    'controller' => NestedOrganizationController::class,
    'routes' => function (RouteRegistrar $route) {
        // GET parent/{id}/[type] -> Index (List sponsors/partners)
        $route->get('/', 'index')->name('index');

        // POST parent/{id}/[type] -> Store (Attach organization as [type])
        $route->post('/', 'store')->name('store');

        // DELETE parent/{id}/[type]/{org_id} -> Destroy (Detach)
        $route->delete('/{organization}', 'destroy')->name('destroy');
    },
];