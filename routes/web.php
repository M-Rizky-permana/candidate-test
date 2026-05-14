<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierImportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
        return redirect()->route('suppliers.index');

});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('suppliers', SupplierController::class);

Route::resource('suppliers.layups', CltLayupController::class)
    ->shallow();

Route::resource('layups.layers', CltLayerController::class)
    ->shallow();

Route::get('/suppliers/{supplier}/export', [SupplierImportExportController::class, 'export'])
    ->name('suppliers.export');

Route::get('/suppliers/{supplier}/import', [SupplierImportExportController::class, 'importForm'])
    ->name('suppliers.import.form');

Route::post('/suppliers/{supplier}/import', [SupplierImportExportController::class, 'import'])
    ->name('suppliers.import');


require __DIR__.'/auth.php';
