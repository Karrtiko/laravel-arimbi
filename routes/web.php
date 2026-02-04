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
