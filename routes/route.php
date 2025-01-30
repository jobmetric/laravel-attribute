<?php

use Illuminate\Support\Facades\Route;
use JobMetric\Attribute\Http\Controllers\AttributeController;
use JobMetric\Attribute\Http\Controllers\AttributeValueController;
use JobMetric\Panelio\Facades\Middleware;

/*
|--------------------------------------------------------------------------
| Laravel Attribute Routes
|--------------------------------------------------------------------------
|
| All Route in Laravel Attribute package
|
*/

Route::prefix('p/{panel}/{section}')->namespace('JobMetric\Attribute\Http\Controllers')->group(function () {
    Route::middleware(Middleware::getMiddlewares())->group(function () {
        // attribute value
        Route::prefix('attributes/{attribute}')->name('attributes_')->group(function(){
            Route::post('values/set-translation', [AttributeValueController::class, 'setTranslation'])->name('values.set-translation');
            Route::options('values', [AttributeValueController::class, 'options'])->name('values.options');
            Route::resource('values', AttributeValueController::class)->except(['show', 'destroy'])->parameters([
                'attribute', 'jm_attribute:id',
                'value' => 'jm_attribute_value:id'
            ]);
        });

        // attribute
        Route::post('attributes/set-translation', [AttributeController::class, 'setTranslation'])->name('attributes.set-translation');
        Route::options('attributes', [AttributeController::class, 'options'])->name('attributes.options');
        Route::resource('attributes', AttributeController::class)->except(['show', 'destroy'])->parameters([
            'attribute', 'jm_attribute:id'
        ]);
    });
});
