<?php

use Illuminate\Support\Facades\Route;
use Shakewell\Litecard\Http\Controllers\CardController;
use Shakewell\Litecard\Http\Controllers\TemplateController;

/*
|--------------------------------------------------------------------------
| LiteCard API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded automatically if you want to use the default
| LiteCard API endpoints. You can disable this by not publishing the routes
| or by creating your own custom routes.
|
*/

Route::middleware(['api'])->prefix('api/v1')->group(function () {
    
    // LiteCard Authentication Routes
    Route::post('litecard/auth/login', [CardController::class, 'login'])
        ->name('litecard.login');
    
    // Protected LiteCard Routes
    Route::middleware(['auth:sanctum'])->prefix('litecard')->group(function () {
        
        // Authentication validation
        Route::post('auth/validate', [CardController::class, 'validateToken'])
            ->name('litecard.validate');
        
        // Template management
        Route::get('templates', [TemplateController::class, 'index'])
            ->name('litecard.templates.index');
        
        Route::get('templates/{template}', [TemplateController::class, 'show'])
            ->name('litecard.templates.show');
        
        // Card management
        Route::post('cards', [CardController::class, 'store'])
            ->name('litecard.cards.store');
        
        Route::get('cards/{card}', [CardController::class, 'show'])
            ->name('litecard.cards.show');
        
        Route::post('cards/{card}/status', [CardController::class, 'updateStatus'])
            ->name('litecard.cards.status');
    });
});