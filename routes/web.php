<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReminderReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/imports', [CompanyImportController::class, 'index'])->name('imports.index');
    Route::post('/imports', [CompanyImportController::class, 'store'])->name('imports.store');

    Route::resource('companies', CompanyController::class)->except(['show']);

    Route::get('/reports/reminders', [ReminderReportController::class, 'index'])->name('reports.reminders.index');
    Route::post('/reports/reminders/run', [ReminderReportController::class, 'run'])->name('reports.reminders.run');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
