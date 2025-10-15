<?php

use HMsoft\Cms\Http\Controllers\Api\LegalsMediaController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for media attached to singleton legal pages.
     */
    'controller' => LegalsMediaController::class,

    /**
     * Routes for media attached to a legal page (e.g., 'about-us').
     * The group prefix (e.g., '/legals/aboutUs') is applied by the CmsRouteManager,
     * so these routes are relative to that.
     */
    'routes' => function (RouteRegistrar $registrar, array $config) {
        // We need the 'type' to associate the media with the correct legal page.
        $type = $config['options']['type'];

        $registrar->get('/media', 'index')->name('index')->defaults('type', $type);
        $registrar->post('/media', 'store')->name('store')->defaults('type', $type);
        $registrar->put('/media/update-all', 'updateAll')->name('updateAll')->defaults('type', $type);
        $registrar->get('/media/{medium}', 'show')->name('show')->defaults('type', $type);
        $registrar->put('/media/reorder', 'reorder')->name('reorder')->defaults('type', $type);
        $registrar->put('/media/{medium}', 'update')->name('update')->defaults('type', $type);
        $registrar->delete('/media/{medium}', 'destroy')->name('destroy')->defaults('type', $type);
    }
];
