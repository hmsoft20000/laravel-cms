<?php

use HMsoft\Cms\Http\Controllers\Api\LegalsController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * The controller for legal page actions.
     */
    'controller' => LegalsController::class,

    /**
     * The routes for a single legal page.
     * It uses the 'type' option passed by the CmsRouteManager to identify the page.
     */
    'routes' => function (RouteRegistrar $registrar, array $config) {
        // We still need the 'type' to tell the controller which legal page to fetch.
        // CmsRouteManager makes the original $config array available for such cases.
        $type = $config['options']['type'];
        $registrar->get('/', 'index')->name('index')->defaults('type', $type);
        $registrar->put('/', 'update')->name('update')->defaults('type', 'index');
    }
];
