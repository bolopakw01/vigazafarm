<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/test-produksi', function () {
    $kandangList = \App\Models\Kandang::whereIn('status', ['aktif', 'maintenance'])
                              ->orderBy('nama_kandang')
                              ->get();
    
    // Get penetasan with available infertile eggs and load kandang relation
    // Only get completed penetasan with available infertile eggs
    $penetasanList = \App\Models\Penetasan::with('kandang')
                                  ->where('status', 'selesai')
                                  ->whereRaw('(telur_tidak_fertil - COALESCE(telur_infertil_ditransfer, 0)) > 0')
                                  ->orderBy('tanggal_menetas', 'desc')
                                  ->get();
    
    // Get pembesaran with available breeding stock and load kandang relation
    // Only get completed pembesaran with available stock
    $pembesaranList = \App\Models\Pembesaran::with('kandang')
                                    ->where('status_batch', 'selesai')
                                    ->whereRaw('(COALESCE(jumlah_siap, 0) - COALESCE(indukan_ditransfer, 0)) > 0')
                                    ->orderBy('tanggal_siap', 'desc')
                                    ->get();
    
    return view('admin.pages.produksi.create-produksi', compact('kandangList', 'penetasanList', 'pembesaranList'));
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
        // Kandang CRUD
        Route::get('/admin/kandang', [App\Http\Controllers\KandangController::class, 'index'])->name('admin.kandang');
        Route::get('/admin/kandang/create', [App\Http\Controllers\KandangController::class, 'create'])->name('admin.kandang.create');
        Route::post('/admin/kandang', [App\Http\Controllers\KandangController::class, 'store'])->name('admin.kandang.store');
        Route::get('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'show'])->name('admin.kandang.show');
        Route::get('/admin/kandang/{kandang}/edit', [App\Http\Controllers\KandangController::class, 'edit'])->name('admin.kandang.edit');
        Route::patch('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'update'])->name('admin.kandang.update');
        Route::delete('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'destroy'])->name('admin.kandang.destroy');

        // Karyawan CRUD
        Route::get('/admin/karyawan', [App\Http\Controllers\KaryawanController::class, 'index'])->name('admin.karyawan');
        Route::get('/admin/karyawan/create', [App\Http\Controllers\KaryawanController::class, 'create'])->name('admin.karyawan.create');
        Route::post('/admin/karyawan', [App\Http\Controllers\KaryawanController::class, 'store'])->name('admin.karyawan.store');
        Route::get('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'show'])->name('admin.karyawan.show');
        Route::get('/admin/karyawan/{karyawan}/edit', [App\Http\Controllers\KaryawanController::class, 'edit'])->name('admin.karyawan.edit');
        Route::patch('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'update'])->name('admin.karyawan.update');
        Route::delete('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');

        // Sistem
        Route::get('/admin/sistem', [App\Http\Controllers\SistemController::class, 'index'])->name('admin.sistem');
        Route::get('/admin/sistem/dashboard', [App\Http\Controllers\SistemController::class, 'dashboard'])->name('admin.sistem.dashboard');
        Route::put('/admin/sistem/dashboard', [App\Http\Controllers\SistemController::class, 'updateDashboard'])->name('admin.sistem.dashboard.update');
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
    Route::post('/admin/pembesaran/{pembesaran}/selesaikan', [App\Http\Controllers\PembesaranController::class, 'selesaikanBatch'])->name('admin.pembesaran.selesaikan');
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
        Route::get('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'showLaporanHarian'])->name('laporan.show');
        Route::patch('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'updateLaporanHarian'])->name('laporan.update');
        Route::delete('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyLaporanHarian'])->name('laporan.destroy');
        
        // Monitoring Lingkungan
        Route::post('/monitoring', [App\Http\Controllers\PembesaranRecordingController::class, 'storeMonitoring'])->name('lingkungan');
        Route::get('/monitoring/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getMonitoringList'])->name('lingkungan.list');
        
        // Kesehatan
        Route::post('/kesehatan', [App\Http\Controllers\PembesaranRecordingController::class, 'storeKesehatan'])->name('kesehatan');
        Route::get('/kesehatan/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getKesehatanList'])->name('kesehatan.list');
        
        // Update Berat
        Route::post('/berat', [App\Http\Controllers\PembesaranRecordingController::class, 'storeBeratRataRata'])->name('berat');
        Route::get('/berat/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getBeratList'])->name('berat.list');
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
    
    // Transfer routes - Penetasan
    Route::get('/admin/penetasan/{penetasan}/transfer', [App\Http\Controllers\TransferController::class, 'showTransferPenetasan'])->name('admin.penetasan.transfer');
    Route::post('/admin/penetasan/{penetasan}/transfer/doc', [App\Http\Controllers\TransferController::class, 'transferDocToPembesaran'])->name('admin.penetasan.transfer.doc');
    Route::post('/admin/penetasan/{penetasan}/transfer/telur-infertil', [App\Http\Controllers\TransferController::class, 'transferTelurInfertilToProduksi'])->name('admin.penetasan.transfer.telur');
    
    // Transfer routes - Pembesaran
    Route::get('/admin/pembesaran/{pembesaran}/transfer', [App\Http\Controllers\TransferController::class, 'showTransferPembesaran'])->name('admin.pembesaran.transfer');
    Route::post('/admin/pembesaran/{pembesaran}/transfer/indukan', [App\Http\Controllers\TransferController::class, 'transferIndukanToProduksi'])->name('admin.pembesaran.transfer.indukan');
    
    // Produksi routes
    Route::get('/admin/produksi', [AdminController::class, 'produksi'])->name('admin.produksi');
    Route::get('/admin/produksi/create', [App\Http\Controllers\ProduksiController::class, 'create'])->name('admin.produksi.create');
    Route::post('/admin/produksi', [App\Http\Controllers\ProduksiController::class, 'store'])->name('admin.produksi.store');
    Route::get('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'show'])->name('admin.produksi.show');
    Route::post('/admin/produksi/{produksi}/laporan-harian', [App\Http\Controllers\ProduksiController::class, 'storeDailyReport'])->name('admin.produksi.laporan.store');
    Route::get('/admin/produksi/{produksi}/laporan/generate-summary', [App\Http\Controllers\ProduksiController::class, 'generateDailyReportSummary'])->name('admin.produksi.laporan.generate-summary');
    Route::delete('/admin/produksi/{produksi}/laporan/{laporan}', [App\Http\Controllers\ProduksiController::class, 'destroyLaporan'])->name('admin.produksi.laporan.destroy');
    Route::patch('/admin/produksi/{produksi}/laporan/{laporan}/reset', [App\Http\Controllers\ProduksiController::class, 'resetLaporan'])->name('admin.produksi.laporan.reset');
    Route::patch('/admin/produksi/{produksi}/tray/{laporan}', [App\Http\Controllers\ProduksiController::class, 'updateTrayEntry'])->name('admin.produksi.tray.update');
    Route::delete('/admin/produksi/{produksi}/tray/{laporan}', [App\Http\Controllers\ProduksiController::class, 'destroyTrayEntry'])->name('admin.produksi.tray.destroy');
    Route::get('/admin/produksi/{produksi}/edit', [App\Http\Controllers\ProduksiController::class, 'edit'])->name('admin.produksi.edit');
    Route::patch('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'update'])->name('admin.produksi.update');
    Route::patch('/admin/produksi/{produksi}/status', [App\Http\Controllers\ProduksiController::class, 'updateStatus'])->name('admin.produksi.updateStatus');
    Route::delete('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'destroy'])->name('admin.produksi.destroy');
    
    // Pencatatan Produksi routes (nested under produksi)
    Route::prefix('admin/produksi/{produksi}/pencatatan')->name('admin.produksi.pencatatan.')->group(function () {
        Route::get('/', [App\Http\Controllers\PencatatanProduksiController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\PencatatanProduksiController::class, 'store'])->name('store');
        Route::patch('/{pencatatan}', [App\Http\Controllers\PencatatanProduksiController::class, 'update'])->name('update');
        Route::delete('/{pencatatan}', [App\Http\Controllers\PencatatanProduksiController::class, 'destroy'])->name('destroy');
        Route::get('/statistics', [App\Http\Controllers\PencatatanProduksiController::class, 'getStatistics'])->name('statistics');
    });
});

require __DIR__.'/auth.php';

// Custom access path for admin login
Route::get('/mimin', [AuthenticatedSessionController::class, 'create'])->name('mimin.login');
Route::post('/mimin', [AuthenticatedSessionController::class, 'store'])->name('mimin.store');
