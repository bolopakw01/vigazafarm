<?php

use App\Http\Controllers\Api\MlPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DssController;
use App\Http\Controllers\FeedVitaminController;
use App\Http\Controllers\DatabaseMaintenanceController;
use App\Http\Controllers\LookerExportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
 *|==================================================================================
 *| File        : routes/web.php                                                   
 *| Deskripsi   : Konfigurasi routing utama sistem manajemen peternakan unggas     
 *|                Mencakup seluruh modul operasional, master data, dan sistem      
 *|                                                                                 
 *| Modul Utama :                                                                  
 *|  - Dashboard & Profil         : Pengelolaan dashboard dan profil pengguna      
 *|  - Master Data (Owner Only)   : Kandang, karyawan, sistem konfigurasi          
 *|  - Pembesaran (Growing)       : Manajemen fase pembesaran ayam                  
 *|  - Penetasan (Hatching)       : Manajemen penetasan telur                       
 *|  - Produksi (Production)      : Manajemen produksi telur                        
 *|  - Sistem & Maintenance       : Backup, restore, IoT, dan konfigurasi sistem   
 *|  - DSS & Machine Learning     : Sistem pendukung keputusan & prediksi          
 *|                                                                                 
 *| Hak Akses   :                                                                  
 *|  - Public                     : Halaman login (/mimin)                                     
 *|  - Authenticated              : Semua user yang login                                      
 *|  - Owner Only                 : Master data dan konfigurasi sistem (middleware:owner)      
 *|                                                                                
 *|  Dibuat     : 10 November 2025                                                 
 *|  Penulis    : Bolopa Kakungnge walinono                                
 *|  Versi      : 3.0.0   
 *|=================================================================================
*/

// ==============================
// ROUTE ROOT & DASHBOARD UTAMA
// ==============================
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('mimin.login');
})->name('home.redirect');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Shortcut agar /admin langsung ke dashboard
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

// ==============================
// GRUP ROUTE DENGAN AUTHENTIKASI
// ==============================
Route::middleware('auth')->group(function () {
    
    // ==============================
    // PROFIL USER
    // ==============================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');            // Form edit profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');      // Update profil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');   // Hapus profil

    // ==============================
    // TEST ERROR PAGES (Manual trigger)
    // ==============================
    Route::prefix('/lopaerror')->name('errors.test.')->group(function () {
        // Landing page to try all error buttons
        Route::get('/', fn() => view('errors.testerror'))->name('index');

        // Abort specific codes when requested; fallback to 404 for unknown codes
        Route::get('/{code}', function ($code) {
            $allowed = [400, 401, 404, 429, 500, 502, 503];
            $code = (int) $code;
            abort(in_array($code, $allowed, true) ? $code : 404);
        })->whereNumber('code')->name('code');
    });

    // ==============================
    // ADMIN DASHBOARD (Semua user yang login)
    // ==============================
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');  // Dashboard admin
    Route::get('/admin/dss', [DssController::class, 'index'])
        ->middleware('dss.enabled')
        ->name('admin.dss');                    // Decision Support System

    // ==============================
    // API MACHINE LEARNING
    // ==============================
    Route::prefix('/api/ml')->name('api.ml.')->group(function () {
        Route::post('/dss/predict', [MlPreviewController::class, 'predict'])->name('dss.predict');  // Prediksi DSS
    });

    // ==============================
    // ROUTE KHUSUS OWNER (Master Data)
    // ==============================
    Route::middleware('owner')->group(function () {
        
        // --------------------------
        // MANAJEMEN KANDANG (CRUD)
        // --------------------------
        Route::get('/admin/kandang', [App\Http\Controllers\KandangController::class, 'index'])->name('admin.kandang');                    // List kandang
        Route::get('/admin/kandang/create', [App\Http\Controllers\KandangController::class, 'create'])->name('admin.kandang.create');     // Form tambah kandang
        Route::post('/admin/kandang', [App\Http\Controllers\KandangController::class, 'store'])->name('admin.kandang.store');             // Simpan kandang baru
        Route::get('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'show'])->name('admin.kandang.show');      // Detail kandang
        Route::get('/admin/kandang/{kandang}/edit', [App\Http\Controllers\KandangController::class, 'edit'])->name('admin.kandang.edit'); // Form edit kandang
        Route::patch('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'update'])->name('admin.kandang.update');// Update kandang
        Route::delete('/admin/kandang/{kandang}', [App\Http\Controllers\KandangController::class, 'destroy'])->name('admin.kandang.destroy'); // Hapus kandang

        // --------------------------
        // MANAJEMEN KARYAWAN (CRUD)
        // --------------------------
        Route::get('/admin/karyawan', [App\Http\Controllers\KaryawanController::class, 'index'])->name('admin.karyawan');                     // List karyawan
        Route::get('/admin/karyawan/create', [App\Http\Controllers\KaryawanController::class, 'create'])->name('admin.karyawan.create');      // Form tambah karyawan
        Route::post('/admin/karyawan', [App\Http\Controllers\KaryawanController::class, 'store'])->name('admin.karyawan.store');              // Simpan karyawan baru
        Route::get('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'show'])->name('admin.karyawan.show');      // Detail karyawan
        Route::get('/admin/karyawan/{karyawan}/edit', [App\Http\Controllers\KaryawanController::class, 'edit'])->name('admin.karyawan.edit'); // Form edit karyawan
        Route::patch('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'update'])->name('admin.karyawan.update');// Update karyawan
        Route::delete('/admin/karyawan/{karyawan}', [App\Http\Controllers\KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy'); // Hapus karyawan

        // ==============================
        // SISTEM KONFIGURASI (Owner Only)
        // ==============================
        Route::get('/admin/sistem', [App\Http\Controllers\SistemController::class, 'index'])->name('admin.sistem'); // Halaman utama sistem
        
        // Dashboard Configuration
        Route::get('/admin/sistem/dashboard', [App\Http\Controllers\SistemController::class, 'dashboard'])->name('admin.sistem.dashboard');          // Konfigurasi dashboard
        Route::put('/admin/sistem/dashboard', [App\Http\Controllers\SistemController::class, 'updateDashboard'])->name('admin.sistem.dashboard.update'); // Update konfig dashboard
        
        // Matrix Configuration
        Route::get('/admin/sistem/matriks', [App\Http\Controllers\SistemController::class, 'matrix'])->name('admin.sistem.matriks');          // Konfigurasi matriks
        Route::put('/admin/sistem/matriks', [App\Http\Controllers\SistemController::class, 'updateMatrix'])->name('admin.sistem.matriks.update'); // Update matriks
        
        // Performance Configuration
        Route::get('/admin/sistem/performance', [App\Http\Controllers\SistemController::class, 'performance'])->name('admin.sistem.performance');          // Konfigurasi performance
        Route::put('/admin/sistem/performance', [App\Http\Controllers\SistemController::class, 'updatePerformance'])->name('admin.sistem.performance.update'); // Update performance
        
        // DSS Configuration
        Route::get('/admin/sistem/dss', [App\Http\Controllers\SistemController::class, 'dss'])->name('admin.sistem.dss');          // Konfigurasi DSS
        Route::put('/admin/sistem/dss', [App\Http\Controllers\SistemController::class, 'updateDss'])->name('admin.sistem.dss.update'); // Update DSS
        
        // PAKAN & VITAMIN MANAGEMENT
        Route::get('/admin/sistem/pakan-vitamin', [FeedVitaminController::class, 'index'])->name('admin.sistem.pakanvitamin');            // List pakan & vitamin
        Route::post('/admin/sistem/pakan-vitamin', [FeedVitaminController::class, 'store'])->name('admin.sistem.pakanvitamin.store');     // Tambah pakan/vitamin
        Route::put('/admin/sistem/pakan-vitamin/{item}', [FeedVitaminController::class, 'update'])->name('admin.sistem.pakanvitamin.update'); // Update pakan/vitamin
        Route::delete('/admin/sistem/pakan-vitamin/{item}', [FeedVitaminController::class, 'destroy'])->name('admin.sistem.pakanvitamin.destroy'); // Hapus pakan/vitamin

        // ==============================
        // DATABASE MAINTENANCE
        // ==============================
        Route::prefix('/admin/sistem/database')->name('admin.sistem.database.')->controller(DatabaseMaintenanceController::class)->group(function () {
            // Backup Database
            Route::get('/backup', 'showBackup')->name('backup');                          // Halaman backup
            Route::post('/backup', 'runBackup')->name('backup.run');                      // Jalankan backup
            Route::get('/backup/download/{filename}', 'downloadBackup')->name('backup.download'); // Download backup
            Route::delete('/backup/{filename}', 'deleteBackup')->name('backup.delete');   // Hapus backup file
            
            // Restore Database
            Route::get('/restore', 'showRestore')->name('restore');                       // Halaman restore
            Route::post('/restore', 'runRestore')->name('restore.run');                   // Jalankan restore
            
            // Database Info & Connection
            Route::get('/info', 'showInfo')->name('info');                                // Info database
            Route::post('/koneksi', 'updateConnection')->name('connection.update');       // Update koneksi database
            
            // Optimization
            Route::get('/optimasi', 'showOptimization')->name('optimization');            // Halaman optimasi
            Route::post('/optimasi', 'runOptimization')->name('optimization.run');        // Jalankan optimasi
        });

        // ==============================
        // IOT CONFIGURATION
        // ==============================
        Route::get('/admin/sistem/iot', [App\Http\Controllers\SistemController::class, 'iot'])->name('admin.sistem.iot');          // Konfigurasi IoT
        Route::put('/admin/sistem/iot', [App\Http\Controllers\SistemController::class, 'updateIot'])->name('admin.sistem.iot.update'); // Update IoT

        // ==============================
        // LOOKER EXPORT
        // ==============================
        Route::get('/admin/sistem/export/looker', [LookerExportController::class, 'index'])->name('admin.sistem.looker.export');                    // Halaman export Looker
        Route::get('/admin/sistem/export/looker/download', [LookerExportController::class, 'download'])->name('admin.sistem.looker.export.download'); // Download export
        Route::get('/admin/sistem/export/looker/download/csv', [LookerExportController::class, 'downloadSingleCsv'])->name('admin.sistem.looker.export.download.csv'); // Download CSV
        Route::get('/admin/sistem/export/looker/download/flat', [LookerExportController::class, 'downloadFlatSingle'])->name('admin.sistem.looker.export.download.flat'); // Download single CSV siap Looker
        Route::post('/admin/sistem/export/looker/embed', [LookerExportController::class, 'toggleEmbed'])->name('admin.sistem.looker.export.embed'); // Toggle embed Looker
    });

    // ==============================
    // OPTIONS PAKAN & VITAMIN (Untuk semua user yang login)
    // ==============================
    Route::get('/admin/sistem/pakan-vitamin/options', [FeedVitaminController::class, 'options'])->name('admin.sistem.pakanvitamin.options');

    // ==============================
    // ROUTE OPERASIONAL (Semua user yang login)
    // ==============================
    
    // ==============================
    // MODUL PEMBESARAN (Growing)
    // ==============================
    Route::get('/admin/pembesaran', [App\Http\Controllers\PembesaranController::class, 'index'])->name('admin.pembesaran');                                // List pembesaran
    Route::get('/admin/pembesaran/create', [App\Http\Controllers\PembesaranController::class, 'create'])->name('admin.pembesaran.create');                 // Form tambah pembesaran
    Route::post('/admin/pembesaran', [App\Http\Controllers\PembesaranController::class, 'store'])->name('admin.pembesaran.store');                         // Simpan pembesaran baru
    Route::get('/admin/pembesaran/from-penetasan/{penetasan}', [App\Http\Controllers\PembesaranController::class, 'createFromPenetasan'])->name('admin.pembesaran.createFromPenetasan'); // Form dari penetasan
    Route::post('/admin/pembesaran/from-penetasan/{penetasan}', [App\Http\Controllers\PembesaranController::class, 'storeFromPenetasan'])->name('admin.pembesaran.storeFromPenetasan');  // Simpan dari penetasan
    Route::get('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'show'])->name('admin.pembesaran.show');              // Detail pembesaran
    Route::get('/admin/pembesaran/{pembesaran}/biaya', [App\Http\Controllers\PembesaranController::class, 'detailBiaya'])->name('admin.pembesaran.detail-biaya'); // Detail biaya
    Route::get('/admin/pembesaran/{pembesaran}/edit', [App\Http\Controllers\PembesaranController::class, 'edit'])->name('admin.pembesaran.edit');          // Form edit pembesaran
    Route::patch('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'update'])->name('admin.pembesaran.update');         // Update pembesaran
    Route::post('/admin/pembesaran/{pembesaran}/selesaikan', [App\Http\Controllers\PembesaranController::class, 'selesaikanBatch'])->name('admin.pembesaran.selesaikan'); // Selesaikan batch
    Route::delete('/admin/pembesaran/{pembesaran}', [App\Http\Controllers\PembesaranController::class, 'destroy'])->name('admin.pembesaran.destroy');      // Hapus pembesaran
    
    // ==============================
    // RECORDING PEMBESARAN (API-like untuk AJAX)
    // ==============================
    Route::prefix('admin/pembesaran/{pembesaran}')->name('admin.pembesaran.recording.')->group(function () {
        // Manajemen Pakan
        Route::post('/pakan', [App\Http\Controllers\PembesaranRecordingController::class, 'storePakan'])->name('pakan');          // Tambah pakan
        Route::get('/pakan/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getPakanList'])->name('pakan.list'); // List pakan
        Route::patch('/pakan/{pakan}', [App\Http\Controllers\PembesaranRecordingController::class, 'updatePakan'])->name('pakan.update'); // Update pakan
        Route::delete('/pakan/{pakan}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyPakan'])->name('pakan.destroy'); // Hapus pakan
        
        // Manajemen Kematian
        Route::post('/kematian', [App\Http\Controllers\PembesaranRecordingController::class, 'storeKematian'])->name('kematian');      // Tambah kematian
        Route::get('/kematian/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getKematianList'])->name('kematian.list'); // List kematian
        Route::patch('/kematian/{kematian}', [App\Http\Controllers\PembesaranRecordingController::class, 'updateKematian'])->name('kematian.update'); // Update kematian
        Route::delete('/kematian/{kematian}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyKematian'])->name('kematian.destroy'); // Hapus kematian
        
        // Laporan Harian
        Route::post('/laporan-harian', [App\Http\Controllers\PembesaranRecordingController::class, 'generateLaporanHarian'])->name('laporan');      // Generate laporan
        Route::get('/laporan-harian/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getLaporanHarianList'])->name('laporan.list'); // List laporan
        Route::get('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'showLaporanHarian'])->name('laporan.show'); // Detail laporan
        Route::patch('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'updateLaporanHarian'])->name('laporan.update'); // Update laporan
        Route::delete('/laporan-harian/{laporan}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyLaporanHarian'])->name('laporan.destroy'); // Hapus laporan
        
        // Monitoring Lingkungan
        Route::post('/monitoring', [App\Http\Controllers\PembesaranRecordingController::class, 'storeMonitoring'])->name('lingkungan');      // Tambah monitoring
        Route::get('/monitoring/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getMonitoringList'])->name('lingkungan.list'); // List monitoring
        Route::delete('/monitoring/{monitoring}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyMonitoring'])->name('lingkungan.destroy'); // Hapus monitoring
        
        // Manajemen Kesehatan
        Route::post('/kesehatan', [App\Http\Controllers\PembesaranRecordingController::class, 'storeKesehatan'])->name('kesehatan');      // Tambah kesehatan
        Route::get('/kesehatan/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getKesehatanList'])->name('kesehatan.list'); // List kesehatan
        Route::post('/kesehatan/{kesehatan}/release', [App\Http\Controllers\PembesaranRecordingController::class, 'releaseKarantina'])->name('kesehatan.release'); // Release karantina
        Route::delete('/kesehatan/{kesehatan}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyKesehatan'])->name('kesehatan.destroy'); // Hapus kesehatan
        
        // Update Berat Rata-rata
        Route::post('/berat', [App\Http\Controllers\PembesaranRecordingController::class, 'storeBeratRataRata'])->name('berat');      // Tambah berat
        Route::get('/berat/list', [App\Http\Controllers\PembesaranRecordingController::class, 'getBeratList'])->name('berat.list'); // List berat
        Route::delete('/berat/{berat}', [App\Http\Controllers\PembesaranRecordingController::class, 'destroyBerat'])->name('berat.destroy'); // Hapus berat
    });
    
    // ==============================
    // MODUL PENETASAN (Hatching)
    // ==============================
    Route::get('/admin/penetasan', [AdminController::class, 'penetasan'])->name('admin.penetasan'); // List penetasan
    
    // CRUD Penetasan
    Route::get('/admin/penetasan/create', [App\Http\Controllers\PenetasanController::class, 'create'])->name('admin.penetasan.create');                 // Form tambah penetasan
    Route::post('/admin/penetasan', [App\Http\Controllers\PenetasanController::class, 'store'])->name('admin.penetasan.store');                         // Simpan penetasan baru
    Route::get('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'show'])->name('admin.penetasan.show');                // Detail penetasan
    Route::get('/admin/penetasan/{penetasan}/edit', [App\Http\Controllers\PenetasanController::class, 'edit'])->name('admin.penetasan.edit');          // Form edit penetasan
    Route::patch('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'update'])->name('admin.penetasan.update');         // Update penetasan
    Route::patch('/admin/penetasan/{penetasan}/status', [App\Http\Controllers\PenetasanController::class, 'updateStatus'])->name('admin.penetasan.updateStatus'); // Update status
    Route::post('/admin/penetasan/{penetasan}/move-to-hatcher', [App\Http\Controllers\PenetasanController::class, 'moveToHatcher'])->name('admin.penetasan.moveToHatcher'); // Pindah ke hatcher
    Route::post('/admin/penetasan/{penetasan}/finish', [App\Http\Controllers\PenetasanController::class, 'finish'])->name('admin.penetasan.finish');    // Selesaikan penetasan
    Route::delete('/admin/penetasan/{penetasan}', [App\Http\Controllers\PenetasanController::class, 'destroy'])->name('admin.penetasan.destroy');       // Hapus penetasan
    
    // ==============================
    // TRANSFER DATA - PENETASAN
    // ==============================
    Route::get('/admin/penetasan/{penetasan}/transfer', [App\Http\Controllers\TransferController::class, 'showTransferPenetasan'])->name('admin.penetasan.transfer'); // Halaman transfer
    Route::post('/admin/penetasan/{penetasan}/transfer/doc', [App\Http\Controllers\TransferController::class, 'transferDocToPembesaran'])->name('admin.penetasan.transfer.doc'); // Transfer DOC ke pembesaran
    Route::post('/admin/penetasan/{penetasan}/transfer/telur-infertil', [App\Http\Controllers\TransferController::class, 'transferTelurInfertilToProduksi'])->name('admin.penetasan.transfer.telur'); // Transfer telur infertil ke produksi
    
    // ==============================
    // TRANSFER DATA - PEMBESARAN
    // ==============================
    Route::get('/admin/pembesaran/{pembesaran}/transfer', [App\Http\Controllers\TransferController::class, 'showTransferPembesaran'])->name('admin.pembesaran.transfer'); // Halaman transfer
    Route::post('/admin/pembesaran/{pembesaran}/transfer/indukan', [App\Http\Controllers\TransferController::class, 'transferIndukanToProduksi'])->name('admin.pembesaran.transfer.indukan'); // Transfer indukan ke produksi
    
    // ==============================
    // MODUL PRODUKSI
    // ==============================
    Route::get('/admin/produksi', [AdminController::class, 'produksi'])->name('admin.produksi'); // List produksi
    
    // CRUD Produksi
    Route::get('/admin/produksi/create', [App\Http\Controllers\ProduksiController::class, 'create'])->name('admin.produksi.create');                 // Form tambah produksi
    Route::post('/admin/produksi', [App\Http\Controllers\ProduksiController::class, 'store'])->name('admin.produksi.store');                         // Simpan produksi baru
    Route::get('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'show'])->name('admin.produksi.show');                // Detail produksi
    Route::get('/admin/produksi/{produksi}/edit', [App\Http\Controllers\ProduksiController::class, 'edit'])->name('admin.produksi.edit');          // Form edit produksi
    Route::patch('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'update'])->name('admin.produksi.update');         // Update produksi
    Route::patch('/admin/produksi/{produksi}/status', [App\Http\Controllers\ProduksiController::class, 'updateStatus'])->name('admin.produksi.updateStatus'); // Update status
    Route::delete('/admin/produksi/{produksi}', [App\Http\Controllers\ProduksiController::class, 'destroy'])->name('admin.produksi.destroy');       // Hapus produksi
    
    // Laporan Harian Produksi
    Route::post('/admin/produksi/{produksi}/laporan-harian', [App\Http\Controllers\ProduksiController::class, 'storeDailyReport'])->name('admin.produksi.laporan.store'); // Simpan laporan harian
    Route::get('/admin/produksi/{produksi}/laporan/generate-summary', [App\Http\Controllers\ProduksiController::class, 'generateDailyReportSummary'])->name('admin.produksi.laporan.generate-summary'); // Generate summary
    Route::delete('/admin/produksi/{produksi}/laporan/{laporan}', [App\Http\Controllers\ProduksiController::class, 'destroyLaporan'])->name('admin.produksi.laporan.destroy'); // Hapus laporan
    Route::patch('/admin/produksi/{produksi}/laporan/{laporan}/reset', [App\Http\Controllers\ProduksiController::class, 'resetLaporan'])->name('admin.produksi.laporan.reset'); // Reset laporan
    
    // Tray Management
    Route::patch('/admin/produksi/{produksi}/tray/{laporan}', [App\Http\Controllers\ProduksiController::class, 'updateTrayEntry'])->name('admin.produksi.tray.update'); // Update tray
    Route::delete('/admin/produksi/{produksi}/tray/{laporan}', [App\Http\Controllers\ProduksiController::class, 'destroyTrayEntry'])->name('admin.produksi.tray.destroy'); // Hapus tray
    
    // ==============================
    // PENCATATAN PRODUKSI (Nested)
    // ==============================
    Route::prefix('admin/produksi/{produksi}/pencatatan')->name('admin.produksi.pencatatan.')->group(function () {
        Route::get('/', [App\Http\Controllers\PencatatanProduksiController::class, 'index'])->name('index');          // List pencatatan
        Route::post('/', [App\Http\Controllers\PencatatanProduksiController::class, 'store'])->name('store');         // Tambah pencatatan
        Route::patch('/{pencatatan}', [App\Http\Controllers\PencatatanProduksiController::class, 'update'])->name('update'); // Update pencatatan
        Route::delete('/{pencatatan}', [App\Http\Controllers\PencatatanProduksiController::class, 'destroy'])->name('destroy'); // Hapus pencatatan
        Route::get('/statistics', [App\Http\Controllers\PencatatanProduksiController::class, 'getStatistics'])->name('statistics'); // Statistik
    });
});

// ==============================
// ROUTE AUTHENTICATION (external)
// ==============================
require __DIR__.'/auth.php';

// ==============================
// CUSTOM LOGIN PATH UNTUK ADMIN
// ==============================
Route::get('/mimin', [AuthenticatedSessionController::class, 'create'])->name('mimin.login');  // Form login admin
Route::post('/mimin', [AuthenticatedSessionController::class, 'store'])->name('mimin.store');  // Proses login admin