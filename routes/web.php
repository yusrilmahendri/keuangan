<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// API Routes
Route::get('/api/v1/saldos', [SaldoController::class, 'data'])->name('saldos.data');
Route::get('/api/v1/saldos/category/{id}', [SaldoController::class, 'getByCategoryId'])->name('saldos.byCategory');
Route::get('/api/v1/saldos/filter', [SaldoController::class, 'getFilteredSaldo'])->name('saldos.filter');
Route::get('/api/v1/transactions', [TransactionsController::class, 'data'])->name('transactions.data');
Route::get('/api/v1/categories', [CategoryController::class, 'data'])->name('categories.data');
Route::get('/api/dashboard/summary', [DashboardController::class, 'filterSummary']);

// Export Routes
Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
Route::get('/saldos/export/excel', [SaldoController::class, 'exportExcel'])->name('saldos.export.excel');
Route::get('/saldos/export/pdf', [SaldoController::class, 'exportPdf'])->name('saldos.export.pdf');
Route::get('/transactions/export/excel', [TransactionsController::class, 'exportExcel'])->name('transactions.export.excel');
Route::get('/transactions/export/pdf', [TransactionsController::class, 'exportPdf'])->name('transactions.export.pdf');

Route::resource('saldos', SaldoController::class);
Route::resource('transactions', TransactionsController::class);
Route::resource('categories', CategoryController::class);
