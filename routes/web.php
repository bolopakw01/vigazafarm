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

    // Admin Dashboard (All authenticated users)
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Master routes (Owner only)
    Route::middleware('owner')->group(function () {
        Route::get('/admin/kandang', [AdminController::class, 'kandang'])->name('admin.kandang');
        Route::get('/admin/karyawan', [AdminController::class, 'karyawan'])->name('admin.karyawan');
    });

    // Operational routes (All authenticated users)
    
    // Pembesaran routes
    Route::get('/admin/pembesaran', [App\Http\Controllers\PembesaranController::class, 'index'])->name('admin.pembesaran');
    Route::get('/admin/pembesaran/create', [App\Http\Controllers\PembesaranController::class, 'create'])->name('admin.pembesaran.create');
    Route::post('/admin/pembesaran', [App\Http\Controllers\PembesaranController::class, 'store'])->name('admin.pembesaran.store');
    Route::get('/admin/pembesaran/from-penetasan/{penetasan}', [App\Http\Controllers\PembesaranController::class, 'createFromPenetasan'])->name('admin.pembesaran.createFromPenetasan');
    Route::post('/admin/pembesaran/from-penetasan/{penetasan}', [App\Http\Controllers\PembesaranController::class, 'storeFromPenetasan'])->name('admin.pembesaran.storeFromPenetasan');
    Route::get('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'show'])->name('admin.pembesaran.show');
    Route::get('/admin/pembesaran/{pembesaran}/edit', [App\Http\Controllers\PembesaranController::class, 'edit'])->name('admin.pembesaran.edit');
    Route::patch('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'update'])->name('admin.pembesaran.update');
    Route::delete('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'destroy'])->name('admin.pembesaran.destroy');
    
    // Pembesaran Recording routes (API-like for AJAX)
    Route::prefix('admin/pembesaran/{pembesaran}')->name('admin.pembesaran.recording.')->group(function () {
        // Pakan
        Route::post('/pakan', [App\Http\Controllers\PembesaranRecordingController::class, 'storePakan'])->name('pakan');
        Route::get('/pakan/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getPakanList'])->name('pakan.list');
        Route::patch('/pakan/{pakan}', [App\Http\Controllers\PembesaranRecordingController::class, 'updatePakan'])->name('pakan.update');
        Route::delete('/pakan/{pakan}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyPakan'])->name('pakan.destroy');
        
        // Kematian
        Route::post('/kematian', [App\Http\Controllers\PembesaranRecordingController::class, 'storeKematian'])->name('kematian');
        Route::get('/kematian/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getKematianList'])->name('kematian.list');
        Route::patch('/kematian/{kematian}', [App\Http\Controllers\PembesaranRecordingController::class, 'updateKematian'])->name('kematian.update');
        Route::delete('/kematian/{kematian}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyKematian'])->name('kematian.destroy');
        
        // Laporan Harian
        Route::post('/laporan-harian', [App\Http\Controllers\PembesaranRecordingController::class, 'generateLaporanHarian'])->name('laporan');
        Route::get('/laporan-harian/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getLaporanHarianList'])->name('laporan.list');
        
        // Monitoring Lingkungan
        Route::post('/monitoring', [App\Http\Controllers\PembesaranRecordingController::class, 'storeMonitoring'])->name('lingkungan');
        Route::get('/monitoring/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getMonitoringList'])->name('lingkungan.list');
        
        // Kesehatan
        Route::post('/kesehatan', [App\Http\Controllers\PembesaranRecordingController::class, 'storeKesehatan'])->name('kesehatan');
        Route::get('/kesehatan/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getKesehatanList'])->name('kesehatan.list');
        
        // Update Berat
        Route::post('/berat', [App\Http\Controllers\PembesaranRecordingController::class, 'storeBeratRataRata'])->name('berat');
    });
    
    // Penetasan routes
    Route::get('/admin/penetasan', [AdminController::class, 'penetasan'])->name('admin.penetasan');
    // resource-like routes for penetasan actions used by the UI
    Route::get('/admin/penetasan/create', [App\Http\Controllers\PenetasanController::class, 'create'])->name('admin.penetasan.create');
    Route::post('/admin/penetasan', [App\Http\Controllers\PenetasanController::class, 'store'])->name('admin.penetasan.store');
    Route::get('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'show'])->name('admin.penetasan.show');
    Route::get('/admin/penetasan/{penetasan}/edit', [App\Http\Controllers\PenetasanController::class, 'edit'])->name('admin.penetasan.edit');
    Route::patch('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'update'])->name('admin.penetasan.update');
    Route::patch('/admin/penetasan/{penetasan}/status', [App\Http\Controllers\PenetasanController::class, 'updateStatus'])->name('admin.penetasan.updateStatus');
    Route::delete('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'destroy'])->name('admin.penetasan.destroy');
    Route::get('/admin/produksi', [AdminController::class, 'produksi'])->name('admin.produksi');
});

require __DIR__.'/auth.php';

// Custom access path for admin login
Route::get('/mimin', [AuthenticatedSessionController::class, 'create'])->name('mimin.login');
Route::post('/mimin', [AuthenticatedSessionController::class, 'store'])->name('mimin.store');
