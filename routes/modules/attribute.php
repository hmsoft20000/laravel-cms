<?php

use HMsoft\Cms\Http\Controllers\Api\AttributeController;
use HMsoft\Cms\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;

return [
    /**
     * The controller for attribute-related actions.
     */
    'controller' => AttributeController::class,

    /**
     * Routes for this module.
     * The prefix (e.g., 'blog-attributes') and name prefix are now applied by the CmsRouteManager.
     */
    'routes' => function (RouteRegistrar $registrar, array $config) {
        // The generic AttributeController needs to know which 'scope' (type) of attribute
        // it is working with (e.g., 'blog', 'portfolio').
        $type = $config['options']['type'];

        $registrar->get('/', 'index')->name('index')->defaults('scope', $type);
        $registrar->post('/', 'store')->name('store')->defaults('scope', $type);
        $registrar->get('/{attribute}', 'show')->name('show')->defaults('scope', $type);
        $registrar->put('/{attribute}', 'update')->name('update')->defaults('scope', $type);
        $registrar->post('/updateAll', 'updateAll')->name('updateAll')->defaults('scope', $type);
        $registrar->delete('/{attribute}', 'destroy')->name('destroy')->defaults('scope', $type);
        $registrar->post('/{attribute}/image', 'updateImage')->name('updateImage')->defaults('scope', $type);
    }
];
