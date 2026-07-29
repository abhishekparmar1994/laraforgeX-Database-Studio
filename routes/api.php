<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laraforge\DatabaseStudio\Http\Controllers\DatabaseManagerController;

Route::get('/', [DatabaseManagerController::class, 'index'])->name('index');
Route::post('/create', [DatabaseManagerController::class, 'store'])->name('store');
Route::get('/{table}', [DatabaseManagerController::class, 'show'])->name('show');
Route::get('/{table}/data', [DatabaseManagerController::class, 'data'])->name('data');
Route::get('/{table}/export', [DatabaseManagerController::class, 'exportData'])->name('export');
Route::post('/{table}/indexes', [DatabaseManagerController::class, 'addIndex'])->name('indexes.store');
Route::delete('/{table}/indexes/{indexName}', [DatabaseManagerController::class, 'dropIndex'])->name('indexes.destroy');
Route::post('/bulk-action', [DatabaseManagerController::class, 'bulkAction'])->name('bulk-action');
Route::post('/{table}/drop-columns', [DatabaseManagerController::class, 'dropColumns'])->name('columns.drop');
Route::put('/{table}/columns/{column}', [DatabaseManagerController::class, 'modifyColumn'])->name('columns.modify');
Route::post('/execute-sql', [DatabaseManagerController::class, 'executeSql'])->name('execute-sql');
Route::post('/{table}/truncate', [DatabaseManagerController::class, 'truncate'])->name('truncate');
Route::delete('/{table}', [DatabaseManagerController::class, 'destroy'])->name('destroy');
