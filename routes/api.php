<?php

use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\V1\OpportunityController;
use App\Http\Controllers\Api\V1\QuoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::get('/service-categories/search', [ServiceCategoryController::class, 'search'])
        ->name('api.service-categories.search');

    Route::prefix('v1')->middleware(['auth:sanctum', 'api.access', 'throttle:api:v1'])->group(function () {
        Route::get('/opportunities', [OpportunityController::class, 'index'])->name('api.v1.opportunities.index');
        Route::get('/quotes', [QuoteController::class, 'index'])->name('api.v1.quotes.index');
        Route::post('/quotes', [QuoteController::class, 'store'])->name('api.v1.quotes.store');
    });
});
