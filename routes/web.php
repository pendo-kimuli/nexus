<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapitalAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\ValueDeclarationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/daraja/callback', [CapitalAccessController::class, 'darajaCallback'])->name('daraja.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/value-declarations/create', [ValueDeclarationController::class, 'create'])->name('value-declarations.create');
    Route::post('/value-declarations', [ValueDeclarationController::class, 'store'])->name('value-declarations.store');
    Route::get('/value-declarations', [ValueDeclarationController::class, 'index'])->name('value-declarations.index');
    Route::get('/matches', [ValueDeclarationController::class, 'matches'])->name('matches');

    Route::post('/exchanges', [ExchangeController::class, 'store'])->name('exchanges.store');
    Route::get('/exchanges', [ExchangeController::class, 'index'])->name('exchanges.index');
    Route::get('/exchanges/{exchange}', [ExchangeController::class, 'show'])->name('exchanges.show');
    Route::post('/exchanges/{exchange}/accept', [ExchangeController::class, 'accept'])->name('exchanges.accept');
    Route::post('/exchanges/{exchange}/decline', [ExchangeController::class, 'decline'])->name('exchanges.decline');
    Route::post('/exchanges/{exchange}/dispute', [ExchangeController::class, 'dispute'])->name('exchanges.dispute');
    Route::post('/exchanges/{exchange}/milestones', [ExchangeController::class, 'storeMilestone'])->name('milestones.store');
    Route::post('/milestones/{milestone}/confirm', [ExchangeController::class, 'confirmMilestone'])->name('milestones.confirm');
    Route::get('/trust-profile', [ExchangeController::class, 'trustProfile'])->name('trust-profile');

    Route::get('/capital/apply', [CapitalAccessController::class, 'create'])->name('capital.create');
    Route::post('/capital/apply', [CapitalAccessController::class, 'store'])->name('capital.store');
    Route::get('/admin/capital', [CapitalAccessController::class, 'index'])->name('capital.index');
    Route::post('/admin/capital/{capitalAccess}/approve', [CapitalAccessController::class, 'approve'])->name('capital.approve');
    Route::post('/admin/capital/{capitalAccess}/reject', [CapitalAccessController::class, 'reject'])->name('capital.reject');
    Route::post('/admin/capital/{capitalAccess}/disburse', [CapitalAccessController::class, 'disburse'])->name('capital.disburse');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/toggle', [AdminController::class, 'toggleUserActive'])->name('admin.users.toggle');

    Route::get('/investors', [InvestorController::class, 'index'])->name('investors.index');
    Route::post('/investors/capital/{capitalAccess}/interest', [InvestorController::class, 'expressInterest'])->name('investors.interest');
});