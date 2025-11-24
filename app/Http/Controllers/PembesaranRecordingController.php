<?php

namespace App\Http\Controllers;

use App\Models\Pembesaran;
use App\Models\Pakan;
use App\Models\Kematian;
use App\Models\LaporanHarian;
use App\Models\MonitoringLingkungan;
use App\Models\Kesehatan;
use App\Models\StokPakan;
use App\Models\ParameterStandar;
use App\Models\FeedVitaminItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * Controller untuk handling recording pembesaran
 * (Pakan, Kematian, Laporan Harian, Monitoring, Kesehatan)
 */
class PembesaranRecordingController extends Controller
{
    /**
     * ==================================================
     * RECORDING PAKAN HARIAN
     * ==================================================
     */
    
    /**
     * Store pakan harian
     */
    public function storePakan(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        $hasFeedMaster = Schema::hasTable('feed_vitamin_items');

        $rules = [
            'tanggal' => 'required|date',
            'jumlah_kg' => 'required|numeric|min:0',
            'jumlah_karung' => 'nullable|integer|min:0',
        ];

        if ($hasFeedMaster) {
            $rules['feed_item_id'] = ['nullable', 'exists:feed_vitamin_items,id', 'required_without:stok_pakan_id'];
            $rules['stok_pakan_id'] = ['nullable', 'exists:stok_pakan,id', 'required_without:feed_item_id'];
        } else {
            $rules['stok_pakan_id'] = ['required', 'exists:stok_pakan,id'];
        }

        $validated = $request->validate($rules);

        $stokPakan = null;
        $feedItem = null;

        if (!empty($validated['stok_pakan_id'])) {
            $stokPakan = StokPakan::find($validated['stok_pakan_id']);
        }

        if ($hasFeedMaster && !empty($validated['feed_item_id'])) {
            $feedItem = FeedVitaminItem::active()
                ->where('category', 'pakan')
                ->find($validated['feed_item_id']);

            if (!$feedItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pakan yang dipilih sudah tidak aktif.',
                ], 422);
            }
        }

        $hargaPerKg = null;
        if ($stokPakan) {
            $hargaPerKg = (float) $stokPakan->harga_per_kg;
        } elseif ($feedItem) {
            $hargaPerKg = (float) $feedItem->price;
        }

        if ($hargaPerKg === null && $request->filled('harga_per_kg')) {
            $hargaPerKg = (float) $request->input('harga_per_kg');
        }

        $pakan = Pakan::create([
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'produksi_id' => null,
            'stok_pakan_id' => $stokPakan?->id,
            'feed_item_id' => $feedItem?->id,
            'tanggal' => $validated['tanggal'],
            'jumlah_kg' => $validated['jumlah_kg'],
            'jumlah_karung' => $validated['jumlah_karung'] ?? 0,
            'harga_per_kg' => $hargaPerKg,
            'total_biaya' => $hargaPerKg !== null ? $validated['jumlah_kg'] * $hargaPerKg : null,
        ]);

        if ($stokPakan) {
            $stokPakan->stok_kg -= $validated['jumlah_kg'];
            if ($validated['jumlah_karung']) {
                $stokPakan->stok_karung -= $validated['jumlah_karung'];
            }
            $stokPakan->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pakan berhasil disimpan',
            'data' => $pakan->load(['stokPakan', 'feedItem']),
        ]);
    }

    /**
     * Update pakan
     */
    public function updatePakan(Request $request, $pakanId)
    {
        $pakan = Pakan::findOrFail($pakanId);
        $oldJumlahKg = $pakan->jumlah_kg;
        $oldJumlahKarung = $pakan->jumlah_karung ?? 0;

        $hasFeedMaster = Schema::hasTable('feed_vitamin_items');

        $rules = [
            'tanggal' => 'required|date',
            'jumlah_kg' => 'required|numeric|min:0',
            'jumlah_karung' => 'nullable|integer|min:0',
        ];

        if ($hasFeedMaster) {
            $rules['feed_item_id'] = ['nullable', 'exists:feed_vitamin_items,id', 'required_without:stok_pakan_id'];
            $rules['stok_pakan_id'] = ['nullable', 'exists:stok_pakan,id', 'required_without:feed_item_id'];
        } else {
            $rules['stok_pakan_id'] = ['required', 'exists:stok_pakan,id'];
        }

        $validated = $request->validate($rules);

        if ($pakan->stok_pakan_id) {
            $oldStok = StokPakan::find($pakan->stok_pakan_id);
            if ($oldStok) {
                $oldStok->stok_kg += $oldJumlahKg;
                $oldStok->stok_karung += $oldJumlahKarung;
                $oldStok->save();
            }
        }

        $stokPakan = null;
        $feedItem = null;

        if (!empty($validated['stok_pakan_id'])) {
            $stokPakan = StokPakan::find($validated['stok_pakan_id']);
        }

        if ($hasFeedMaster && !empty($validated['feed_item_id'])) {
            $feedItem = FeedVitaminItem::active()
                ->where('category', 'pakan')
                ->find($validated['feed_item_id']);

            if (!$feedItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pakan yang dipilih sudah tidak aktif.',
                ], 422);
            }
        }

        $hargaPerKg = null;
        if ($stokPakan) {
            $hargaPerKg = (float) $stokPakan->harga_per_kg;
        } elseif ($feedItem) {
            $hargaPerKg = (float) $feedItem->price;
        }

        if ($hargaPerKg === null && $request->filled('harga_per_kg')) {
            $hargaPerKg = (float) $request->input('harga_per_kg');
        }

        $pakan->update([
            'stok_pakan_id' => $stokPakan?->id,
            'feed_item_id' => $feedItem?->id,
            'tanggal' => $validated['tanggal'],
            'jumlah_kg' => $validated['jumlah_kg'],
            'jumlah_karung' => $validated['jumlah_karung'] ?? 0,
            'harga_per_kg' => $hargaPerKg,
            'total_biaya' => $hargaPerKg !== null ? $validated['jumlah_kg'] * $hargaPerKg : null,
        ]);

        if ($stokPakan) {
            $stokPakan->stok_kg -= $validated['jumlah_kg'];
            if ($validated['jumlah_karung']) {
                $stokPakan->stok_karung -= $validated['jumlah_karung'];
            }
            $stokPakan->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pakan berhasil diperbarui',
            'data' => $pakan->load(['stokPakan', 'feedItem']),
        ]);
    }

    /**
     * Delete pakan
     */
    public function destroyPakan($pakanId)
    {
        $pakan = Pakan::findOrFail($pakanId);

        if ($pakan->stok_pakan_id) {
            $stokPakan = StokPakan::find($pakan->stok_pakan_id);
            if ($stokPakan) {
                $stokPakan->stok_kg += $pakan->jumlah_kg;
                $stokPakan->stok_karung += $pakan->jumlah_karung ?? 0;
                $stokPakan->save();
            }
        }

        $pakan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pakan berhasil dihapus',
        ]);
    }

    /**
     * Get list pakan untuk pembesaran
     */
    public function getPakanList(Pembesaran $pembesaran)
    {
        $batchId = $pembesaran->batch_produksi_id;

        $pakanQuery = Pakan::query();
        if ($batchId) {
            $pakanQuery->where('batch_produksi_id', $batchId);
        } else {
            $pakanQuery->whereNull('batch_produksi_id');
        }

        $pakanList = (clone $pakanQuery)
            ->with(['stokPakan', 'feedItem'])
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        $totalKonsumsi = (float) $pakanQuery->sum('jumlah_kg');
        $totalBiaya = (float) $pakanQuery->sum('total_biaya');

        return response()->json([
            'success' => true,
            'data' => $pakanList,
            'summary' => [
                'total_konsumsi_kg' => $totalKonsumsi,
                'total_biaya' => $totalBiaya,
            ],
        ]);
    }

    /**
     * ==================================================
     * RECORDING KEMATIAN
     * ==================================================
     */
    
    /**
     * Store kematian
     */
    public function storeKematian(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'penyebab' => 'required|in:penyakit,stress,kecelakaan,usia,tidak_diketahui',
            'keterangan' => 'nullable|string',
        ]);

        $kematian = Kematian::create([
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'produksi_id' => null,
            'tanggal' => $validated['tanggal'],
            'jumlah' => $validated['jumlah'],
            'penyebab' => $validated['penyebab'],
            'keterangan' => $validated['keterangan'],
        ]);

        // Hitung mortalitas
        $totalMati = Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
        $mortalitas = Kematian::hitungMortalitasKumulatif(
            $pembesaran->batch_produksi_id, 
            $pembesaran->jumlah_anak_ayam
        );

        // Check if mortalitas tinggi
        $isHighMortality = $mortalitas > 5;

        return response()->json([
            'success' => true,
            'message' => 'Data kematian berhasil disimpan',
            'data' => $kematian,
            'total_mati' => $totalMati,
            'mortalitas' => $mortalitas,
            'is_high_mortality' => $isHighMortality,
            'alert' => $isHighMortality ? 'Perhatian! Mortalitas melebihi 5%' : null,
        ]);
    }

    /**
     * Update kematian
     */
    public function updateKematian(Request $request, $kematianId)
    {
        $kematian = Kematian::findOrFail($kematianId);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'penyebab' => 'required|in:penyakit,stress,kecelakaan,usia,tidak_diketahui',
            'keterangan' => 'nullable|string',
        ]);

        $kematian->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kematian berhasil diperbarui',
            'data' => $kematian,
        ]);
    }

    /**
     * Delete kematian
     */
    public function destroyKematian($kematianId)
    {
        $kematian = Kematian::findOrFail($kematianId);
        $kematian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kematian berhasil dihapus',
        ]);
    }

    /**
     * Get list kematian
     */
    public function getKematianList(Pembesaran $pembesaran)
    {
        
        $kematianList = Kematian::where('batch_produksi_id', $pembesaran->batch_produksi_id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        $totalMati = Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
        $mortalitas = Kematian::hitungMortalitasKumulatif(
            $pembesaran->batch_produksi_id, 
            $pembesaran->jumlah_anak_ayam
        );
        $statistikPenyebab = Kematian::getStatistikPenyebab($pembesaran->batch_produksi_id);

        return response()->json([
            'success' => true,
            'data' => $kematianList,
            'total_mati' => $totalMati,
            'mortalitas' => $mortalitas,
            'statistik_penyebab' => $statistikPenyebab,
        ]);
    }

    /**
     * ==================================================
     * LAPORAN HARIAN
     * ==================================================
     */
    
    /**
     * Generate laporan harian otomatis
     */
    public function generateLaporanHarian(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'catatan_kejadian' => 'nullable|string',
        ]);

        // Get authenticated user ID (should be integer)
        $userId = Auth::id();
        
        // Fallback: if Auth::id() returns null, try to get from Auth::user()
        if (!$userId && Auth::check()) {
            $userId = Auth::user()->id;
        }
        
        // Last resort: get first user ID (for development only)
        if (!$userId) {
            $userId = \App\Models\User::first()->id ?? 1;
        }

        // Check if laporan already exists for this date (server-side guard)
        $existing = LaporanHarian::getLaporanHarian($pembesaran->batch_produksi_id, $validated['tanggal']);
        if ($existing) {
            return response()->json([
                'success' => true,
                'already_exists' => true,
                'message' => 'Anda sudah melakukan pencatatan laporan harian untuk tanggal ini',
                'data' => $existing,
            ]);
        }

        // Create laporan jika belum ada
        $laporan = LaporanHarian::generateLaporanHarian(
            $pembesaran->batch_produksi_id,
            $validated['tanggal'],
            $userId
        );

        if ($laporan && $validated['catatan_kejadian']) {
            $laporan->catatan_kejadian = $validated['catatan_kejadian'];
            $laporan->save();
        }

        return response()->json([
            'success' => true,
            'already_exists' => false,
            'message' => 'Laporan harian berhasil di-generate',
            'data' => $laporan,
        ]);
    }

    /**
     * Get laporan harian list
     */
    public function getLaporanHarianList(Pembesaran $pembesaran)
    {
        
        $laporanList = LaporanHarian::where('batch_produksi_id', $pembesaran->batch_produksi_id)
            ->with('pengguna')
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $laporanList,
        ]);
    }

    /**
     * Show detail laporan harian (full page with layout)
     */
    public function showLaporanHarian(Pembesaran $pembesaran, LaporanHarian $laporan)
    {
        // Verify laporan belongs to this pembesaran
        if ($laporan->batch_produksi_id !== $pembesaran->batch_produksi_id) {
            abort(404, 'Laporan tidak ditemukan untuk pembesaran ini');
        }

        // Load relasi
        $laporan->load('pengguna');

        return view('admin.pages.pembesaran.detail-laporan', [
            'laporan' => $laporan,
            'pembesaran' => $pembesaran
        ]);
    }

    /**
     * Update laporan harian (hanya pembuat atau owner)
     */
    public function updateLaporanHarian(Request $request, Pembesaran $pembesaran, LaporanHarian $laporan)
    {
        // Verify laporan belongs to this pembesaran
        if ($laporan->batch_produksi_id !== $pembesaran->batch_produksi_id) {
            abort(404, 'Laporan tidak ditemukan untuk pembesaran ini');
        }

        // Authorization: hanya pembuat atau owner
        $user = Auth::user();
        if ($laporan->pengguna_id !== $user->id && $user->peran !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit laporan ini'
            ], 403);
        }

        $validated = $request->validate([
            'catatan_kejadian' => 'nullable|string',
        ]);

        $laporan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $laporan
        ]);
    }

    /**
     * Delete laporan harian (hanya pembuat atau owner)
     */
    public function destroyLaporanHarian(Pembesaran $pembesaran, LaporanHarian $laporan)
    {
        // Verify laporan belongs to this pembesaran
        if ($laporan->batch_produksi_id !== $pembesaran->batch_produksi_id) {
            abort(404, 'Laporan tidak ditemukan untuk pembesaran ini');
        }

        // Authorization: hanya pembuat atau owner
        $user = Auth::user();
        if ($laporan->pengguna_id !== $user->id && $user->peran !== 'owner') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus laporan ini'
            ], 403);
        }

        $laporan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus'
        ]);
    }

    /**
     * ==================================================
     * MONITORING LINGKUNGAN
     * ==================================================
     */
    
    /**
     * Store monitoring lingkungan
     */
    public function storeMonitoring(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'waktu_pencatatan' => 'required|date',
            'suhu' => 'required|numeric',
            'kelembaban' => 'required|numeric',
            'intensitas_cahaya' => 'nullable|numeric',
            'kondisi_ventilasi' => 'nullable|in:Baik,Cukup,Kurang',
            'catatan' => 'nullable|string',
        ]);

        $monitoring = MonitoringLingkungan::create([
            'kandang_id' => $pembesaran->kandang_id,
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'waktu_pencatatan' => $validated['waktu_pencatatan'],
            'suhu' => $validated['suhu'],
            'kelembaban' => $validated['kelembaban'],
            'intensitas_cahaya' => $validated['intensitas_cahaya'],
            'kondisi_ventilasi' => $validated['kondisi_ventilasi'],
            'catatan' => $validated['catatan'],
        ]);

        // Check status lingkungan
        $status = $monitoring->getStatusLingkungan('grower');

        return response()->json([
            'success' => true,
            'message' => 'Data monitoring berhasil disimpan',
            'data' => $monitoring,
            'status' => $status,
        ]);
    }

    /**
     * Get monitoring list
     */
    public function getMonitoringList(Pembesaran $pembesaran)
    {
        
        $monitoringList = MonitoringLingkungan::where('batch_produksi_id', $pembesaran->batch_produksi_id)
            ->orderByDesc('waktu_pencatatan')
            ->limit(50)
            ->get();

        // Get summary mingguan
        $summaryMingguan = MonitoringLingkungan::getSummaryMingguan(
            $pembesaran->kandang_id,
            $pembesaran->batch_produksi_id
        );

        return response()->json([
            'success' => true,
            'data' => $monitoringList,
            'summary_mingguan' => $summaryMingguan,
        ]);
    }

    /**
     * ==================================================
     * KESEHATAN & VAKSINASI
     * ==================================================
     */
    
    /**
     * Store kegiatan kesehatan
     */
    public function storeKesehatan(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tipe_kegiatan' => 'required|in:vaksinasi,pengobatan,pemeriksaan_rutin,karantina',
            'nama_vaksin_obat' => 'required|string',
            'jumlah_burung' => 'required|integer|min:1',
            'gejala' => 'nullable|string',
            'diagnosa' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'biaya' => 'nullable|numeric|min:0',
            'petugas' => 'nullable|string',
        ]);

        $kesehatan = Kesehatan::create([
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'tanggal' => $validated['tanggal'],
            'tipe_kegiatan' => $validated['tipe_kegiatan'],
            'nama_vaksin_obat' => $validated['nama_vaksin_obat'],
            'jumlah_burung' => $validated['jumlah_burung'],
            'gejala' => $validated['gejala'],
            'diagnosa' => $validated['diagnosa'],
            'tindakan' => $validated['tindakan'],
            'biaya' => $validated['biaya'],
            'petugas' => $validated['petugas'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kesehatan berhasil disimpan',
            'data' => $kesehatan,
        ]);
    }

    /**
     * Get kesehatan list
     */
    public function getKesehatanList(Pembesaran $pembesaran)
    {
        
        $kesehatanList = Kesehatan::where('batch_produksi_id', $pembesaran->batch_produksi_id)
            ->orderByDesc('tanggal')
            ->get();

        // Hitung umur batch
        $umurHari = Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(Carbon::now());
        
        // Generate reminder vaksinasi
        $reminders = Kesehatan::generateReminder($pembesaran->batch_produksi_id, $umurHari);

        $totalBiaya = Kesehatan::getTotalBiayaKesehatan($pembesaran->batch_produksi_id);

        return response()->json([
            'success' => true,
            'data' => $kesehatanList,
            'reminders' => $reminders,
            'umur_hari' => $umurHari,
            'total_biaya' => $totalBiaya,
        ]);
    }

    /**
     * ==================================================
     * UPDATE SAMPLING BERAT
     * ==================================================
     */
    
    /**
     * Store berat rata-rata pembesaran (sampling mingguan)
     */
    public function storeBeratRataRata(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'berat_rata_rata' => 'required|numeric|min:0',
            'umur_hari' => 'required|integer|min:0',
            'jumlah_sampel' => 'nullable|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        // Update pembesaran (nilai terkini)
        $pembesaran->update([
            'berat_rata_rata' => $validated['berat_rata_rata'],
            'umur_hari' => $validated['umur_hari'],
        ]);

        // Simpan history ke tabel berat_sampling
        $beratSampling = \App\Models\BeratSampling::create([
            'batch_produksi_id' => $pembesaran->batch_produksi_id,
            'tanggal_sampling' => now()->toDateString(),
            'umur_hari' => $validated['umur_hari'],
            'berat_rata_rata' => $validated['berat_rata_rata'],
            'jumlah_sampel' => $validated['jumlah_sampel'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Get parameter standar untuk grower
        $paramStandar = ParameterStandar::where('fase', 'grower')
            ->where('parameter', 'berat_rata_rata')
            ->first();

        $status = 'normal';
        $badge = 'success';
        $message = 'Berat sesuai standar';

        if ($paramStandar) {
            if ($validated['berat_rata_rata'] < $paramStandar->nilai_minimal) {
                $status = 'warning';
                $badge = 'warning';
                $message = 'Berat di bawah standar minimal';
            } elseif ($validated['berat_rata_rata'] > $paramStandar->nilai_maksimal) {
                $status = 'success';
                $badge = 'success';
                $message = 'Berat di atas standar (performa sangat baik)';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berat rata-rata berhasil disimpan',
            'data' => $pembesaran,
            'sampling' => $beratSampling,
            'standar' => $paramStandar,
            'status' => [
                'status' => $status,
                'badge' => $badge,
                'message' => $message,
            ],
        ]);
    }

    /**
     * Update berat rata-rata pembesaran (sampling mingguan)
     */
    public function updateBeratRataRata(Request $request, $pembesaranId)
    {
        $pembesaran = Pembesaran::findOrFail($pembesaranId);
        
        $validated = $request->validate([
            'berat_rata_rata' => 'required|numeric|min:0',
            'umur_hari' => 'required|integer|min:0',
        ]);

        $pembesaran->update([
            'berat_rata_rata' => $validated['berat_rata_rata'],
            'umur_hari' => $validated['umur_hari'],
        ]);

        // Get parameter standar untuk grower
        $paramStandar = ParameterStandar::where('fase', 'grower')
            ->where('parameter', 'berat_rata_rata')
            ->first();

        $status = 'normal';
        $badge = 'success';
        $message = 'Berat sesuai standar';

        if ($paramStandar) {
            if ($validated['berat_rata_rata'] < $paramStandar->nilai_minimal) {
                $status = 'warning';
                $badge = 'warning';
                $message = 'Berat di bawah standar minimal';
            } elseif ($validated['berat_rata_rata'] > $paramStandar->nilai_maksimal) {
                $status = 'success';
                $badge = 'success';
                $message = 'Berat di atas standar (performa sangat baik)';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berat rata-rata berhasil diperbarui',
            'data' => $pembesaran,
            'standar' => $paramStandar,
            'status' => [
                'status' => $status,
                'badge' => $badge,
                'message' => $message,
            ],
        ]);
    }

    /**
     * Get list berat sampling
     */
    public function getBeratList(Pembesaran $pembesaran)
    {
        $beratList = \App\Models\BeratSampling::where('batch_produksi_id', $pembesaran->batch_produksi_id)
            ->orderBy('tanggal_sampling', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $beratList,
        ]);
    }
}
