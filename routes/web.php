<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laraforge\DatabaseStudio\Http\Controllers\DatabaseStudioWebController;
use Laraforge\DatabaseStudio\Http\Middleware\DatabaseStudioAuthMiddleware;

// Guest Security Login Routes
Route::get('/login', [DatabaseStudioWebController::class, 'showLogin'])->name('login');
Route::post('/login', [DatabaseStudioWebController::class, 'login'])->name('login.post');
Route::get('/logout', [DatabaseStudioWebController::class, 'logout'])->name('logout');

// Protected Dashboard Routes
Route::middleware([DatabaseStudioAuthMiddleware::class])->group(function () {
    Route::get('/', [DatabaseStudioWebController::class, 'index'])->name('index');
    Route::get('/create', [DatabaseStudioWebController::class, 'create'])->name('create');
    Route::get('/console', [DatabaseStudioWebController::class, 'console'])->name('console');
    Route::get('/manage/{table}', [DatabaseStudioWebController::class, 'manage'])->name('manage');
});
