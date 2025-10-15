<?php

use HMsoft\Cms\Http\Controllers\Api\FaqController;
use HMsoft\Cms\Routing\RouteRegistrar;

return [
    /**
     * Controller for this module's routes.
     */
    'controller' => FaqController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'portfolios/{owner}/faqs') is applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar) {
        $registrar->get('/', 'index')->name('index');
        $registrar->post('/', 'store')->name('store');
        $registrar->put('/update-all', 'updateAll')->name('updateAll');
        $registrar->get('/{faq}', 'show')->name('show');
        $registrar->put('/{faq}', 'update')->name('update');
        $registrar->delete('/{faq}', 'destroy')->name('destroy');
    }
];
