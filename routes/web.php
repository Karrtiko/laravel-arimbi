<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FrontendController;

// Frontend routes
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/shop', [FrontendController::class, 'shop'])->name('shop');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/product/{slug}', [FrontendController::class, 'product'])->name('product.show');
Route::get('/package/{slug}', [FrontendController::class, 'package'])->name('package.show');

// Invoice routes
Route::get('/invoice/{order}', [InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/invoices/bulk', [InvoiceController::class, 'bulk'])->name('invoice.bulk');

// Watermark image serving - DISABLED temporarily due to routing conflict
// TODO: Re-enable after fixing route pattern to be more specific
// Route::get('/storage/watermarked/{path}', [App\Http\Controllers\Admin\WatermarkController::class, 'serve'])
//     ->where('path', '.*')
//     ->name('watermark.serve');

// Admin watermark management routes
Route::middleware(['auth'])->prefix('admin')->name('watermark.')->group(function () {
    Route::get('/watermark', [App\Http\Controllers\Admin\WatermarkController::class, 'index'])->name('index');
    Route::post('/watermark', [App\Http\Controllers\Admin\WatermarkController::class, 'update'])->name('update');
    Route::post('/watermark/clear', [App\Http\Controllers\Admin\WatermarkController::class, 'clearCache'])->name('clear');
    Route::post('/watermark/cleanup', [App\Http\Controllers\Admin\WatermarkController::class, 'cleanupUnused'])->name('cleanup');
});
