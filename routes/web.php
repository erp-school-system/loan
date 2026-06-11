<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/loans', [CustomerController::class, 'index'])->name('loans.index');
    Route::get('/loans/apply', [CustomerController::class, 'create'])->name('loans.create');
    Route::post('/loans', [CustomerController::class, 'store'])->name('loans.store');
    Route::get('/loans/{id}', [CustomerController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loanId}/pay/{repaymentId}', [CustomerController::class, 'payInstallment'])->name('repayments.pay');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminLoanController::class, 'dashboard'])->name('dashboard');
    Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{id}', [AdminLoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{id}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{id}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
});
