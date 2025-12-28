<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/api/v1/budgets', [BudgetController::class, 'data'])->name('budgets.data');
Route::get('/api/v1/saldos', [SaldoController::class, 'data'])->name('saldos.data');
Route::get('/api/v1/saldos/category/{id}', [SaldoController::class, 'getByCategoryId'])->name('saldos.byCategory');
Route::get('/api/v1/transactions', [TransactionsController::class, 'data'])->name('transactions.data');

Route::resource('budgets', BudgetController::class);
Route::resource('saldos', SaldoController::class);  
Route::resource('transactions', TransactionsController::class);
Route::resource('categories', CategoryController::class);