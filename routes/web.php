<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root URL based on auth status and role
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (! $user->is_active) {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('error', 'Your account is inactive.');
    }

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('employee.dashboard');
});

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'auth::login')->name('login');
});

// Authenticated Logout Route
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Protected Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::livewire('/dashboard', 'admin::dashboard')->name('dashboard');
    Route::livewire('/employees', 'admin::employee-management')->name('employees');
    Route::livewire('/departments', 'admin::department-management')->name('departments');
});

// Protected Employee Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->as('employee.')->group(function () {
    Route::livewire('/dashboard', 'employee::dashboard')->name('dashboard');
    Route::livewire('/daily-task-reports', 'employee::daily-task-report-list')->name('daily-task-report-list');
    Route::livewire('/daily-task-reports/create', 'employee::daily-task-report-create')->name('daily-task-report-create');
    Route::livewire('/daily-task-reports/{report}/edit', 'employee::daily-task-report-create')->name('daily-task-report-edit');
    Route::livewire('/settings', 'employee::setting')->name('settings');
});
