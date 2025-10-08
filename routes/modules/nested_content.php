
<?php

use HMsoft\Cms\Http\Controllers\Api\NestedPostController;
use Illuminate\Support\Facades\Route;

// Get the type of child we are creating/managing from the config
//    (e.g., 'blog', 'service')
$type = $config['options']['type'];

/*
|--------------------------------------------------------------------------
| Nested Content CRUD Routes
|--------------------------------------------------------------------------
|
| These routes are automatically nested under a parent model (the {owner}).
| For example: /portfolios/{owner}/blogs/{childPost}
|
*/
Route::controller(NestedPostController::class)
    ->group(function () use ($type) {

        // GET /portfolios/{owner}/blogs
        Route::get('/', 'index')->name('index')->defaults('type', $type);

        // POST /portfolios/{owner}/blogs
        Route::post('/', 'store')->name('store')->defaults('type', $type);

        // All routes below this will automatically handle scoped binding
        Route::group(['prefix' => '/{childPost}', 'scopeBindings' => true], function () use ($type) {

            // GET /portfolios/{owner}/blogs/{childPost}
            Route::get('/', 'show')->name('show')->defaults('type', $type);

            // PUT /portfolios/{owner}/blogs/{childPost}
            Route::put('/', 'update')->name('update')->defaults('type', $type);

            // DELETE /portfolios/{owner}/blogs/{childPost}
            Route::delete('/', 'destroy')->name('destroy')->defaults('type', $type);
        });
    });
