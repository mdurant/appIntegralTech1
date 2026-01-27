<?php

use App\Http\Controllers\Api\ServiceCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::get('/service-categories/search', [ServiceCategoryController::class, 'search'])
        ->name('api.service-categories.search');
});
