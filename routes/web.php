<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laraforge\DatabaseStudio\Http\Controllers\DatabaseStudioWebController;

Route::get('/', [DatabaseStudioWebController::class, 'index'])->name('index');
Route::get('/create', [DatabaseStudioWebController::class, 'create'])->name('create');
Route::get('/console', [DatabaseStudioWebController::class, 'console'])->name('console');
Route::get('/manage/{table}', [DatabaseStudioWebController::class, 'manage'])->name('manage');
