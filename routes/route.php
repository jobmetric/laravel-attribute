<?php

use Illuminate\Support\Facades\Route;
use JobMetric\Attribute\Http\Controllers\AttributeController;
use JobMetric\Panelio\Facades\Middleware;

/*
|--------------------------------------------------------------------------
| Laravel Attribute Routes
|--------------------------------------------------------------------------
|
| All Route in Laravel Attribute package
|
*/

// Attribute
Route::prefix('p/{panel}/{section}')->namespace('JobMetric\Attribute\Http\Controllers')->group(function () {
    Route::middleware(Middleware::getMiddlewares())->group(function () {
        Route::post('attribute/set-translation', [AttributeController::class, 'setTranslation'])->name('attribute.set-translation');
        Route::options('attribute', [AttributeController::class, 'options'])->name('attribute.options');
        Route::resource('attribute', AttributeController::class)->except(['show', 'destroy'])->parameter('attribute', 'jm_attribute:id');
    });
});
