<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::get('/admin/kandang', [AdminController::class, 'kandang'])->name('admin.kandang');
    Route::get('/admin/karyawan', [AdminController::class, 'karyawan'])->name('admin.karyawan');
    Route::get('/admin/pembesaran', [AdminController::class, 'pembesaran'])->name('admin.pembesaran');
    Route::get('/admin/penetasan', [AdminController::class, 'penetasan'])->name('admin.penetasan');
    Route::get('/admin/produksi', [AdminController::class, 'produksi'])->name('admin.produksi');
});

require __DIR__.'/auth.php';

// Custom access path for admin login
Route::get('/mimin', [AuthenticatedSessionController::class, 'create'])->name('mimin.login');
Route::post('/mimin', [AuthenticatedSessionController::class, 'store'])->name('mimin.store');
