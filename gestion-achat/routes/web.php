<?php

use App\Http\Controllers\PurchasesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('purchases')->group(function () {
    Route::get('/import', [PurchasesController::class, 'importForm'])->name('purchases.import.form');
    Route::post('/import', [PurchasesController::class, 'import'])->name('purchases.import');
    Route::post('/export', [PurchasesController::class, 'export'])->name('purchases.export');
    Route::post('/api/sync', 'PurchasesController@sync')->name('api.sync');
});
