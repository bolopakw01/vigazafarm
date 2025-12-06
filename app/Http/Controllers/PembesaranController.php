<?php

namespace App\Http\Controllers;

use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Kandang;
use App\Models\FeedVitaminItem;
use App\Models\Pakan;
use App\Models\Kematian;
use App\Models\MonitoringLingkungan;
use App\Models\Kesehatan;
use App\Models\LaporanHarian;
use App\Models\FeedHistory;
use App\Models\BeratSampling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ==========================================
 * Controller : PembesaranController
 * Deskripsi  : Mengelola siklus pembesaran mulai dari penjadwalan, pencatatan, hingga penyelesaian batch.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class PembesaranController extends Controller
{
    private const READY_MIN_DAYS = 35;
    private const READY_MAX_DAYS = 40;
    private const READY_DEFAULT_DAYS = 38;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Menampilkan daftar pembesaran (batch) dengan relasi dan paginasi.
         */
        $pembesaran = Pembesaran::with(['kandang', 'penetasan', 'creator', 'updater'])
            ->orderBy('dibuat_pada', 'desc')
            ->paginate(10);

        return view('admin.pages.pembesaran.index-pembesaran', compact('pembesaran'));
    }

    /**
     * Show the form for creating a new resource from penetasan.
     */
    public function createFromPenetasan($penetasanId)
    {
        /**
         * Menampilkan form pembuatan pembesaran baru yang bersumber dari penetasan tertentu.
         * Melakukan validasi status dan jumlah DOQ sebelum menampilkan form.
         */
        $penetasan = Penetasan::with('kandang')->findOrFail($penetasanId);
        
        // Validasi status penetasan harus selesai
        if ($penetasan->status !== 'selesai') {
            return redirect()->route('admin.penetasan')
                ->with('error', 'Hanya penetasan dengan status "selesai" yang dapat dipindahkan ke pembesaran.');
        }

        // Validasi harus ada jumlah DOQ
        if (!$penetasan->jumlah_doc || $penetasan->jumlah_doc <= 0) {
            return redirect()->route('admin.penetasan')
            ->with('error', 'Penetasan harus memiliki jumlah DOQ yang valid untuk dipindahkan ke pembesaran.');
        }

        // Ambil kandang pembesaran aktif
        $kandangList = Kandang::query()
            ->typeIs('pembesaran')
            ->statusIn(['aktif', 'maintenance'])
            ->orderBy('nama_kandang')
            ->get();

        return view('admin.pages.pembesaran.create-from-penetasan', compact('penetasan', 'kandangList'));
    }

    /**
     * Store a newly created resource in storage from penetasan.
     */
    public function storeFromPenetasan(Request $request, $penetasanId)
    {
        /**
         * Menyimpan data pembesaran yang dibuat dari penetasan setelah validasi input.
         */
        $validated = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'catatan' => 'nullable|string|max:100',
        ]);

        $penetasan = Penetasan::findOrFail($penetasanId);

        // Validasi jumlah tidak melebihi DOQ yang tersedia
        if ($validated['jumlah_anak_ayam'] > $penetasan->jumlah_doc) {
            return back()->withInput()
            ->withErrors(['jumlah_anak_ayam' => 'Jumlah anak puyuh tidak boleh melebihi jumlah DOQ yang tersedia (' . $penetasan->jumlah_doc . ')']);
        }

        $readyDate = $request->input('tanggal_siap');
        if (!$readyDate && !empty($validated['tanggal_masuk'])) {
            $readyDate = $this->getEstimatedReadyDate($validated['tanggal_masuk']);
        }

        $pembesaran = Pembesaran::create([
            'penetasan_id' => $penetasan->id,
            'kandang_id' => $validated['kandang_id'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'jumlah_anak_ayam' => $validated['jumlah_anak_ayam'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? 'campuran',
            'status_batch' => 'Aktif',
            'catatan' => $validated['catatan'] ?? null,
            'created_by' => Auth::id(),
            'batch_produksi_id' => $penetasan->batch ?? $this->generateBatchCode(),
            'tanggal_siap' => $readyDate,
        ]);

        Kandang::find($validated['kandang_id'])?->syncMaintenanceStatus();

        return redirect()->route('admin.pembesaran')
            ->with(
                'success',
                'Data pembesaran berhasil ditambahkan dengan batch: ' . ($pembesaran->batch_produksi_id ?? $penetasan->batch ?? ('ID ' . $pembesaran->id))
            );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Menampilkan form pembuatan pembesaran baru (manual) dan meng-generate kode batch.
         */
        $kandangList = Kandang::query()
            ->typeIs('pembesaran')
            ->statusIn(['aktif', 'maintenance'])
            ->orderBy('nama_kandang')
            ->get();

        // Ambil daftar penetasan yang selesai dan punya DOQ
        $penetasanList = Penetasan::where('status', 'selesai')
            ->where('jumlah_doc', '>', 0)
            ->orderBy('tanggal_menetas', 'desc')
            ->get();

        $generatedBatch = $this->generateBatchCode();

        return view('admin.pages.pembesaran.create-pembesaran', compact('kandangList', 'penetasanList', 'generatedBatch'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /**
         * Memvalidasi dan menyimpan pembesaran baru ke database.
         */
        $validated = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'penetasan_id' => 'nullable|exists:vf_penetasan,id',
            'batch_produksi_id' => 'required|string|unique:vf_pembesaran,batch_produksi_id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'umur_hari' => 'nullable|integer|min:0',
            'tanggal_siap' => 'nullable|date|after:tanggal_masuk',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'target_berat_akhir' => 'nullable|numeric|min:0',
            'kondisi_doc' => 'nullable|string',
            'catatan' => 'nullable|string|max:100',
        ]);

        $kandang = Kandang::findOrFail($validated['kandang_id']);
        $kapasitasTersisa = $kandang->kapasitas_tersisa;
        $jumlahMasuk = (int) $validated['jumlah_anak_ayam'];

        if ($kapasitasTersisa <= 0) {
            return back()->withInput()->withErrors([
                'kandang_id' => sprintf(
                    'Kandang %s sudah penuh. Silakan pilih kandang lain atau kosongkan terlebih dahulu.',
                    $kandang->nama_kandang ?? ('#' . $kandang->id)
                ),
            ]);
        }

        if ($jumlahMasuk > $kapasitasTersisa) {
            return back()->withInput()->withErrors([
                'jumlah_anak_ayam' => sprintf(
                    'Jumlah anak puyuh (%s) melebihi sisa kapasitas %s pada kandang %s.',
                    number_format($jumlahMasuk),
                    number_format($kapasitasTersisa),
                    $kandang->nama_kandang ?? ('#' . $kandang->id)
                ),
            ]);
        }

        // Tetapkan nilai default
        $validated['status_batch'] = 'Aktif';
        $validated['jenis_kelamin'] = $validated['jenis_kelamin'] ?? 'campuran';
        $validated['created_by'] = Auth::id();

        if (empty($validated['tanggal_siap']) && !empty($validated['tanggal_masuk'])) {
            $validated['tanggal_siap'] = $this->getEstimatedReadyDate($validated['tanggal_masuk']);
        }

        $pembesaran = Pembesaran::create($validated);

        $kandang->syncMaintenanceStatus();

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil ditambahkan dengan batch: ' . ($pembesaran->batch_produksi_id ?? $validated['batch_produksi_id']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembesaran $pembesaran)
    {
        /**
         * Menampilkan halaman detail sebuah pembesaran beserta metrik dan reminder vaksinasi.
         */
        $pembesaran->load(['kandang', 'penetasan']);
        
        // Hitung metrics
        $populasiAwal = $pembesaran->jumlah_anak_ayam;
        $totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
        $populasiSaatIni = $populasiAwal - $totalMati;
        $mortalitas = \App\Models\Kematian::hitungMortalitasKumulatif($pembesaran->batch_produksi_id, $populasiAwal);
        $karantinaAktif = Kesehatan::totalKarantinaAktif($pembesaran->batch_produksi_id);
        $populasiSaatIni = max(0, $populasiSaatIni - $karantinaAktif);
        
        // Hitung total konsumsi pakan & biaya
        $totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);
        $totalBiayaPakan = \App\Models\Pakan::totalBiayaByBatch($pembesaran->batch_produksi_id);
        
        // Hitung total biaya kesehatan & vaksinasi
        $totalBiayaKesehatan = \App\Models\Kesehatan::getTotalBiayaKesehatan($pembesaran->batch_produksi_id);
        
        // Hitung umur hari (menggunakan startOfDay agar hasilnya integer)
        $umurHari = \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay());
        
        // Get stok pakan untuk dropdown legacy
        $stokPakanList = \App\Models\StokPakan::where('stok_kg', '>', 0)
            ->orderBy('nama_pakan')
            ->get();

        // Sync daftar pakan dengan master Set Pakan & Vitamin (jika tersedia)
        $feedOptions = collect();
        $vitaminOptions = collect();
        if (Schema::hasTable('vf_feed_vitamin_items')) {
            $feedOptions = FeedVitaminItem::active()
                ->where('category', 'pakan')
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'unit']);

            $vitaminOptions = FeedVitaminItem::active()
                ->where('category', 'vitamin')
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'unit']);
        }

        $kandangKarantinaOptions = Kandang::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(COALESCE(tipe_kandang, "")) LIKE ?', ['%karantina%'])
                    ->orWhereRaw('LOWER(COALESCE(nama_kandang, "")) LIKE ?', ['%karantina%']);
            })
            ->orderBy('nama_kandang')
            ->get(['id', 'nama_kandang', 'kode_kandang', 'kapasitas_maksimal', 'tipe_kandang', 'status']);

        if ($kandangKarantinaOptions->isEmpty()) {
            $kandangKarantinaOptions = Kandang::query()
                ->orderBy('nama_kandang')
                ->limit(25)
                ->get(['id', 'nama_kandang', 'kode_kandang', 'kapasitas_maksimal', 'tipe_kandang', 'status']);
        }
        
        // Get parameter standar
        $paramBeratStandar = \App\Models\ParameterStandar::where('fase', 'grower')
            ->where('parameter', 'berat_rata_rata')
            ->first();
            
        $paramSuhuStandar = \App\Models\ParameterStandar::where('fase', 'grower')
            ->where('parameter', 'suhu')
            ->first();
            
        $paramKelembabanStandar = \App\Models\ParameterStandar::where('fase', 'grower')
            ->where('parameter', 'kelembaban')
            ->first();
        
        // Get reminder vaksinasi
        $reminders = \App\Models\Kesehatan::generateReminder($pembesaran->batch_produksi_id, $umurHari);
        
        return view('admin.pages.pembesaran.show-pembesaran', compact(
            'pembesaran',
            'populasiAwal',
            'populasiSaatIni',
            'totalMati',
            'mortalitas',
            'totalPakan',
            'totalBiayaPakan',
            'totalBiayaKesehatan',
            'umurHari',
            'stokPakanList',
            'feedOptions',
            'vitaminOptions',
            'kandangKarantinaOptions',
            'paramBeratStandar',
            'paramSuhuStandar',
            'paramKelembabanStandar',
            'reminders',
            'karantinaAktif'
        ));
    }

    /**
     * Display the detail biaya page for the specified resource.
     */
    public function detailBiaya(Pembesaran $pembesaran)
    {
        /**
         * Menampilkan halaman detail biaya pembesaran dengan breakdown lengkap.
         */
        $pembesaran->load(['kandang', 'penetasan']);

        // Hitung metrics yang diperlukan untuk halaman detail biaya
        $populasiAwal = $pembesaran->jumlah_anak_ayam;
        $totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
        $populasiSaatIni = $populasiAwal - $totalMati;
        $karantinaAktif = Kesehatan::totalKarantinaAktif($pembesaran->batch_produksi_id);
        $populasiSaatIni = max(0, $populasiSaatIni - $karantinaAktif);

        // Hitung total konsumsi pakan & biaya
        $totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);
        $totalBiayaPakan = \App\Models\Pakan::totalBiayaByBatch($pembesaran->batch_produksi_id);

        // Hitung total biaya kesehatan & vaksinasi
        $totalBiayaKesehatan = \App\Models\Kesehatan::getTotalBiayaKesehatan($pembesaran->batch_produksi_id);

        // Hitung umur hari
        $umurHari = \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay());

        return view('admin.pages.pembesaran.detail-biaya', compact(
            'pembesaran',
            'populasiSaatIni',
            'karantinaAktif',
            'totalPakan',
            'totalBiayaPakan',
            'totalBiayaKesehatan',
            'umurHari'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembesaran $pembesaran)
    {
        /**
         * Menampilkan form edit untuk pembesaran yang dipilih.
         */
        $kandangList = Kandang::query()
            ->where(function ($query) use ($pembesaran) {
                $query->where(function ($available) {
                    $available->typeIs('pembesaran')->statusIn(['aktif', 'maintenance']);
                });

                if ($pembesaran->kandang_id) {
                    $query->orWhere('id', $pembesaran->kandang_id);
                }
            })
            ->orderBy('nama_kandang')
            ->get();

        return view('admin.pages.pembesaran.edit-pembesaran', compact('pembesaran', 'kandangList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembesaran $pembesaran)
    {
        /**
         * Memvalidasi dan memperbarui data pembesaran, termasuk kontrol akses untuk update status.
         */
        $validated = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'tanggal_siap' => 'nullable|date|after_or_equal:tanggal_masuk',
            'jumlah_siap' => 'nullable|integer|min:0',
            'umur_hari' => 'nullable|integer|min:0',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:100',
        ]);

        // Owner atau Super Admin bisa update status dengan menyalakan override
        $user = Auth::user();
        $canOwnerOverride = $user && in_array($user->peran, ['owner', 'super_admin']);

        if ($canOwnerOverride && $request->boolean('owner_override_active')) {
            $validated = array_merge($validated, $request->validate([
                'status_batch' => 'nullable|in:Aktif,Selesai',
                'tanggal_selesai' => 'nullable|date',
            ]));
        }

        $validated['updated_by'] = Auth::id();

        $pembesaran->update($validated);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran dengan batch: ' . ($pembesaran->batch_produksi_id ?? ('ID ' . $pembesaran->id)) . ' berhasil diperbarui.');
    }

    /**
     * Selesaikan batch pembesaran
     */
    public function selesaikanBatch(Pembesaran $pembesaran)
    {
        /**
         * Menandai batch pembesaran sebagai selesai setelah pengecekan aturan (umur atau role owner).
         */
        // Cek apakah user adalah owner atau super admin
    $user = Auth::user();
        $isOwnerOrSuperAdmin = $user && ($user->peran === 'owner' || $user->peran === 'super_admin');
        
        if (!$isOwnerOrSuperAdmin) {
            // Jika bukan owner, cek apakah target sudah tercapai
            $umurHari = \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(\Carbon\Carbon::now());
            $targetUmur = 35; // Target umur minimal untuk pembesaran
            
            if ($umurHari < $targetUmur) {
                return back()->with('error', 'Batch belum mencapai target umur minimal (' . $targetUmur . ' hari). Saat ini: ' . $umurHari . ' hari.');
            }
            
            // Cek target berat
            if ($pembesaran->target_berat_akhir && $pembesaran->berat_rata_rata < $pembesaran->target_berat_akhir) {
                return back()->with('error', 'Target berat belum tercapai. Target: ' . $pembesaran->target_berat_akhir . 'g, Saat ini: ' . $pembesaran->berat_rata_rata . 'g');
            }
        }
        
        // Update status dan tanggal selesai
        $pembesaran->update([
            'status_batch' => 'Selesai',
            'tanggal_selesai' => \Carbon\Carbon::now()
        ]);
        
    return back()->with('success', 'Batch pembesaran ' . ($pembesaran->batch_produksi_id ?? ('ID ' . $pembesaran->id)) . ' berhasil diselesaikan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembesaran $pembesaran)
    {
        /**
         * Menghapus data pembesaran dari database.
         */
        $batchLabel = $pembesaran->batch_produksi_id ?? null;
        $identifier = 'ID: ' . $pembesaran->id;
        $batchId = $pembesaran->batch_produksi_id;

        DB::transaction(function () use ($pembesaran, $batchId) {
            if ($batchId) {
                $this->deleteBatchRelatedData($batchId);
            }

            $pembesaran->delete();
        });

        return redirect()->route('admin.pembesaran')
            ->with(
                'success',
                'Data pembesaran ' . $identifier . ($batchLabel ? ' (Batch: ' . $batchLabel . ')' : '') . ' berhasil dihapus.'
            );
    }
    /**
     * Hapus seluruh data operasional yang terikat dengan batch tertentu.
     */
    private function deleteBatchRelatedData(?string $batchId): void
    {
        if (!$batchId) {
            return;
        }

        Pakan::where('batch_produksi_id', $batchId)->delete();
        Kematian::where('batch_produksi_id', $batchId)->delete();
        MonitoringLingkungan::where('batch_produksi_id', $batchId)->delete();
        Kesehatan::where('batch_produksi_id', $batchId)->delete();
        LaporanHarian::where('batch_produksi_id', $batchId)->delete();
        if ($this->feedHistoryTableAvailable()) {
            FeedHistory::where('batch_produksi_id', $batchId)->delete();
        }
        BeratSampling::where('batch_produksi_id', $batchId)->delete();
    }

    /**
     * Determine if optional feed history table exists before running queries.
     */
    private function feedHistoryTableAvailable(): bool
    {
        static $hasTable = null;

        if ($hasTable === null) {
            $hasTable = Schema::hasTable((new FeedHistory())->getTable());
        }

        return $hasTable;
    }

    /**
     * Hitung tanggal siap estimasi dari tanggal masuk.
     */
    private function getEstimatedReadyDate(?string $tanggalMasuk, ?int $offsetDays = null): ?string
    {
        if (!$tanggalMasuk) {
            return null;
        }

        $days = $offsetDays ?? self::READY_DEFAULT_DAYS;
        $days = max(self::READY_MIN_DAYS, min(self::READY_MAX_DAYS, $days));

        return Carbon::parse($tanggalMasuk)
            ->addDays($days)
            ->format('Y-m-d');
    }

    /**
     * Generate kode batch pembesaran unik berbasis tanggal.
     */
    private function generateBatchCode(): string
    {
        $today = date('Ymd');
        $prefix = 'PB-' . $today . '-';

        $lastBatch = Pembesaran::where('batch_produksi_id', 'like', $prefix . '%')
            ->orderByDesc('batch_produksi_id')
            ->first();

        if ($lastBatch && $lastBatch->batch_produksi_id) {
            $lastNumber = (int) substr($lastBatch->batch_produksi_id, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }
}
