<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Kandang;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\LaporanHarian;
use App\Models\TrayHistory;
use App\Models\BatchProduksi;
use App\Models\FeedVitaminItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

/**
 * ==========================================
 * Controller : ProduksiController
 * Deskripsi  : Mengatur seluruh siklus produksi mulai input batch, laporan harian, tray tracking, hingga rekomendasi performa.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /**
         * Menampilkan daftar produksi dengan filter pencarian dan paginasi.
         * Menyusun metrik ringkasan yang diperlukan untuk tampilan index produksi.
         */
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = Produksi::with(['kandang', 'penetasan', 'pembesaran']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('batch_produksi_id', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('kandang', function($q) use ($search) {
                      $q->where('nama_kandang', 'like', "%{$search}%");
                  });
            });
        }

        if ($perPage === 'all') {
            $produksi = $query->orderBy('tanggal_mulai', 'desc')->get();
            $produksi = new \Illuminate\Pagination\LengthAwarePaginator(
                $produksi,
                $produksi->count(),
                $produksi->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $produksi = $query->orderBy('tanggal_mulai', 'desc')->paginate($perPage);
        }

        return view('admin.pages.produksi.index-produksi', compact('produksi'));
    }

    /**
     * Delete a specific laporan_harian entry (history only).
     */
    public function destroyLaporan(Produksi $produksi, LaporanHarian $laporan)
    {
        try {
            $justNotes = $laporan->catatan_kejadian &&
                !$laporan->produksi_telur &&
                !$laporan->konsumsi_pakan_kg &&
                !$laporan->vitamin_terpakai &&
                !$laporan->jumlah_kematian;

            if ($justNotes) {
                $laporan->catatan_kejadian = null;
            }

            $laporan->tampilkan_di_histori = false;
            $laporan->save();

            $this->syncTelurTurunanFromPuyuh($produksi);

            return redirect()->route('admin.produksi.show', $produksi->id)
                ->with('success', 'Histori berhasil disembunyikan tanpa mengubah total KAI.');
        } catch (\Exception $e) {
            return redirect()->route('admin.produksi.show', $produksi->id)
                ->with('error', 'Gagal menghapus histori: ' . $e->getMessage());
        }
    }

    /**
     * Reset a laporan_harian entry: set produksi_telur/input_telur to 0 and update related fields.
     */
    public function resetLaporan(Request $request, Produksi $produksi, LaporanHarian $laporan)
    {
        try {
            $resetTab = $request->input('reset_tab', 'all');

            $resetTelur = $resetTab === 'telur' || $resetTab === 'all';
            $resetPakan = $resetTab === 'pakan' || $resetTab === 'all';
            $resetVitamin = $resetTab === 'vitamin' || $resetTab === 'all';
            $resetKematian = $resetTab === 'kematian' || $resetTab === 'all';
            $resetPenjualan = $resetTab === 'penjualan' || $resetTab === 'all';

            // Normalisasi angka agar increment/decrement tidak menghasilkan NULL
            $produksi->jumlah_indukan = (int) ($produksi->jumlah_indukan ?? 0);
            $produksi->jumlah_jantan = (int) ($produksi->jumlah_jantan ?? 0);
            $produksi->jumlah_betina = (int) ($produksi->jumlah_betina ?? 0);

            // Reset data produksi telur jika diminta
            if ($resetTelur && ($laporan->produksi_telur > 0 || (Schema::hasColumn('vf_laporan_harian', 'input_telur') && $laporan->input_telur > 0))) {
                $laporan->produksi_telur = 0;
                if (Schema::hasColumn('vf_laporan_harian', 'input_telur')) {
                    $laporan->input_telur = 0;
                }
                if (Schema::hasColumn('vf_laporan_harian', 'sisa_telur')) {
                    $laporan->sisa_telur = null;
                }
            }

            // Reset data konsumsi pakan jika diminta
            if ($resetPakan && $laporan->konsumsi_pakan_kg !== null) {
                $laporan->konsumsi_pakan_kg = 0;
                if (Schema::hasColumn('vf_laporan_harian', 'sisa_pakan_kg')) {
                    $laporan->sisa_pakan_kg = null;
                }
                if (Schema::hasColumn('vf_laporan_harian', 'harga_pakan_per_kg')) {
                    $laporan->harga_pakan_per_kg = null;
                }
                if (Schema::hasColumn('vf_laporan_harian', 'biaya_pakan_harian')) {
                    $laporan->biaya_pakan_harian = null;
                }
            }

            // Reset vitamin data jika diminta
            if ($resetVitamin && $laporan->vitamin_terpakai !== null) {
                $laporan->vitamin_terpakai = 0;
                if (Schema::hasColumn('vf_laporan_harian', 'sisa_vitamin_liter')) {
                    $laporan->sisa_vitamin_liter = null;
                }
                if (Schema::hasColumn('vf_laporan_harian', 'harga_vitamin_per_liter')) {
                    $laporan->harga_vitamin_per_liter = null;
                }
                if (Schema::hasColumn('vf_laporan_harian', 'biaya_vitamin_harian')) {
                    $laporan->biaya_vitamin_harian = null;
                }
            }

            // Reset kematian jika diminta
            $jumlahKematianSebelumReset = 0;
            $jenisKelaminKematianSebelumReset = null;
            if ($resetKematian) {
                $jumlahKematianSebelumReset = $laporan->jumlah_kematian ?? 0;
                $jenisKelaminKematianSebelumReset = $laporan->jenis_kelamin_kematian ? strtolower((string) $laporan->jenis_kelamin_kematian) : null;
                if ($jumlahKematianSebelumReset > 0) {
                    $laporan->jumlah_kematian = 0;
                    $laporan->jenis_kelamin_kematian = null;
                    $laporan->keterangan_kematian = null;
                }
            }

            // Reset penjualan jika diminta
            $penjualanPuyuhSebelumReset = 0;
            $jenisKelaminPenjualanSebelumReset = null;
            $campuranPenjualanSebelumReset = null;
            if ($resetPenjualan) {
                $penjualanPuyuhSebelumReset = $laporan->penjualan_puyuh_ekor ?? 0;
                $jenisKelaminPenjualanSebelumReset = $laporan->jenis_kelamin_penjualan ? strtolower((string) $laporan->jenis_kelamin_penjualan) : null;
                $campuranPenjualanSebelumReset = $this->parsePenjualanCampuran($jenisKelaminPenjualanSebelumReset);

                // Fallback: jika jenis_kelamin_penjualan kosong, coba infer dari breakdown columns
                if (!$campuranPenjualanSebelumReset && !$jenisKelaminPenjualanSebelumReset) {
                    $jantanBreakdown = $laporan->penjualan_puyuh_jantan ?? 0;
                    $betinaBreakdown = $laporan->penjualan_puyuh_betina ?? 0;
                    if ($jantanBreakdown > 0 && $betinaBreakdown > 0) {
                        $campuranPenjualanSebelumReset = ['jantan' => $jantanBreakdown, 'betina' => $betinaBreakdown];
                        $jenisKelaminPenjualanSebelumReset = 'campuran'; // untuk logic selanjutnya
                    } elseif ($jantanBreakdown > 0) {
                        $jenisKelaminPenjualanSebelumReset = 'jantan';
                    } elseif ($betinaBreakdown > 0) {
                        $jenisKelaminPenjualanSebelumReset = 'betina';
                    }
                }

                if (($laporan->penjualan_telur_butir ?? 0) > 0 || $penjualanPuyuhSebelumReset > 0) {
                    $laporan->penjualan_telur_butir = 0;
                    $laporan->penjualan_puyuh_ekor = 0;
                    $laporan->pendapatan_harian = 0;
                    $laporan->tray_penjualan_id = null;
                    $laporan->nama_tray_penjualan = null;
                    $laporan->harga_per_butir = null;

                    if (Schema::hasColumn('vf_laporan_harian', 'jenis_kelamin_penjualan')) {
                        $laporan->jenis_kelamin_penjualan = null;
                    }
                    if (Schema::hasColumn('vf_laporan_harian', 'penjualan_puyuh_jantan')) {
                        $laporan->penjualan_puyuh_jantan = 0;
                    }
                    if (Schema::hasColumn('vf_laporan_harian', 'penjualan_puyuh_betina')) {
                        $laporan->penjualan_puyuh_betina = 0;
                    }
                }
            }

            // Tentukan apakah masih ada data yang perlu ditampilkan di histori
            $hasVisibleData = (
                ($laporan->produksi_telur ?? 0) > 0 ||
                ($laporan->input_telur ?? 0) > 0 ||
                ($laporan->penjualan_telur_butir ?? 0) > 0 ||
                ($laporan->penjualan_puyuh_ekor ?? 0) > 0 ||
                ($laporan->konsumsi_pakan_kg ?? 0) > 0 ||
                ($laporan->vitamin_terpakai ?? 0) > 0 ||
                ($laporan->jumlah_kematian ?? 0) > 0 ||
                !empty($laporan->catatan_kejadian)
            );

            $laporan->tampilkan_di_histori = $hasVisibleData;
            $laporan->save();

            // Normalisasi nilai null ke 0 agar increment tidak menghasilkan NULL
            $produksi->jumlah_indukan = (int) ($produksi->jumlah_indukan ?? 0);
            $produksi->jumlah_jantan = (int) ($produksi->jumlah_jantan ?? 0);
            $produksi->jumlah_betina = (int) ($produksi->jumlah_betina ?? 0);

            // Jika ada kematian yang direset, tambah kembali ke populasi produksi
            if ($jumlahKematianSebelumReset > 0 && $produksi->tipe_produksi !== 'telur') {
                $produksi->increment('jumlah_indukan', $jumlahKematianSebelumReset);

                $totalJantan = max(0, $produksi->jumlah_jantan ?? 0);
                $totalBetina = max(0, $produksi->jumlah_betina ?? 0);
                $totalPopulasi = $totalJantan + $totalBetina;

                if ($jenisKelaminKematianSebelumReset === 'jantan') {
                    $produksi->increment('jumlah_jantan', $jumlahKematianSebelumReset);
                } elseif ($jenisKelaminKematianSebelumReset === 'betina') {
                    $produksi->increment('jumlah_betina', $jumlahKematianSebelumReset);
                } else {
                    // campuran/tidak tercatat: pakai rasio jika ada; jika tidak ada rasio, default ke betina agar tidak membelah jantan/betina secara acak
                    if ($totalPopulasi > 0) {
                        $tambahJantan = (int) round($jumlahKematianSebelumReset * ($totalJantan / $totalPopulasi));
                        $tambahBetina = $jumlahKematianSebelumReset - $tambahJantan;
                        $produksi->increment('jumlah_jantan', $tambahJantan);
                        $produksi->increment('jumlah_betina', $tambahBetina);
                    } else {
                        $produksi->increment('jumlah_betina', $jumlahKematianSebelumReset);
                    }
                }
            }

            // Jika ada penjualan puyuh yang direset, tambah kembali ke populasi produksi
            if ($penjualanPuyuhSebelumReset > 0 && $produksi->tipe_produksi !== 'telur') {
                $produksi->increment('jumlah_indukan', $penjualanPuyuhSebelumReset);

                $totalJantan = max(0, $produksi->jumlah_jantan ?? 0);
                $totalBetina = max(0, $produksi->jumlah_betina ?? 0);
                $totalPopulasi = $totalJantan + $totalBetina;

                if ($jenisKelaminPenjualanSebelumReset === 'jantan') {
                    $produksi->increment('jumlah_jantan', $penjualanPuyuhSebelumReset);
                } elseif ($jenisKelaminPenjualanSebelumReset === 'betina') {
                    $produksi->increment('jumlah_betina', $penjualanPuyuhSebelumReset);
                } elseif ($campuranPenjualanSebelumReset) {
                    $produksi->increment('jumlah_jantan', $campuranPenjualanSebelumReset['jantan']);
                    $produksi->increment('jumlah_betina', $campuranPenjualanSebelumReset['betina']);
                } else {
                    // Jika gender tidak tercatat: jika stok jantan 0, kembalikan seluruhnya ke betina; jika stok betina 0, kembalikan ke jantan; jika keduanya ada, pakai rasio
                    if ($totalJantan === 0 && $totalBetina > 0) {
                        $produksi->increment('jumlah_betina', $penjualanPuyuhSebelumReset);
                    } elseif ($totalBetina === 0 && $totalJantan > 0) {
                        $produksi->increment('jumlah_jantan', $penjualanPuyuhSebelumReset);
                    } elseif ($totalPopulasi > 0) {
                        $tambahJantan = (int) round($penjualanPuyuhSebelumReset * ($totalJantan / $totalPopulasi));
                        $tambahBetina = $penjualanPuyuhSebelumReset - $tambahJantan;
                        $produksi->increment('jumlah_jantan', $tambahJantan);
                        $produksi->increment('jumlah_betina', $tambahBetina);
                    } else {
                        $produksi->increment('jumlah_betina', $penjualanPuyuhSebelumReset);
                    }
                }
            }

            // Simpan perubahan populasi setelah penambahan akibat reset
            if ($jumlahKematianSebelumReset > 0 || $penjualanPuyuhSebelumReset > 0) {
                $produksi->save();
            }

            $this->syncTelurTurunanFromPuyuh($produksi);

            // Sync jumlah_indukan to ensure it matches jumlah_jantan + jumlah_betina
            $produksi->jumlah_indukan = ($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0);
            $produksi->save();

            // Sinkronisasi status kandang jika kapasitas telah penuh atau tersedia kembali
            Kandang::find($produksi->kandang_id)?->syncMaintenanceStatus();

            return redirect()->route('admin.produksi.show', $produksi->id)
                ->with('success', 'Histori berhasil di-reset. Menu KAI akan otomatis ter-update.');
        } catch (\Exception $e) {
            return redirect()->route('admin.produksi.show', $produksi->id)
                ->with('error', 'Gagal mereset histori: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kandangList = Kandang::query()
            ->typeIs('produksi')
            ->statusIn(['aktif', 'maintenance', 'penuh'])
            ->orderBy('nama_kandang')
            ->get();
        
        // Dapatkan pembesaran dengan stok breeding yang tersedia dan muat relasi kandang
        // Hanya dapat pembesaran yang telah selesai dengan stok tersedia
        $pembesaranList = Pembesaran::with('kandang')
            ->whereRaw("LOWER(COALESCE(status_batch, '')) = ?", ['selesai'])
            ->whereRaw('(COALESCE(jumlah_siap, 0) - COALESCE(indukan_ditransfer, 0)) > 0')
            ->orderBy('tanggal_siap', 'desc')
            ->get();

        $produksiSumberList = $this->loadProduksiSumberList();
        
        // Set default jenis_input based on available data
        $defaultJenisInput = 'manual';
        if ($pembesaranList->isNotEmpty()) {
            $defaultJenisInput = 'dari_pembesaran';
        } elseif ($produksiSumberList->isNotEmpty()) {
            $defaultJenisInput = 'dari_produksi';
        }
        
        return view('admin.pages.produksi.create-produksi', compact('kandangList', 'pembesaranList', 'produksiSumberList', 'defaultJenisInput'));
    }

    /**
     * Parse string jenis_kelamin_penjualan yang menyimpan breakdown campuran.
     * Format yang didukung:
     * - campuran:jantan=5;betina=7
     * - campuran:5:7 (legacy fallback)
     */
    protected function parsePenjualanCampuran($value): ?array
    {
        if (!is_string($value)) {
            return null;
        }

        $value = strtolower(trim($value));
        if (!Str::startsWith($value, 'campuran')) {
            return null;
        }

        $jantan = null;
        $betina = null;

        if (preg_match('/jantan\s*=\s*(\d+)/', $value, $matchJantan)) {
            $jantan = (int) $matchJantan[1];
        }
        if (preg_match('/betina\s*=\s*(\d+)/', $value, $matchBetina)) {
            $betina = (int) $matchBetina[1];
        }

        // Legacy pattern campuran:5:7
        if (($jantan === null || $betina === null) && preg_match('/campuran\s*:?\s*(\d+)\s*[:|,]\s*(\d+)/', $value, $legacyMatch)) {
            $jantan = $jantan === null ? (int) $legacyMatch[1] : $jantan;
            $betina = $betina === null ? (int) $legacyMatch[2] : $betina;
        }

        if ($jantan === null && $betina === null) {
            return null;
        }

        return [
            'jantan' => max(0, (int) ($jantan ?? 0)),
            'betina' => max(0, (int) ($betina ?? 0)),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Store original fokus_manual before mapping
        $fokusManual = $request->get('fokus_manual');
        
        // Map form field names to database field names
        $mappedData = $request->all();
        
        // Map jumlah_burung to jumlah_indukan
        if (isset($mappedData['jumlah_burung'])) {
            $mappedData['jumlah_indukan'] = $mappedData['jumlah_burung'];
            unset($mappedData['jumlah_burung']);
        }
        
        // Map umur_burung to umur_mulai_produksi
        if (isset($mappedData['umur_burung'])) {
            $mappedData['umur_mulai_produksi'] = $mappedData['umur_burung'];
            unset($mappedData['umur_burung']);
        }
        
        // Map berat_rata_burung to berat_rata_rata
        if (isset($mappedData['berat_rata_burung'])) {
            $mappedData['berat_rata_rata'] = $mappedData['berat_rata_burung'];
            unset($mappedData['berat_rata_burung']);
        }
        
        // Use mapped data directly for validation
        $request->merge($mappedData);

        // Dynamic validation based on jenis_input and fokus_manual
        $rules = [
            'kandang_id' => 'required|exists:vf_kandang,id',
            'jenis_input' => 'required|in:manual,dari_pembesaran,dari_penetasan,dari_produksi',
            'batch_produksi_id' => 'nullable|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,tidak_aktif',
            'catatan' => 'nullable|string',
            'harga_per_pcs' => 'nullable|numeric|min:0',
            'produksi_sumber_id' => 'nullable|exists:vf_produksi,id',
        ];

        $jenisInput = $mappedData['jenis_input'] ?? 'manual';
        $fokusManual = $mappedData['fokus_manual'] ?? 'burung'; // default to burung if not set

        if ($jenisInput === 'manual') {
            if ($fokusManual === 'burung') {
                $rules = array_merge($rules, [
                    'jumlah_indukan' => 'required|integer|min:1',
                    'jenis_kelamin' => 'required|in:jantan,betina,campuran',
                    'umur_mulai_produksi' => 'required|integer|min:1',
                    'berat_rata_rata' => 'required|numeric|min:0',
                    'jumlah_jantan' => 'nullable|integer|min:0',
                    'jumlah_betina' => 'nullable|integer|min:0',
                ]);
            } elseif ($fokusManual === 'telur') {
                $rules = array_merge($rules, [
                    'jumlah_telur' => 'required|integer|min:1',
                    'persentase_fertil' => 'nullable|numeric|min:0|max:100',
                    'berat_rata_telur' => 'nullable|numeric|min:0',
                ]);
            }
        } elseif ($jenisInput === 'dari_pembesaran') {
            $rules = array_merge($rules, [
                'pembesaran_id' => 'required|exists:vf_pembesaran,id',
                'jumlah_indukan' => 'required|integer|min:1',
                'jenis_kelamin' => 'nullable|in:jantan,betina,campuran',
                'jumlah_jantan' => 'nullable|integer|min:0',
                'jumlah_betina' => 'nullable|integer|min:0',
            ]);
        } elseif ($jenisInput === 'dari_penetasan') {
            $rules = array_merge($rules, [
                'penetasan_id' => 'required|exists:vf_penetasan,id',
                'jumlah_telur' => 'required|integer|min:1',
                'berat_rata_telur' => 'nullable|numeric|min:0',
            ]);
        } elseif ($jenisInput === 'dari_produksi') {
            $rules = array_merge($rules, [
                'produksi_sumber_id' => 'required|exists:vf_produksi,id',
                'jumlah_telur' => 'nullable|integer|min:0',
                'berat_rata_telur' => 'nullable|numeric|min:0',
            ]);
        }

        $validated = $request->validate($rules, [
            'umur_mulai_produksi.required' => 'Field umur mulai produksi wajib diisi.',
            'berat_rata_rata.required' => 'Field berat rata-rata wajib diisi.',
            'jumlah_jantan.min' => 'Jumlah jantan tidak boleh negatif',
            'jumlah_betina.min' => 'Jumlah betina tidak boleh negatif',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
        ]);

        $jumlahIndukan = array_key_exists('jumlah_indukan', $validated)
            ? (int) $validated['jumlah_indukan']
            : null;

        if ($jumlahIndukan !== null && $jumlahIndukan > 0) {
            $kandang = Kandang::findOrFail($validated['kandang_id']);
            $kapasitasTersisa = $kandang->kapasitas_tersisa;

            if ($kapasitasTersisa <= 0) {
                return back()->withInput()->withErrors([
                    'kandang_id' => sprintf(
                        'Kandang %s sudah penuh. Pilih kandang lain atau selesaikan batch aktif terlebih dahulu.',
                        $kandang->nama_kandang ?? ('#' . $kandang->id)
                    ),
                ]);
            }

            if ($jumlahIndukan > $kapasitasTersisa) {
                return back()->withInput()->withErrors([
                    'jumlah_indukan' => sprintf(
                        'Jumlah indukan (%s) melebihi sisa kapasitas %s pada kandang %s.',
                        number_format($jumlahIndukan),
                        number_format($kapasitasTersisa),
                        $kandang->nama_kandang ?? ('#' . $kandang->id)
                    ),
                ]);
            }
        }

        // Custom validation for campuran gender (only for burung production)
        if (($jenisInput === 'manual' && $fokusManual === 'burung') || ($jenisInput === 'dari_pembesaran' && !empty($mappedData['jenis_kelamin']))) {
            $jenisKelamin = $mappedData['jenis_kelamin'] ?? '';
            $jumlahJantan = isset($mappedData['jumlah_jantan']) ? (int)$mappedData['jumlah_jantan'] : 0;
            $jumlahBetina = isset($mappedData['jumlah_betina']) ? (int)$mappedData['jumlah_betina'] : 0;
            $totalBurung = isset($mappedData['jumlah_burung'])
                ? (int)$mappedData['jumlah_burung']
                : (isset($mappedData['jumlah_indukan']) ? (int)$mappedData['jumlah_indukan'] : 0);

            if ($jenisKelamin === 'jantan') {
                // For jantan, jumlah_betina must be 0 and jumlah_jantan should equal total_burung
                if ($jumlahBetina > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jenis_kelamin' => 'Untuk jenis kelamin jantan, jumlah betina harus 0']);
                }
                // If jumlah_jantan is filled, it should match total_burung
                if ($jumlahJantan > 0 && $jumlahJantan != $totalBurung) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jumlah_jantan' => "Jumlah jantan harus sama dengan jumlah puyuh total ({$totalBurung})"]);
                }
            } elseif ($jenisKelamin === 'betina') {
                // For betina, jumlah_jantan must be 0 and jumlah_betina should equal total_burung
                if ($jumlahJantan > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jenis_kelamin' => 'Untuk jenis kelamin betina, jumlah jantan harus 0']);
                }
                // If jumlah_betina is filled, it should match total_burung
                if ($jumlahBetina > 0 && $jumlahBetina != $totalBurung) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jumlah_betina' => "Jumlah betina harus sama dengan jumlah puyuh total ({$totalBurung})"]);
                }
            } elseif ($jenisKelamin === 'campuran') {
                // For campuran, both fields must be filled and their sum must equal total_burung
                $totalCampuran = $jumlahJantan + $jumlahBetina;
                if ($jumlahJantan <= 0 || $jumlahBetina <= 0) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jenis_kelamin' => 'Untuk jenis kelamin campuran, jumlah jantan dan betina harus diisi']);
                }
                if ($totalCampuran != $totalBurung) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['jumlah_jantan' => "Jumlah Puyuh tidak sesuai ({$totalCampuran}) / ({$totalBurung})"]);
                }
            }
        }

        // Normalise male/female counts for manual burung input so downstream views get consistent data
        if ($jenisInput === 'manual' && $fokusManual === 'burung') {
            $jenisKelamin = $mappedData['jenis_kelamin'] ?? null;
            $totalIndukan = isset($validated['jumlah_indukan']) ? (int)$validated['jumlah_indukan'] : 0;

            if ($jenisKelamin === 'jantan') {
                $validated['jumlah_jantan'] = $totalIndukan;
                $validated['jumlah_betina'] = 0;
            } elseif ($jenisKelamin === 'betina') {
                $validated['jumlah_jantan'] = 0;
                $validated['jumlah_betina'] = $totalIndukan;
            } elseif ($jenisKelamin === 'campuran') {
                $validated['jumlah_jantan'] = isset($mappedData['jumlah_jantan']) ? (int)$mappedData['jumlah_jantan'] : null;
                $validated['jumlah_betina'] = isset($mappedData['jumlah_betina']) ? (int)$mappedData['jumlah_betina'] : null;
            }
        }

        // Set tipe_produksi based on input type
        if ($validated['jenis_input'] === 'manual') {
            if ($fokusManual) {
                $validated['tipe_produksi'] = $fokusManual === 'telur' ? 'telur' : 'puyuh';
            } else {
                $validated['tipe_produksi'] = 'puyuh'; // default for manual input
            }
        } elseif ($validated['jenis_input'] === 'dari_pembesaran') {
            $validated['tipe_produksi'] = 'puyuh';
        } elseif ($validated['jenis_input'] === 'dari_penetasan') {
            $validated['tipe_produksi'] = 'telur';
            // Set persentase_fertil to 100% for penetasan since eggs are already infertile
            $validated['persentase_fertil'] = 100;
        } elseif ($validated['jenis_input'] === 'dari_produksi') {
            $validated['tipe_produksi'] = 'telur';
            $validated['persentase_fertil'] = $validated['persentase_fertil'] ?? 100;
        }

        if (($validated['jenis_input'] ?? null) !== 'dari_penetasan') {
            $validated['penetasan_id'] = null;
        }

        if (($validated['jenis_input'] ?? null) !== 'dari_produksi') {
            $validated['produksi_sumber_id'] = null;
        }

        DB::beginTransaction();
        try {
            // Generate or resolve batch_produksi_id (FK to vf_batch_produksi)
            $providedBatch = $validated['batch_produksi_id'] ?? null;
            $jumlahAwal = $validated['jumlah_indukan'] ?? ($validated['jumlah_telur'] ?? 0);

            if ($providedBatch) {
                if (is_numeric($providedBatch)) {
                    $batch = BatchProduksi::find($providedBatch);
                    if (!$batch) {
                        throw new \Exception('Batch produksi tidak ditemukan.');
                    }
                    $validated['batch_produksi_id'] = $batch->id;
                } else {
                    $batch = BatchProduksi::firstOrCreate(
                        ['kode_batch' => $providedBatch],
                        [
                            'kandang_id' => $validated['kandang_id'],
                            'tanggal_mulai' => $validated['tanggal_mulai'],
                            'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
                            'jumlah_awal' => $jumlahAwal,
                            'jumlah_saat_ini' => $validated['tipe_produksi'] === 'puyuh' ? $jumlahAwal : null,
                            'fase' => 'layer',
                            'status' => 'aktif',
                            'catatan' => $validated['catatan'] ?? null,
                        ]
                    );
                    $validated['batch_produksi_id'] = $batch->id;
                }
            } else {
                $date = Carbon::parse($validated['tanggal_mulai']);
                $prefix = $validated['tipe_produksi'] === 'telur' ? 'TELUR-INF' : 'PROD-PUY';
                $count = BatchProduksi::whereDate('tanggal_mulai', $date)->count() + 1;
                $kodeBatch = sprintf('%s-%s-%04d', $prefix, $date->format('Ymd'), $count);

                $batch = BatchProduksi::create([
                    'kode_batch' => $kodeBatch,
                    'kandang_id' => $validated['kandang_id'],
                    'tanggal_mulai' => $validated['tanggal_mulai'],
                    'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
                    'jumlah_awal' => $jumlahAwal,
                    'jumlah_saat_ini' => $validated['tipe_produksi'] === 'puyuh' ? $jumlahAwal : null,
                    'fase' => 'layer',
                    'status' => 'aktif',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                $validated['batch_produksi_id'] = $batch->id;
            }

            // Handle transfer from pembesaran
            if ($validated['jenis_input'] === 'dari_pembesaran' && $validated['pembesaran_id']) {
                $pembesaran = Pembesaran::findOrFail($validated['pembesaran_id']);
                $jenisKelaminPembesaran = strtolower($pembesaran->jenis_kelamin ?? '');
                if (empty($validated['jenis_kelamin']) && $jenisKelaminPembesaran) {
                    $validated['jenis_kelamin'] = $jenisKelaminPembesaran;
                }
                $totalIndukan = isset($validated['jumlah_indukan']) ? (int)$validated['jumlah_indukan'] : 0;
                $currentJenisKelamin = $validated['jenis_kelamin'] ?? null;

                if ($totalIndukan > 0) {
                    if ($currentJenisKelamin === 'jantan') {
                        $validated['jumlah_jantan'] = $totalIndukan;
                        $validated['jumlah_betina'] = 0;
                    } elseif ($currentJenisKelamin === 'betina') {
                        $validated['jumlah_jantan'] = 0;
                        $validated['jumlah_betina'] = $totalIndukan;
                    } elseif ($currentJenisKelamin === 'campuran') {
                        $hasJantan = array_key_exists('jumlah_jantan', $validated) && $validated['jumlah_jantan'] !== null && $validated['jumlah_jantan'] !== '';
                        $hasBetina = array_key_exists('jumlah_betina', $validated) && $validated['jumlah_betina'] !== null && $validated['jumlah_betina'] !== '';

                        if (!$hasJantan) {
                            $validated['jumlah_jantan'] = (int) ceil($totalIndukan / 2);
                        } else {
                            $validated['jumlah_jantan'] = (int) $validated['jumlah_jantan'];
                        }

                        if (!$hasBetina) {
                            $validated['jumlah_betina'] = $totalIndukan - $validated['jumlah_jantan'];
                        } else {
                            $validated['jumlah_betina'] = (int) $validated['jumlah_betina'];
                        }
                    }
                }
                
                // Periksa stok tersedia
                $tersedia = $pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0);
                if ($validated['jumlah_indukan'] > $tersedia) {
                    throw new \Exception("Jumlah indukan melebihi stok tersedia ({$tersedia})");
                }

                // Update pembesaran
                $pembesaran->increment('indukan_ditransfer', $validated['jumlah_indukan']);
                
                // Periksa apakah semua stok telah dipindahkan
                if ($pembesaran->indukan_ditransfer >= $pembesaran->jumlah_siap) {
                    $pembesaran->update(['status_batch' => 'selesai']);
                }
            }

            // Handle transfer from penetasan (infertile eggs)
            if ($validated['jenis_input'] === 'dari_penetasan' && $validated['penetasan_id']) {
                $penetasan = Penetasan::findOrFail($validated['penetasan_id']);
                
                // Periksa stok tersedia
                $tersedia = $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0);
                if ($validated['jumlah_telur'] > $tersedia) {
                    throw new \Exception("Jumlah telur melebihi stok tersedia ({$tersedia})");
                }

                // Update penetasan
                $penetasan->increment('telur_infertil_ditransfer', $validated['jumlah_telur']);
            }

            $sumberProduksi = null;
            if ($validated['jenis_input'] === 'dari_produksi' && $validated['produksi_sumber_id']) {
                $sumberProduksi = Produksi::findOrFail($validated['produksi_sumber_id']);

                if ($sumberProduksi->tipe_produksi !== 'puyuh') {
                    throw new \Exception('Sumber produksi tidak valid untuk transfer telur.');
                }

                $stats = $this->attachTelurStatsToProduksi(collect([$sumberProduksi]))->first();
                $tersediaTelur = (int) ($stats->total_telur_tersedia ?? 0);

                if (($validated['jumlah_telur'] ?? 0) > $tersediaTelur) {
                    throw new \Exception("Jumlah telur melebihi stok tersedia ({$tersediaTelur})");
                }

                $validated['jumlah_telur'] = $tersediaTelur;
            }

            Log::info('Creating production record', $validated);

            // Create produksi record
            $produksi = Produksi::create($validated);
            Kandang::find($validated['kandang_id'])?->syncMaintenanceStatus();
            if ($sumberProduksi) {
                $this->syncTelurTurunanFromPuyuh($sumberProduksi);
            }
            
            Log::info('Production record created with ID: ' . $produksi->id);

            DB::commit();
            Log::info('Transaction committed successfully');
            $redirectUrl = route('admin.produksi');
            Log::info('Redirecting to: ' . $redirectUrl);
            $produksi->loadMissing(['batchProduksi', 'pembesaran', 'penetasan']);
            $message = sprintf(
                'Produksi %s berhasil ditambahkan.',
                $produksi->batch_label
            );

            return redirect()->route('admin.produksi')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production creation failed: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produksi $produksi)
    {
        $produksi->load(['kandang', 'penetasan', 'pembesaran', 'produksiSumber']);

        $laporanHarian = collect();

        if (!empty($produksi->batch_produksi_id)) {
            $laporanHarian = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
                ->orderByDesc('tanggal')
                ->orderByDesc('dibuat_pada')
                ->get();
        }

        $soldTrayIds = $laporanHarian->whereNotNull('tray_penjualan_id')
            ->pluck('tray_penjualan_id')
            ->filter()
            ->unique()
            ->values();

        $soldTrayNames = $laporanHarian->whereNotNull('nama_tray_penjualan')
            ->pluck('nama_tray_penjualan')
            ->filter()
            ->map(function ($name) {
                $stringable = Str::of($name)->trim();
                if ($stringable->isEmpty()) {
                    return null;
                }
                return $stringable->lower()->value();
            })
            ->filter()
            ->unique()
            ->values();

        $trayHistories = collect();
        if (Schema::hasTable('vf_tray_histories')) {
            $trayHistories = $produksi->trayHistories()
                ->with('pengguna')
                ->orderByDesc('created_at')
                ->limit(150)
                ->get();
        }

        $eggsPerTray = (int) config('produksi.eggs_per_tray', 100);

        $summary = [
            'total_telur' => $laporanHarian->sum('produksi_telur'),
            'total_kematian' => $laporanHarian->sum('jumlah_kematian'),
            'total_penjualan_telur' => $laporanHarian->sum('penjualan_telur_butir'),
            'total_penjualan_puyuh' => $laporanHarian->sum('penjualan_puyuh_ekor'),
            'total_pendapatan' => $laporanHarian->sum('pendapatan_harian'),
            'total_pakan_kg' => $laporanHarian->sum('konsumsi_pakan_kg'),
            'total_vitamin_liter' => $laporanHarian->sum('vitamin_terpakai'),
            'total_biaya_pakan' => $laporanHarian->sum('biaya_pakan_harian'),
            'total_biaya_vitamin' => $laporanHarian->sum('biaya_vitamin_harian'),
            'last_sisa_pakan' => optional($laporanHarian->first())->sisa_pakan_kg,
            'last_sisa_telur' => optional($laporanHarian->first())->sisa_telur,
            'laporan_count' => $laporanHarian->count(),
        ];

        $summary['total_pengeluaran'] = ($summary['total_biaya_pakan'] ?? 0) + ($summary['total_biaya_vitamin'] ?? 0);

        $summary['total_telur_rusak'] = $laporanHarian->sum('telur_rusak');
        $summary['eggs_per_tray'] = max($eggsPerTray, 1);

        // Hitung total telur dari tray yang belum terjual saja (aktif/hijau)
        $totalTelurAktif = $laporanHarian
            ->whereNotIn('id', $soldTrayIds)
            ->whereNotNull('nama_tray')
            ->sum('produksi_telur');

        // Hitung jumlah tray aktif berdasarkan jumlah entries, bukan total telur
        $activeTrayCount = $laporanHarian
            ->whereNotIn('id', $soldTrayIds)
            ->whereNotNull('nama_tray')
            ->where('produksi_telur', '>', 0)
            ->count();

        $summary['total_tray'] = $activeTrayCount;

        // Hitung total telur yang sudah pernah dimasukkan ke tray (termasuk yang sudah dihapus)
        // Telur yang sudah masuk tray tidak bisa kembali ke sisa telur
        $totalTelurCommitted = 0;
        if (Schema::hasTable('vf_tray_histories')) {
            $totalTelurCommitted = $produksi->trayHistories()
                ->where('action', 'created')
                ->sum('jumlah_telur');
        }

        // Hitung sisa telur: total telur awal produksi - telur yang sudah dimasukkan ke tray (termasuk yang sudah dihapus)
        $totalTelurAwal = $produksi->jumlah_telur ?? 0;
        $summary['sisa_telur'] = max(0, $totalTelurAwal - $totalTelurCommitted);

        // Death impact per gender to update population and ratio in KAI
        $deathByGender = [
            'jantan' => $laporanHarian
                ->where('jenis_kelamin_kematian', 'jantan')
                ->sum('jumlah_kematian'),
            'betina' => $laporanHarian
                ->where('jenis_kelamin_kematian', 'betina')
                ->sum('jumlah_kematian'),
            'campuran' => $laporanHarian
                ->where('jenis_kelamin_kematian', 'campuran')
                ->sum('jumlah_kematian'),
        ];

        $salesByGender = [
            'jantan' => $laporanHarian
                ->where('jenis_kelamin_penjualan', 'jantan')
                ->sum('penjualan_puyuh_ekor') +
                $laporanHarian
                ->where('jenis_kelamin_penjualan', 'campuran')
                ->sum('penjualan_puyuh_jantan'),
            'betina' => $laporanHarian
                ->where('jenis_kelamin_penjualan', 'betina')
                ->sum('penjualan_puyuh_ekor') +
                $laporanHarian
                ->where('jenis_kelamin_penjualan', 'campuran')
                ->sum('penjualan_puyuh_betina'),
        ];

        $initialMale = $produksi->jumlah_jantan;
        $initialFemale = $produksi->jumlah_betina;
        $jenisKelaminProduksi = strtolower($produksi->jenis_kelamin ?? '');

        if ($jenisKelaminProduksi === 'jantan' && $initialMale === null) {
            $initialMale = $produksi->jumlah_indukan ?? 0;
            $initialFemale = $initialFemale ?? 0;
        }

        if ($jenisKelaminProduksi === 'betina' && $initialFemale === null) {
            $initialFemale = $produksi->jumlah_indukan ?? 0;
            $initialMale = $initialMale ?? 0;
        }

        if ($jenisKelaminProduksi === 'campuran') {
            $fallbackTotal = $produksi->jumlah_indukan ?? 0;

            if ($initialMale === null && $initialFemale === null) {
                $initialMale = (int) floor($fallbackTotal / 2);
                $initialFemale = max($fallbackTotal - $initialMale, 0);
            } elseif ($initialMale === null) {
                $initialMale = max($fallbackTotal - $initialFemale, 0);
            } elseif ($initialFemale === null) {
                $initialFemale = max($fallbackTotal - $initialMale, 0);
            }
        }

        $initialMale = $initialMale ?? 0;
        $initialFemale = $initialFemale ?? 0;

        $currentMale = max($initialMale - $deathByGender['jantan'] - $salesByGender['jantan'], 0);
        $currentFemale = max($initialFemale - $deathByGender['betina'] - $salesByGender['betina'], 0);

        // Distribute campuran deaths proportionally
        if ($deathByGender['campuran'] > 0) {
            $totalCurrent = $currentMale + $currentFemale;
            if ($totalCurrent > 0) {
                $campuranMaleDeaths = (int) round($deathByGender['campuran'] * ($currentMale / $totalCurrent));
                $campuranFemaleDeaths = $deathByGender['campuran'] - $campuranMaleDeaths;
                $currentMale = max($currentMale - $campuranMaleDeaths, 0);
                $currentFemale = max($currentFemale - $campuranFemaleDeaths, 0);
            } else {
                // If no current population, distribute evenly
                $campuranMaleDeaths = (int) ceil($deathByGender['campuran'] / 2);
                $campuranFemaleDeaths = $deathByGender['campuran'] - $campuranMaleDeaths;
                $currentMale = max($currentMale - $campuranMaleDeaths, 0);
                $currentFemale = max($currentFemale - $campuranFemaleDeaths, 0);
            }
        }

        $currentPopulationFromGender = max($currentMale + $currentFemale, 0);

        if ($currentPopulationFromGender === 0 && ($produksi->jumlah_indukan ?? 0) > 0) {
            $currentPopulationFromGender = max(
                ($produksi->jumlah_indukan ?? 0)
                - $summary['total_kematian']
                - ($summary['total_penjualan_puyuh'] ?? 0),
                0
            );
        }

        $summary['total_kematian_jantan'] = $deathByGender['jantan'];
        $summary['total_kematian_betina'] = $deathByGender['betina'];
        $summary['total_kematian_campuran'] = $deathByGender['campuran'];
        $summary['initial_jantan'] = $initialMale;
        $summary['initial_betina'] = $initialFemale;
        $summary['current_jantan'] = $currentMale;
        $summary['current_betina'] = $currentFemale;
        $summary['total_penjualan_jantan'] = $salesByGender['jantan'];
        $summary['total_penjualan_betina'] = $salesByGender['betina'];
        $summary['current_population'] = $currentPopulationFromGender;

        $existingEntriesByTab = [
            'telur' => [],
            'tray' => [],
            'penjualan' => [],
            'pakan' => [],
            'vitamin' => [],
            'kematian' => [],
            'laporan' => [],
        ];

        $laporanHarian->each(function ($laporanItem) use (&$existingEntriesByTab) {
            if (!$laporanItem->tanggal) {
                return;
            }

            $dateKey = $laporanItem->tanggal->format('Y-m-d');

            if (($laporanItem->produksi_telur ?? 0) > 0) {
                $existingEntriesByTab['telur'][$dateKey] = true;
            }

            if (($laporanItem->konsumsi_pakan_kg ?? 0) > 0) {
                $existingEntriesByTab['pakan'][$dateKey] = true;
            }

            if (($laporanItem->vitamin_terpakai ?? 0) > 0) {
                $existingEntriesByTab['vitamin'][$dateKey] = true;
            }

            if (($laporanItem->jumlah_kematian ?? 0) > 0) {
                $existingEntriesByTab['kematian'][$dateKey] = true;
            }

            if (
                $laporanItem->penjualan_telur_butir !== null ||
                $laporanItem->penjualan_puyuh_ekor !== null ||
                $laporanItem->pendapatan_harian !== null
            ) {
                $existingEntriesByTab['penjualan'][$dateKey] = true;
            }

            if (!empty($laporanItem->catatan_kejadian)) {
                $existingEntriesByTab['laporan'][$dateKey] = true;
            }
        });

        $latestLaporan = $laporanHarian->first();
        $todayLaporan = $laporanHarian->firstWhere('tanggal', Carbon::today()->toDateString());

        $pencatatanProduksi = $produksi->pencatatanProduksi()
            ->orderByDesc('tanggal')
            ->get();

        $summary['total_telur_awal'] = $produksi->jumlah_telur ?? 0;

        $historyClearRoute = false; // Route not implemented yet

        $view = $produksi->tipe_produksi === 'telur'
            ? 'admin.pages.produksi.show-telur'
            : 'admin.pages.produksi.show-puyuh';

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

        return view($view, compact(
            'produksi',
            'laporanHarian',
            'trayHistories',
            'summary',
            'latestLaporan',
            'todayLaporan',
            'pencatatanProduksi',
            'historyClearRoute',
            'existingEntriesByTab',
            'soldTrayIds',
            'soldTrayNames',
            'feedOptions',
            'vitaminOptions'
        ));
    }

    /**
     * Store or update daily report for a production batch.
     */
    public function storeDailyReport(Request $request, Produksi $produksi)
    {
        if (empty($produksi->batch_produksi_id)) {
            return redirect()->back()->with('error', 'Produksi ini belum memiliki kode batch. Tambahkan batch terlebih dahulu sebelum mencatat laporan.');
        }

        $isTelurBatch = $produksi->tipe_produksi === 'telur';

        // Normalisasi angka agar increment/decrement tidak menghasilkan NULL
        $produksi->jumlah_indukan = (int) ($produksi->jumlah_indukan ?? 0);
        $produksi->jumlah_jantan = (int) ($produksi->jumlah_jantan ?? 0);
        $produksi->jumlah_betina = (int) ($produksi->jumlah_betina ?? 0);

        // Handle comma decimal separator for pakan fields before validation
        $requestData = $request->all();
        if (isset($requestData['konsumsi_pakan_kg']) && is_string($requestData['konsumsi_pakan_kg'])) {
            $requestData['konsumsi_pakan_kg'] = str_replace(',', '.', $requestData['konsumsi_pakan_kg']);
        }
        if (isset($requestData['sisa_pakan_kg']) && is_string($requestData['sisa_pakan_kg'])) {
            $requestData['sisa_pakan_kg'] = str_replace(',', '.', $requestData['sisa_pakan_kg']);
        }
        if (isset($requestData['vitamin_terpakai']) && is_string($requestData['vitamin_terpakai'])) {
            $requestData['vitamin_terpakai'] = str_replace(',', '.', $requestData['vitamin_terpakai']);
        }
        if (isset($requestData['sisa_vitamin_liter']) && is_string($requestData['sisa_vitamin_liter'])) {
            $requestData['sisa_vitamin_liter'] = str_replace(',', '.', $requestData['sisa_vitamin_liter']);
        }
        if (isset($requestData['harga_pakan_per_kg']) && is_string($requestData['harga_pakan_per_kg'])) {
            $requestData['harga_pakan_per_kg'] = str_replace(',', '.', $requestData['harga_pakan_per_kg']);
        }
        if (isset($requestData['harga_vitamin_per_liter']) && is_string($requestData['harga_vitamin_per_liter'])) {
            $requestData['harga_vitamin_per_liter'] = str_replace(',', '.', $requestData['harga_vitamin_per_liter']);
        }

        // Normalisasi separator ribuan untuk field integer (misal: 2.500 -> 2500)
        $integerFields = [
            'penjualan_puyuh_ekor',
            'penjualan_puyuh_jantan',
            'penjualan_puyuh_betina',
            'jumlah_kematian',
            'jumlah_telur_terjual',
            'penjualan_telur_butir',
            'sisa_telur',
            'sisa_tray_lembar',
            'produksi_telur',
        ];

        foreach ($integerFields as $intField) {
            if (isset($requestData[$intField]) && is_string($requestData[$intField])) {
                $requestData[$intField] = str_replace(['.', ',', ' '], '', $requestData[$intField]);
            }
        }

        $request->merge($requestData);

        // Dapatkan tab aktif untuk menentukan field yang diperlukan
        $activeTab = $request->input('active_tab');
        $rules = [
            'tanggal' => 'required|date',
            'active_tab' => 'required|in:telur,penjualan,pakan,vitamin,kematian,laporan',
            'produksi_telur' => 'nullable|integer|min:0|max:100',
            'jumlah_kematian' => 'nullable|integer|min:1',
            'jenis_kelamin_kematian' => 'nullable|in:jantan,betina',
            'keterangan_kematian' => 'nullable|string|max:225',
            'konsumsi_pakan_kg' => 'nullable|numeric|min:0',
            'sisa_pakan_kg' => 'nullable|numeric|min:0',
            'harga_pakan_per_kg' => 'nullable|numeric|min:0',
            'sisa_tray_bal' => 'nullable|numeric|min:0',
            'sisa_tray_lembar' => 'nullable|integer|min:0',
            'sisa_vitamin_liter' => 'nullable|numeric|min:0',
            'vitamin_terpakai' => 'nullable|numeric|min:0',
            'harga_vitamin_per_liter' => 'nullable|numeric|min:0',
            'sisa_telur' => 'nullable|integer|min:0',
            'penjualan_telur_butir' => 'nullable|integer|min:0',
            'penjualan_puyuh_ekor' => 'nullable|integer|min:0',
            'jenis_kelamin_penjualan' => 'nullable|in:jantan,betina,campuran',
            'pendapatan_harian' => 'nullable|numeric|min:0',
            'tray_penjualan' => 'nullable|integer|exists:vf_laporan_harian,id',
            'jumlah_telur_terjual' => 'nullable|integer|min:1',
            'harga_penjualan' => 'nullable|numeric|min:0',
            'catatan_kejadian' => 'nullable|string|max:2500',
        ];

        if (Schema::hasTable('vf_feed_vitamin_items')) {
            $rules['feed_item_id'] = 'nullable|exists:vf_feed_vitamin_items,id';
            $rules['vitamin_item_id'] = 'nullable|exists:vf_feed_vitamin_items,id';
        }

        // Make the main field required based on active tab
        switch ($activeTab) {
            case 'telur':
                $rules['produksi_telur'] = 'required|integer|min:0';
                break;
            case 'penjualan':
                if ($isTelurBatch) {
                    $rules['tray_penjualan'] = 'required|integer|exists:vf_laporan_harian,id';
                    $rules['jumlah_telur_terjual'] = 'required|integer|min:1';
                    $rules['harga_penjualan'] = 'required|numeric|min:0';
                } else {
                    if (Schema::hasColumn('vf_laporan_harian', 'jenis_kelamin_penjualan')) {
                        $rules['jenis_kelamin_penjualan'] = 'required|in:jantan,betina,campuran';
                    }
                    $rules['penjualan_puyuh_ekor'] = 'nullable|integer|min:1';
                    if ($request->jenis_kelamin_penjualan === 'campuran') {
                        $rules['penjualan_puyuh_jantan'] = 'required|integer|min:0';
                        $rules['penjualan_puyuh_betina'] = 'required|integer|min:0';
                    } else {
                        $rules['penjualan_puyuh_jantan'] = 'nullable|integer|min:0';
                        $rules['penjualan_puyuh_betina'] = 'nullable|integer|min:0';
                    }
                    $rules['harga_penjualan'] = 'required|numeric|min:0';
                }
                break;
            case 'pakan':
                $rules['konsumsi_pakan_kg'] = 'required|numeric|min:0';
                break;
            case 'vitamin':
                $rules['vitamin_terpakai'] = 'required|numeric|min:0';
                break;
            case 'kematian':
                $rules['jumlah_kematian'] = 'required|integer|min:0';
                $rules['jenis_kelamin_kematian'] = 'required|in:jantan,betina';
                break;
            case 'laporan':
                $rules['catatan_kejadian'] = 'required|string|max:2500';
                break;
        }

        $validated = $request->validate($rules, [
            'tanggal.required' => 'Tanggal harus diisi.',
            'active_tab.required' => 'Tab aktif harus dipilih.',
            'active_tab.in' => 'Tab aktif tidak valid.',
            'produksi_telur.required' => 'Jumlah produksi telur harus diisi.',
            'produksi_telur.integer' => 'Jumlah produksi telur harus berupa angka bulat.',
            'produksi_telur.min' => 'Jumlah produksi telur tidak boleh negatif.',
            'konsumsi_pakan_kg.required' => 'Jumlah konsumsi pakan harus diisi.',
            'konsumsi_pakan_kg.numeric' => 'Jumlah konsumsi pakan harus berupa angka.',
            'konsumsi_pakan_kg.min' => 'Jumlah konsumsi pakan tidak boleh negatif.',
            'vitamin_terpakai.required' => 'Jumlah vitamin terpakai harus diisi.',
            'vitamin_terpakai.numeric' => 'Jumlah vitamin terpakai harus berupa angka.',
            'vitamin_terpakai.min' => 'Jumlah vitamin terpakai tidak boleh negatif.',
            'jumlah_kematian.required' => 'Jumlah kematian harus diisi.',
            'jumlah_kematian.integer' => 'Jumlah kematian harus berupa angka bulat.',
            'jumlah_kematian.min' => 'Jumlah kematian minimal 1 ekor.',
            'jenis_kelamin_kematian.required' => 'Jenis kelamin kematian harus dipilih.',
            'jenis_kelamin_kematian.in' => 'Jenis kelamin kematian tidak valid.',
            'keterangan_kematian.max' => 'Keterangan kematian maksimal 225 karakter.',
            'sisa_pakan_kg.numeric' => 'Sisa pakan harus berupa angka.',
            'sisa_pakan_kg.min' => 'Sisa pakan tidak boleh negatif.',
            'sisa_tray_bal.numeric' => 'Sisa tray bal harus berupa angka.',
            'sisa_tray_bal.min' => 'Sisa tray bal tidak boleh negatif.',
            'sisa_tray_lembar.integer' => 'Sisa tray lembar harus berupa angka bulat.',
            'sisa_tray_lembar.min' => 'Sisa tray lembar tidak boleh negatif.',
            'sisa_vitamin_liter.numeric' => 'Sisa vitamin liter harus berupa angka.',
            'sisa_vitamin_liter.min' => 'Sisa vitamin liter tidak boleh negatif.',
            'sisa_telur.integer' => 'Sisa telur harus berupa angka bulat.',
            'sisa_telur.min' => 'Sisa telur tidak boleh negatif.',
            'penjualan_telur_butir.integer' => 'Penjualan telur butir harus berupa angka bulat.',
            'penjualan_telur_butir.min' => 'Penjualan telur butir tidak boleh negatif.',
            'penjualan_puyuh_ekor.integer' => 'Penjualan puyuh ekor harus berupa angka bulat.',
            'penjualan_puyuh_ekor.min' => 'Jumlah puyuh terjual tidak boleh kurang dari :min.',
            'penjualan_puyuh_ekor.required' => 'Jumlah puyuh terjual harus diisi.',
            'penjualan_puyuh_jantan.integer' => 'Jumlah jantan harus berupa angka bulat.',
            'penjualan_puyuh_jantan.min' => 'Jumlah jantan tidak boleh kurang dari :min.',
            'penjualan_puyuh_betina.integer' => 'Jumlah betina harus berupa angka bulat.',
            'penjualan_puyuh_betina.min' => 'Jumlah betina tidak boleh kurang dari :min.',
            'pendapatan_harian.numeric' => 'Pendapatan harian harus berupa angka.',
            'pendapatan_harian.min' => 'Pendapatan harian tidak boleh negatif.',
            'jenis_kelamin_penjualan.required' => 'Jenis kelamin penjualan harus dipilih.',
            'jenis_kelamin_penjualan.in' => 'Jenis kelamin penjualan tidak valid.',
            'tray_penjualan.required' => 'Pilih tray yang akan dijual.',
            'tray_penjualan.integer' => 'Tray yang dipilih tidak valid.',
            'tray_penjualan.exists' => 'Tray yang dipilih tidak ditemukan.',
            'jumlah_telur_terjual.required' => 'Jumlah telur terjual harus diisi.',
            'jumlah_telur_terjual.integer' => 'Jumlah telur terjual harus berupa angka bulat.',
            'jumlah_telur_terjual.min' => 'Jumlah telur terjual minimal 1 butir.',
            'harga_penjualan.required' => 'Harga penjualan harus diisi.',
            'harga_penjualan.numeric' => 'Harga penjualan harus berupa angka.',
            'harga_penjualan.min' => 'Harga penjualan tidak boleh negatif.',
            'catatan_kejadian.required' => 'Catatan kejadian harus diisi.',
            'catatan_kejadian.max' => 'Catatan kejadian maksimal 2500 karakter.',
            'sisa_telur.required_without_all' => 'Isi minimal salah satu data stok telur atau tray.',
            'penjualan_telur_butir.required' => 'Jumlah telur terjual harus diisi.',
        ]);

        // Validasi tambahan untuk penjualan campuran (puyuh) agar total diambil dari pembagiannya
        if (($validated['active_tab'] ?? null) === 'penjualan' && !$isTelurBatch && ($validated['jenis_kelamin_penjualan'] ?? null) === 'campuran') {
            $jantanCampuran = (int) ($validated['penjualan_puyuh_jantan'] ?? 0);
            $betinaCampuran = (int) ($validated['penjualan_puyuh_betina'] ?? 0);

            if (($jantanCampuran + $betinaCampuran) < 1) {
                return redirect()->back()->withErrors([
                    'penjualan_puyuh_jantan' => 'Total penjualan campuran harus lebih dari 0 ekor.',
                ])->withInput();
            }

            // Sinkronkan total utama agar sesuai penjumlahan campuran
            $validated['penjualan_puyuh_ekor'] = $jantanCampuran + $betinaCampuran;
        }

        // Validasi penjualan jantan/betina tunggal: pastikan total terisi
        if (($validated['active_tab'] ?? null) === 'penjualan' && !$isTelurBatch && ($validated['jenis_kelamin_penjualan'] ?? null) !== 'campuran') {
            if (empty($validated['penjualan_puyuh_ekor']) || (int) $validated['penjualan_puyuh_ekor'] < 1) {
                return redirect()->back()->withErrors([
                    'penjualan_puyuh_ekor' => 'Jumlah puyuh terjual harus diisi minimal 1 ekor.',
                ])->withInput();
            }
        }

        // Validasi bahwa jumlah penjualan tidak melebihi populasi yang tersedia
        if (($validated['active_tab'] ?? null) === 'penjualan' && !$isTelurBatch && isset($validated['penjualan_puyuh_ekor']) && $validated['penjualan_puyuh_ekor'] > 0) {
            $jumlahTerjual = (int) $validated['penjualan_puyuh_ekor'];
            $jenisKelamin = $validated['jenis_kelamin_penjualan'] ?? null;
            $campuranCounts = $this->parsePenjualanCampuran($jenisKelamin);

            $currentIndukan = (int) ($produksi->jumlah_indukan ?? 0);
            $currentJantan = (int) ($produksi->jumlah_jantan ?? 0);
            $currentBetina = (int) ($produksi->jumlah_betina ?? 0);

            // Cek total populasi
            if ($jumlahTerjual > $currentIndukan) {
                return redirect()->back()->withErrors([
                    'penjualan_puyuh_ekor' => "Jumlah penjualan ({$jumlahTerjual} ekor) melebihi populasi yang tersedia ({$currentIndukan} ekor).",
                ])->withInput();
            }

            // Cek berdasarkan jenis kelamin
            if ($campuranCounts) {
                $kurangJantan = $campuranCounts['jantan'];
                $kurangBetina = $campuranCounts['betina'];
                if ($kurangJantan > $currentJantan) {
                    return redirect()->back()->withErrors([
                        'penjualan_puyuh_jantan' => "Jumlah jantan terjual ({$kurangJantan}) melebihi stok jantan yang tersedia ({$currentJantan}).",
                    ])->withInput();
                }
                if ($kurangBetina > $currentBetina) {
                    return redirect()->back()->withErrors([
                        'penjualan_puyuh_betina' => "Jumlah betina terjual ({$kurangBetina}) melebihi stok betina yang tersedia ({$currentBetina}).",
                    ])->withInput();
                }
            } elseif ($jenisKelamin === 'jantan') {
                if ($jumlahTerjual > $currentJantan) {
                    return redirect()->back()->withErrors([
                        'penjualan_puyuh_ekor' => "Jumlah jantan terjual ({$jumlahTerjual}) melebihi stok jantan yang tersedia ({$currentJantan}).",
                    ])->withInput();
                }
            } elseif ($jenisKelamin === 'betina') {
                if ($jumlahTerjual > $currentBetina) {
                    return redirect()->back()->withErrors([
                        'penjualan_puyuh_ekor' => "Jumlah betina terjual ({$jumlahTerjual}) melebihi stok betina yang tersedia ({$currentBetina}).",
                    ])->withInput();
                }
            }
        }

        // Validasi bahwa jumlah kematian tidak melebihi populasi yang tersedia
        if (($validated['active_tab'] ?? null) === 'kematian' && !$isTelurBatch && isset($validated['jumlah_kematian']) && $validated['jumlah_kematian'] > 0) {
            $jumlahMati = (int) $validated['jumlah_kematian'];
            $jenisKelamin = $validated['jenis_kelamin_kematian'] ?? null;

            $currentIndukan = (int) ($produksi->jumlah_indukan ?? 0);
            $currentJantan = (int) ($produksi->jumlah_jantan ?? 0);
            $currentBetina = (int) ($produksi->jumlah_betina ?? 0);

            // Cek total populasi
            if ($jumlahMati > $currentIndukan) {
                return redirect()->back()->withErrors([
                    'jumlah_kematian' => "Jumlah kematian ({$jumlahMati} ekor) melebihi populasi yang tersedia ({$currentIndukan} ekor).",
                ])->withInput();
            }

            // Cek berdasarkan jenis kelamin
            if ($jenisKelamin === 'jantan') {
                if ($jumlahMati > $currentJantan) {
                    return redirect()->back()->withErrors([
                        'jumlah_kematian' => "Jumlah kematian jantan ({$jumlahMati}) melebihi stok jantan yang tersedia ({$currentJantan}).",
                    ])->withInput();
                }
            } elseif ($jenisKelamin === 'betina') {
                if ($jumlahMati > $currentBetina) {
                    return redirect()->back()->withErrors([
                        'jumlah_kematian' => "Jumlah kematian betina ({$jumlahMati}) melebihi stok betina yang tersedia ({$currentBetina}).",
                    ])->withInput();
                }
            }
        }

        $selectedFeedItem = null;
        $selectedVitaminItem = null;

        if ($request->filled('feed_item_id') && Schema::hasTable('vf_feed_vitamin_items')) {
            $selectedFeedItem = FeedVitaminItem::active()
                ->where('category', 'pakan')
                ->whereKey($request->input('feed_item_id'))
                ->first();
        }

        if ($request->filled('vitamin_item_id') && Schema::hasTable('vf_feed_vitamin_items')) {
            $selectedVitaminItem = FeedVitaminItem::active()
                ->where('category', 'vitamin')
                ->whereKey($request->input('vitamin_item_id'))
                ->first();
        }

        $activeTab = $validated['active_tab'];

        if ($activeTab === 'pakan' && $selectedFeedItem) {
            $validated['harga_pakan_per_kg'] = (float) $selectedFeedItem->price;
        }

        if ($activeTab === 'vitamin' && $selectedVitaminItem) {
            $validated['harga_vitamin_per_liter'] = (float) $selectedVitaminItem->price;
        }

        // Find existing record or create new one (enforce unique batch_id + tanggal)
        $laporan = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        // If record exists and already has data for the active tab, prompt before overwrite
        if ($laporan && !$request->filled('duplicate_action')) {
            $isHidden = $laporan->tampilkan_di_histori === false;
            $hasExistingForTab = false;
            switch ($activeTab) {
                case 'telur':
                    $hasExistingForTab = false; // Allow multiple trays per day
                    break;
                case 'penjualan':
                    if ($isTelurBatch) {
                        $hasExistingForTab = false; // Allow multiple tray sales per day
                    } else {
                        $hasExistingForTab = !$isHidden && (($laporan->penjualan_telur_butir ?? 0) > 0 || ($laporan->penjualan_puyuh_ekor ?? 0) > 0);
                    }
                    break;
                case 'pakan':
                    // Hanya anggap ada data jika nilai konsumsi sudah terisi > 0
                    $hasExistingForTab = !$isHidden && ($laporan->konsumsi_pakan_kg ?? 0) > 0;
                    break;
                case 'vitamin':
                    $hasExistingForTab = !$isHidden && $laporan->vitamin_terpakai !== null;
                    break;
                case 'kematian':
                    $hasExistingForTab = !$isHidden && ($laporan->jumlah_kematian ?? 0) > 0;
                    break;
                case 'laporan':
                    $hasExistingForTab = !$isHidden && !empty($laporan->catatan_kejadian);
                    break;
            }

            if ($hasExistingForTab) {
                $tanggalDisplay = Carbon::parse($validated['tanggal'])->locale('id')->translatedFormat('d F Y');
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('duplicate_warning', [
                        'tanggal' => $validated['tanggal'],
                        'tanggal_display' => $tanggalDisplay,
                    ]);
            }
        }

        if (!$laporan) {
            $laporan = new LaporanHarian([
                'batch_produksi_id' => $produksi->batch_produksi_id,
                'tanggal' => $validated['tanggal'],
                'jumlah_burung' => $produksi->jumlah_indukan ?? 0,
            ]);
        }

        $isNewRecord = !$laporan->exists;

        // Only update fields that are relevant to the active tab
        $updateData = [
            'pengguna_id' => Auth::id(),
            'tampilkan_di_histori' => true,
        ];

        // Special handling for telur tab (tray creation)
        if ($activeTab === 'telur') {
            if (isset($validated['produksi_telur']) && $validated['produksi_telur'] > 0) {
                if ($validated['produksi_telur'] > 100) {
                    // Bulk creation: divide into trays of 100 eggs each
                    $eggsPerTray = 100;
                    $totalEggs = (int) $validated['produksi_telur'];
                    $numTrays = (int) ceil($totalEggs / $eggsPerTray);
                    for ($i = 0; $i < $numTrays; $i++) {
                        $eggs = $eggsPerTray;
                        if ($i == $numTrays - 1) {
                            $eggs = $totalEggs - ($i * $eggsPerTray);
                        }
                        $newLaporan = new LaporanHarian([
                            'batch_produksi_id' => $produksi->batch_produksi_id,
                            'tanggal' => $validated['tanggal'],
                            'pengguna_id' => Auth::id(),
                            'tampilkan_di_histori' => true,
                            'produksi_telur' => $eggs,
                            'input_telur' => $eggs,
                            'jumlah_burung' => $produksi->jumlah_indukan ?? 0,
                        ]);
                        $newLaporan->save();
                        $newLaporan->nama_tray = $this->generateDefaultTrayName($newLaporan);
                        $newLaporan->save();
                        $this->logTrayHistory($produksi, $newLaporan, 'created');
                    }
                    $message = "Berhasil membuat {$numTrays} tray dengan total {$totalEggs} telur.";
                } else {
                    // Single tray
                    $laporan->fill([
                        'pengguna_id' => Auth::id(),
                        'tampilkan_di_histori' => true,
                        'produksi_telur' => $validated['produksi_telur'],
                        'input_telur' => $validated['produksi_telur'],
                    ]);
                    $laporan->save();
                    $laporan->nama_tray = $this->generateDefaultTrayName($laporan);
                    $laporan->save();
                    $this->logTrayHistory($produksi, $laporan, 'created');
                    $message = 'Tray berhasil ditambahkan.';
                }
            } else {
                $message = 'Tidak ada telur yang diinput.';
            }
            return redirect()->route('admin.produksi.show', $produksi->id)->with('success', $message);
        }

        switch ($activeTab) {

            case 'penjualan':
                if ($isTelurBatch) {
                    if (isset($validated['tray_penjualan']) && $validated['tray_penjualan']) {
                        $selectedTray = LaporanHarian::where('id', $validated['tray_penjualan'])
                            ->where('batch_produksi_id', $produksi->batch_produksi_id)
                            ->whereNotNull('nama_tray')
                            ->first();

                        if (!$selectedTray) {
                            return redirect()->back()->withErrors(['tray_penjualan' => 'Tray yang dipilih tidak valid atau tidak tersedia.']);
                        }

                        $availableEggs = $selectedTray->produksi_telur ?? 0;
                        if ($validated['jumlah_telur_terjual'] > $availableEggs) {
                            return redirect()->back()->withErrors(['jumlah_telur_terjual' => "Jumlah telur terjual tidak boleh melebihi stok tray ({$availableEggs} butir)."]);
                        }

                        // Create new record for each tray sale (allow multiple sales per day)
                        $newLaporan = new LaporanHarian([
                            'batch_produksi_id' => $produksi->batch_produksi_id,
                            'tanggal' => $validated['tanggal'],
                            'pengguna_id' => Auth::id(),
                            'tampilkan_di_histori' => true,
                            'tray_penjualan_id' => $validated['tray_penjualan'],
                            'penjualan_telur_butir' => $validated['jumlah_telur_terjual'],
                            'harga_per_butir' => $validated['harga_penjualan'],
                            'pendapatan_harian' => $validated['jumlah_telur_terjual'] * $validated['harga_penjualan'],
                            'nama_tray_penjualan' => $selectedTray->nama_tray,
                            'jumlah_burung' => $produksi->jumlah_indukan ?? 0,
                        ]);
                        $newLaporan->save();

                        $message = "Tray '{$selectedTray->nama_tray}' berhasil dijual ({$validated['jumlah_telur_terjual']} butir).";
                        return redirect()->route('admin.produksi.show', $produksi->id)->with('success', $message);
                    }
                } else {
                    $jumlahTerjual = isset($validated['penjualan_puyuh_ekor']) ? (int) $validated['penjualan_puyuh_ekor'] : 0;
                    $hargaSatuan = isset($validated['harga_penjualan']) ? (float) $validated['harga_penjualan'] : 0;
                    $jenisKelaminPenjualan = $validated['jenis_kelamin_penjualan'] ?? null;
                    $jumlahCampuranJantan = (int) ($validated['penjualan_puyuh_jantan'] ?? 0);
                    $jumlahCampuranBetina = (int) ($validated['penjualan_puyuh_betina'] ?? 0);

                    if ($jenisKelaminPenjualan === 'campuran') {
                        $jumlahTerjual = $jumlahCampuranJantan + $jumlahCampuranBetina;
                    }

                    if ($jumlahTerjual > 0) {
                        $updateData['penjualan_puyuh_ekor'] = $jumlahTerjual;
                        if (Schema::hasColumn('vf_laporan_harian', 'jenis_kelamin_penjualan')) {
                            $updateData['jenis_kelamin_penjualan'] = $jenisKelaminPenjualan;
                        }
                        // Simpan breakdown jantan dan betina
                        if ($jenisKelaminPenjualan === 'campuran') {
                            $updateData['penjualan_puyuh_jantan'] = $jumlahCampuranJantan;
                            $updateData['penjualan_puyuh_betina'] = $jumlahCampuranBetina;
                        } elseif ($jenisKelaminPenjualan === 'jantan') {
                            $updateData['penjualan_puyuh_jantan'] = $jumlahTerjual;
                            $updateData['penjualan_puyuh_betina'] = 0;
                        } elseif ($jenisKelaminPenjualan === 'betina') {
                            $updateData['penjualan_puyuh_jantan'] = 0;
                            $updateData['penjualan_puyuh_betina'] = $jumlahTerjual;
                        }
                        $updateData['harga_per_butir'] = $hargaSatuan; // reuse column as harga satuan
                        $updateData['pendapatan_harian'] = $jumlahTerjual * $hargaSatuan;
                    }
                }
                break;

            case 'pakan':
                if (isset($validated['konsumsi_pakan_kg']) && $validated['konsumsi_pakan_kg'] !== null && $validated['konsumsi_pakan_kg'] !== '') {
                    $totalPakan = (float) $validated['konsumsi_pakan_kg'];
                    $updateData['konsumsi_pakan_kg'] = $totalPakan;
                    $updateData['sisa_pakan_kg'] = isset($validated['sisa_pakan_kg']) && $validated['sisa_pakan_kg'] !== null && $validated['sisa_pakan_kg'] !== '' ? (float) $validated['sisa_pakan_kg'] : null;

                    $hargaPakan = isset($validated['harga_pakan_per_kg']) && $validated['harga_pakan_per_kg'] !== ''
                        ? (float) $validated['harga_pakan_per_kg']
                        : null;

                    $updateData['harga_pakan_per_kg'] = $hargaPakan;
                    $updateData['biaya_pakan_harian'] = $hargaPakan !== null ? round($totalPakan * $hargaPakan, 2) : null;
                }
                break;

            case 'vitamin':
                if (isset($validated['vitamin_terpakai']) && $validated['vitamin_terpakai'] !== null && $validated['vitamin_terpakai'] !== '') {
                    $totalVitamin = (float) $validated['vitamin_terpakai'];
                    $updateData['vitamin_terpakai'] = $totalVitamin;
                    $updateData['sisa_vitamin_liter'] = isset($validated['sisa_vitamin_liter']) && $validated['sisa_vitamin_liter'] !== ''
                        ? (float) $validated['sisa_vitamin_liter']
                        : null;

                    $hargaVitamin = isset($validated['harga_vitamin_per_liter']) && $validated['harga_vitamin_per_liter'] !== ''
                        ? (float) $validated['harga_vitamin_per_liter']
                        : null;

                    $updateData['harga_vitamin_per_liter'] = $hargaVitamin;
                    $updateData['biaya_vitamin_harian'] = $hargaVitamin !== null ? round($totalVitamin * $hargaVitamin, 2) : null;
                }
                break;

            case 'kematian':
                if (isset($validated['jumlah_kematian']) && $validated['jumlah_kematian'] !== null && $validated['jumlah_kematian'] !== '') {
                    $updateData['jumlah_kematian'] = (int) $validated['jumlah_kematian'];
                    $updateData['jenis_kelamin_kematian'] = $validated['jenis_kelamin_kematian'] ?? null;
                    $updateData['keterangan_kematian'] = $validated['keterangan_kematian'] ?? null;
                }
                break;

            case 'laporan':
                if (isset($validated['catatan_kejadian']) && $validated['catatan_kejadian'] !== null && $validated['catatan_kejadian'] !== '') {
                    $updateData['catatan_kejadian'] = $validated['catatan_kejadian'];
                }
                break;
        }

        $laporan->fill($updateData);

        // For existing laporan, reset population changes from previous data
        if ($laporan->exists && !$isTelurBatch) {
            if ($activeTab === 'penjualan' && $laporan->penjualan_puyuh_ekor > 0) {
                $jenis = strtolower($laporan->jenis_kelamin_penjualan ?? '');
                if ($jenis === 'campuran') {
                    $jantan = $laporan->penjualan_puyuh_jantan ?? 0;
                    $betina = $laporan->penjualan_puyuh_betina ?? 0;
                    $produksi->increment('jumlah_jantan', $jantan);
                    $produksi->increment('jumlah_betina', $betina);
                } elseif ($jenis === 'jantan') {
                    $produksi->increment('jumlah_jantan', $laporan->penjualan_puyuh_ekor);
                } elseif ($jenis === 'betina') {
                    $produksi->increment('jumlah_betina', $laporan->penjualan_puyuh_ekor);
                }
            } elseif ($activeTab === 'kematian' && $laporan->jumlah_kematian > 0) {
                $jenis = strtolower($laporan->jenis_kelamin_kematian ?? '');
                if ($jenis === 'jantan') {
                    $produksi->increment('jumlah_jantan', $laporan->jumlah_kematian);
                } elseif ($jenis === 'betina') {
                    $produksi->increment('jumlah_betina', $laporan->jumlah_kematian);
                }
            }
        }

        $laporan->save();

        // Update population directly based on the new laporan
        if (!$isTelurBatch) {
            if ($activeTab === 'penjualan' && isset($jumlahTerjual) && $jumlahTerjual > 0) {
                if ($jenisKelaminPenjualan === 'campuran') {
                    $produksi->decrement('jumlah_jantan', $jumlahCampuranJantan);
                    $produksi->decrement('jumlah_betina', $jumlahCampuranBetina);
                } elseif ($jenisKelaminPenjualan === 'jantan') {
                    $produksi->decrement('jumlah_jantan', $jumlahTerjual);
                } elseif ($jenisKelaminPenjualan === 'betina') {
                    $produksi->decrement('jumlah_betina', $jumlahTerjual);
                }
            } elseif ($activeTab === 'kematian' && isset($validated['jumlah_kematian']) && $validated['jumlah_kematian'] > 0) {
                $jenisKelamin = $validated['jenis_kelamin_kematian'];
                if ($jenisKelamin === 'jantan') {
                    $produksi->decrement('jumlah_jantan', $validated['jumlah_kematian']);
                } elseif ($jenisKelamin === 'betina') {
                    $produksi->decrement('jumlah_betina', $validated['jumlah_kematian']);
                }
            }
            // Sync jumlah_indukan
            $produksi->jumlah_indukan = max(0, ($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0));
            $produksi->save();
        }

        if ($activeTab === 'telur' && empty($laporan->nama_tray)) {
            $laporan->nama_tray = $this->generateDefaultTrayName($laporan);
            $laporan->save();
        }

        $this->syncTelurTurunanFromPuyuh($produksi);

        // Sync jumlah_indukan to ensure it matches jumlah_jantan + jumlah_betina
        if (!$isTelurBatch) {
            $produksi->jumlah_indukan = max(0, ($produksi->jumlah_jantan ?? 0) + ($produksi->jumlah_betina ?? 0));
            $produksi->save();
        }


        $wasCreated = $isNewRecord;

        // Generate specific success message based on active tab
        $tabNames = [
            'telur' => 'Telur',
            'tray' => 'Tray',
            'penjualan' => 'Penjualan',
            'pakan' => 'Pakan',
            'vitamin' => 'Vitamin',
            'kematian' => 'Kematian',
            'laporan' => 'Laporan'
        ];

        $tabName = $tabNames[$activeTab] ?? 'Laporan';
        $action = $wasCreated ? 'ditambahkan' : 'diperbarui';
        $message = "Laporan harian {$tabName} berhasil {$action}.";

        return redirect()
            ->route('admin.produksi.show', $produksi->id)
            ->with('success', $message);
    }

    public function updateTrayEntry(Request $request, Produksi $produksi, LaporanHarian $laporan)
    {
        $this->ensureTrayEntryBelongsToProduksi($produksi, $laporan);

        $validated = $request->validate([
            'nama_tray' => 'nullable|string|max:120',
            'jumlah_telur' => 'required|integer|min:1',
            'keterangan_tray' => 'nullable|string|max:1000',
        ], [
            'jumlah_telur.required' => 'Jumlah telur harus diisi.',
            'jumlah_telur.min' => 'Jumlah telur minimal 1 butir.',
        ]);

        // Capture old values before updating
        $oldValues = [
            'nama_tray' => $laporan->nama_tray,
            'jumlah_telur' => $laporan->produksi_telur,
            'keterangan_tray' => $laporan->keterangan_tray,
        ];

        // Periksa apakah jumlah telur dikurangi - tambahkan selisih ke telur_rusak
        if ($validated['jumlah_telur'] < $oldValues['jumlah_telur']) {
            $difference = $oldValues['jumlah_telur'] - $validated['jumlah_telur'];
            $laporan->telur_rusak = ($laporan->telur_rusak ?? 0) + $difference;
        }

        $laporan->nama_tray = $validated['nama_tray'] ?: $laporan->nama_tray;
        $laporan->keterangan_tray = $validated['keterangan_tray'] ?? null;
        $laporan->produksi_telur = $validated['jumlah_telur'];
        if (Schema::hasColumn('vf_laporan_harian', 'input_telur')) {
            $laporan->input_telur = $validated['jumlah_telur'];
        }
        $laporan->save();

        $history = $this->logTrayHistory($produksi, $laporan, 'updated', $oldValues);

        $this->syncTelurTurunanFromPuyuh($produksi);

        return response()->json([
            'message' => 'Tray berhasil diperbarui.',
            'tray' => $this->formatTrayPayload($laporan),
            'history' => $this->formatTrayHistoryPayload($history),
        ]);
    }

    public function destroyTrayEntry(Produksi $produksi, LaporanHarian $laporan)
    {
        $this->ensureTrayEntryBelongsToProduksi($produksi, $laporan);

        if (($laporan->produksi_telur ?? 0) <= 0) {
            return response()->json([
                'message' => 'Entry ini bukan data tray.',
            ], 422);
        }

        // Capture old values before deleting
        $oldValues = [
            'nama_tray' => $laporan->nama_tray,
            'jumlah_telur' => $laporan->produksi_telur,
            'keterangan_tray' => $laporan->keterangan_tray,
        ];

        $history = $this->logTrayHistory($produksi, $laporan, 'deleted', $oldValues);
        $laporanId = $laporan->id;
        $laporan->delete();

        $this->syncTelurTurunanFromPuyuh($produksi);

        return response()->json([
            'message' => 'Tray berhasil dihapus.',
            'tray_id' => $laporanId,
            'history' => $this->formatTrayHistoryPayload($history),
        ]);
    }

    /**
     * Generate auto summary text for the laporan tab based on the day's inputs.
     */
    public function generateDailyReportSummary(Request $request, Produksi $produksi)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'variant' => 'nullable|in:puyuh,telur',
        ]);

        if (empty($produksi->batch_produksi_id)) {
            return response()->json([
                'message' => 'Produksi ini belum memiliki kode batch.',
            ], 422);
        }

        $tanggal = Carbon::parse($validated['tanggal'])->toDateString();
        $tanggalFormatted = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');
        $variant = $validated['variant'] ?? ($produksi->tipe_produksi === 'telur' ? 'telur' : 'puyuh');

        $laporanHarian = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('dibuat_pada')
            ->get();

        if ($laporanHarian->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada pencatatan lain pada tanggal tersebut.',
            ], 404);
        }
        if ($variant === 'telur' && $produksi->tipe_produksi !== 'telur') {
            $variant = 'puyuh';
        } elseif ($variant === 'puyuh' && $produksi->tipe_produksi === 'telur') {
            $variant = 'telur';
        }

        $summary = $variant === 'telur'
            ? $this->buildTelurDailySummary($produksi, $laporanHarian, $tanggalFormatted)
            : $this->buildPuyuhDailySummary($produksi, $laporanHarian, $tanggalFormatted);

        return response()->json([
            'summary' => $summary,
            'date' => $tanggal,
        ]);
    }

    protected function buildPuyuhDailySummary(Produksi $produksi, Collection $laporanHarian, string $tanggalFormatted): string
    {
        $formatNumber = function ($value, $decimals = 0) {
            return number_format((float) ($value ?? 0), $decimals, ',', '.');
        };

        $kandangNama = $produksi->kandang?->nama_kandang ?: 'Tidak ditentukan';

        $segments = [];

        $totalTelur = $laporanHarian->sum('produksi_telur');
        $totalTelurRusak = $laporanHarian->sum('telur_rusak');
        $sisaTelur = optional($laporanHarian->first(fn ($item) => $item->sisa_telur !== null))->sisa_telur;
        $totalPenjualanPuyuh = $laporanHarian->sum('penjualan_puyuh_ekor');
        $totalPendapatanPuyuh = $laporanHarian->sum(function ($laporan) {
            return ($laporan->penjualan_puyuh_ekor ?? 0) > 0 ? (float) ($laporan->pendapatan_harian ?? 0) : 0;
        });
        $totalPakan = $laporanHarian->sum('konsumsi_pakan_kg');
        $totalBiayaPakan = $laporanHarian->sum('biaya_pakan_harian');
        $hargaPakanPerKg = optional($laporanHarian->first(fn ($item) => $item->harga_pakan_per_kg !== null))->harga_pakan_per_kg;
        $sisaPakan = optional($laporanHarian->first(fn ($item) => $item->sisa_pakan_kg !== null))->sisa_pakan_kg;
        $totalVitamin = $laporanHarian->sum('vitamin_terpakai');
        $totalBiayaVitamin = $laporanHarian->sum('biaya_vitamin_harian');
        $hargaVitaminPerLiter = optional($laporanHarian->first(fn ($item) => $item->harga_vitamin_per_liter !== null))->harga_vitamin_per_liter;
        $sisaVitamin = optional($laporanHarian->first(fn ($item) => $item->sisa_vitamin_liter !== null))->sisa_vitamin_liter;
        $totalKematian = $laporanHarian->sum('jumlah_kematian');
        $currentPopulation = $laporanHarian->max('jumlah_burung') ?? $produksi->jumlah_indukan;

        $mortalityRate = $currentPopulation > 0 ? round(($totalKematian / max($currentPopulation, 1)) * 100, 2) : 0;
        $avgPricePuyuh = $totalPenjualanPuyuh > 0 ? $totalPendapatanPuyuh / max($totalPenjualanPuyuh, 1) : 0;
        $totalBiayaKonsumsi = ($totalBiayaPakan ?? 0) + ($totalBiayaVitamin ?? 0);

        $catatanUtama = $laporanHarian
            ->whereNotNull('catatan_kejadian')
            ->sortByDesc(fn ($item) => $item->dibuat_pada ?? $item->created_at ?? $item->tanggal)
            ->pluck('catatan_kejadian')
            ->map(fn ($note) => trim($note))
            ->first();

        if (!$catatanUtama || strlen($catatanUtama) < 1) {
            $catatanUtama = 'Tidak ada catatan tambahan.';
        }

        $penyusun = Auth::user()->nama_pengguna ?? Auth::user()->username ?? 'Sistem';
        $tanggalPenyusunan = now()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB';

        // Header
        $segments[] = 'LAPORAN PRODUKSI HARIAN - PUYUH PETELUR';
        $segments[] = '============================================';
        $segments[] = 'Tanggal: ' . $tanggalFormatted;
        $segments[] = 'Nomor Batch: ' . $produksi->batch_produksi_id;
        $segments[] = 'Lokasi Kandang: ' . $kandangNama;
        $segments[] = '';

        // Produksi & Penjualan
        $segments[] = 'PRODUKSI & PENJUALAN';
        $segments[] = '';
        $segments[] = 'Telur Dihasilkan: ' . $formatNumber($totalTelur) . ' butir';
        $segments[] = 'Puyuh Terjual: ' . $formatNumber($totalPenjualanPuyuh) . ' ekor';
        $segments[] = 'Harga per Ekor: Rp ' . $formatNumber(round($avgPricePuyuh));
        $segments[] = 'Total Pendapatan: Rp ' . $formatNumber($totalPendapatanPuyuh);
        $segments[] = '';

        // Biaya Konsumsi
        $segments[] = 'BIAYA KONSUMSI';
        $segments[] = '';
        $segments[] = 'Pakan: ' . $formatNumber($totalPakan, 2) . ' kg (Rp ' . $formatNumber($hargaPakanPerKg ?? 0) . '/kg) = Rp ' . $formatNumber($totalBiayaPakan);
        $segments[] = 'Vitamin: ' . $formatNumber($totalVitamin, 2) . ' L (Rp ' . $formatNumber($hargaVitaminPerLiter ?? 0) . '/L) = Rp ' . $formatNumber($totalBiayaVitamin);
        $segments[] = 'TOTAL BIAYA: Rp ' . $formatNumber($totalBiayaKonsumsi);
        $segments[] = '';

        // Monitoring Kesehatan
        $segments[] = 'MONITORING KESEHATAN';
        $segments[] = '';
        $segments[] = 'Mortalitas: ' . $formatNumber($totalKematian) . ' ekor';
        $segments[] = 'Persentase Mortalitas: ' . $formatNumber($mortalityRate, 2) . '% (dari total populasi ' . $formatNumber($currentPopulation) . ' ekor)';
        $segments[] = '';

        // Catatan
        $segments[] = 'CATATAN';
        $segments[] = '';
        $segments[] = preg_replace('/\s+/', ' ', $catatanUtama);
        $segments[] = '';

        // Informasi Dokumen
        $segments[] = 'INFORMASI DOKUMEN';
        $segments[] = '';
        $segments[] = 'Disusun oleh: ' . $penyusun;
        $segments[] = 'Tanggal Penyusunan: ' . $tanggalPenyusunan;
        $segments[] = 'Dokumen ini digenerate otomatis untuk dokumentasi produksi.';

        return $this->finalizeSummary($segments, 1200);
    }

    protected function buildTelurDailySummary(Produksi $produksi, Collection $laporanHarian, string $tanggalFormatted): string
    {
        $formatNumber = fn ($value, $decimals = 0) => number_format((float) ($value ?? 0), $decimals, ',', '.');

        $kandangNama = $produksi->kandang->nama_kandang ?? 'Tidak ditentukan';

        $segments = [
            "📅 Rangkuman Telur | {$tanggalFormatted}",
            "🏷️ Batch {$produksi->batch_produksi_id} - {$kandangNama}",
            '',
        ];

        $totalTelur = $laporanHarian->sum('produksi_telur');
        $totalTelurRusak = $laporanHarian->sum('telur_rusak');
        $sisaTelur = optional($laporanHarian->first(fn ($item) => $item->sisa_telur !== null))->sisa_telur;
        $totalPenjualan = $laporanHarian->sum('penjualan_telur_butir');
        $totalPendapatan = $laporanHarian->sum('pendapatan_harian');
        $unsold = max($totalTelur - $totalPenjualan, 0);
        $totalTrayDipakai = $laporanHarian->whereNotNull('nama_tray')->where('produksi_telur', '>', 0)->count();
        $sisaTrayBal = optional($laporanHarian->first(fn ($item) => $item->sisa_tray_bal !== null))->sisa_tray_bal;
        $sisaTrayLembar = optional($laporanHarian->first(fn ($item) => $item->sisa_tray_lembar !== null))->sisa_tray_lembar;

        $produksiMetrics = [];
        $operasionalMetrics = [];

        if ($totalTelur > 0) {
            $line = "🥚 Telur: {$formatNumber($totalTelur)} butir";
            $details = [];
            if ($totalTelurRusak > 0) {
                $details[] = 'rusak ' . $formatNumber($totalTelurRusak);
            }
            if ($sisaTelur !== null) {
                $details[] = 'stok ' . $formatNumber($sisaTelur);
            }
            if (!empty($details)) {
                $line .= ' (' . implode('; ', $details) . ')';
            }
            $produksiMetrics[] = $line;
        }

        if ($totalPenjualan > 0) {
            $line = "💰 Penjualan: {$formatNumber($totalPenjualan)} butir";
            if ($totalPendapatan > 0) {
                $avgPrice = $totalPendapatan / max($totalPenjualan, 1);
                $line .= " | Rp {$formatNumber($totalPendapatan)} (Rp {$formatNumber(round($avgPrice))}/butir)";
            }
            if ($unsold > 0) {
                $line .= " | sisa {$formatNumber($unsold)}";
            }
            $produksiMetrics[] = $line;
        }

        if ($totalTrayDipakai > 0) {
            $operasionalMetrics[] = "📦 Tray: {$formatNumber($totalTrayDipakai)} terpakai";
        }

        if ($sisaTrayBal !== null || $sisaTrayLembar !== null) {
            $parts = [];
            if ($sisaTrayBal !== null) {
                $parts[] = "bal {$formatNumber($sisaTrayBal, 2)}";
            }
            if ($sisaTrayLembar !== null) {
                $parts[] = "lembar {$formatNumber($sisaTrayLembar)}";
            }
            $operasionalMetrics[] = '📦 Stok tray: ' . implode(' | ', $parts);
        }

        if (!empty($produksiMetrics)) {
            $segments[] = '📊 Produksi';
            foreach ($produksiMetrics as $metric) {
                $segments[] = '- ' . $metric;
            }
            $segments[] = '';
        }

        if (!empty($operasionalMetrics)) {
            $segments[] = '🏭 Operasional';
            foreach ($operasionalMetrics as $metric) {
                $segments[] = '- ' . $metric;
            }
            $segments[] = '';
        }

        if (empty($produksiMetrics) && empty($operasionalMetrics)) {
            $segments[] = 'Tidak ada input pada tanggal ini.';
            $segments[] = '';
        }

        $notePool = $laporanHarian->whereNotNull('catatan_kejadian')
            ->pluck('catatan_kejadian')
            ->filter()
            ->map(fn ($note) => trim($note))
            ->filter(fn ($note) => !str_contains($note, 'Ringkasan') && !str_contains($note, 'Batch') && strlen($note) >= 3 && strlen($note) < 200)
            ->unique();

        $notes = $notePool->take(3);

        $segments[] = '📝 Catatan';
        if ($notes->isEmpty()) {
            $segments[] = '- Tidak ada catatan.';
        } else {
            foreach ($notes as $note) {
                $segments[] = '- ' . preg_replace('/\s+/', ' ', $note);
            }
        }

        if ($notePool->count() > $notes->count()) {
            $segments[] = '- Catatan lain tersimpan di histori.';
        }

        $segments[] = '- Generated by ' . (Auth::user()->nama_pengguna ?? Auth::user()->username ?? 'Sistem') . ' at ' . now()->locale('id')->format('d F Y, H:i');

        return $this->finalizeSummary($segments, 1200);
    }

    protected function finalizeSummary(array $segments, int $maxLength = 2400): string
    {
        $text = implode("\n", $segments);
        $text = preg_replace("/\n{3,}/", "\n\n", $text ?? '') ?? '';
        $text = trim($text);

        if ($text === '') {
            $text = 'Belum ada data otomatis untuk tanggal ini. Lengkapi pencatatan terlebih dahulu.';
        }

        $lengthFn = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
        $substrFn = function_exists('mb_substr') ? 'mb_substr' : 'substr';

        if ($lengthFn($text) > $maxLength) {
            $text = $substrFn($text, 0, $maxLength - 3);
            $text = rtrim($text);
            $text .= '...';
        }

        return $text;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produksi $produksi)
    {
        $kandangList = Kandang::query()
            ->typeIs('produksi')
            ->statusIn(['aktif', 'maintenance'])
            ->when($produksi->kandang_id, function ($query) use ($produksi) {
                $query->orWhere('id', $produksi->kandang_id);
            })
            ->orderBy('nama_kandang')
            ->get();
        
        $penetasanList = Penetasan::with('kandang')
                                  ->whereIn('status', ['selesai', 'proses'])
                                  ->orderBy('tanggal_menetas', 'desc')
                                  ->get();
        
        $pembesaranList = Pembesaran::with('kandang')
                                    ->whereIn('status_batch', ['aktif', 'Aktif', 'selesai'])
                                    ->orderBy('tanggal_masuk', 'desc')
                                    ->get();
        
        return view('admin.pages.produksi.edit-produksi', compact('produksi', 'kandangList', 'penetasanList', 'pembesaranList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produksi $produksi)
    {
        $originalKandangId = $produksi->kandang_id;

        $rules = [
            'kandang_id' => 'required|exists:vf_kandang,id',
            'batch_produksi_id' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,tidak_aktif',
            'catatan' => 'nullable|string',
            'harga_per_pcs' => 'nullable|numeric|min:0',
        ];

        if ($produksi->tipe_produksi === 'puyuh') {
            $rules = array_merge($rules, [
                'jumlah_indukan' => 'required|integer|min:1',
                'jumlah_jantan' => 'nullable|integer|min:0',
                'jumlah_betina' => 'nullable|integer|min:0',
                'umur_mulai_produksi' => 'nullable|integer|min:1',
                'jumlah_telur' => 'nullable|integer|min:0',
                'berat_rata_telur' => 'nullable|numeric|min:0',
            ]);
        } else {
            $rules = array_merge($rules, [
                'jumlah_telur' => 'required|integer|min:1',
                'berat_rata_telur' => 'nullable|numeric|min:0',
                'jumlah_indukan' => 'nullable|integer|min:0',
                'jumlah_jantan' => 'nullable|integer|min:0',
                'jumlah_betina' => 'nullable|integer|min:0',
                'umur_mulai_produksi' => 'nullable|integer|min:1',
            ]);
        }

        $validated = $request->validate($rules);

        if ($produksi->tipe_produksi === 'puyuh' && $produksi->jenis_kelamin === 'campuran') {
            $jumlahIndukan = isset($validated['jumlah_indukan']) ? (int) $validated['jumlah_indukan'] : (int) ($produksi->jumlah_indukan ?? 0);
            $jumlahJantan = isset($validated['jumlah_jantan']) ? (int) $validated['jumlah_jantan'] : (int) ($produksi->jumlah_jantan ?? 0);
            $jumlahBetina = isset($validated['jumlah_betina']) ? (int) $validated['jumlah_betina'] : (int) ($produksi->jumlah_betina ?? 0);
            if (($jumlahJantan + $jumlahBetina) !== $jumlahIndukan) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah_jantan' => "Jumlah Puyuh tidak sesuai (" . ($jumlahJantan + $jumlahBetina) . ") / ({$jumlahIndukan})"]);
            }
        }

        try {
            // Pastikan batch_produksi_id berupa ID numerik (FK) walau form mengirim kode batch
            $providedBatch = $validated['batch_produksi_id'] ?? null;
            $jumlahAwal = $validated['jumlah_indukan'] ?? ($validated['jumlah_telur'] ?? 0);

            if ($providedBatch) {
                if (is_numeric($providedBatch)) {
                    $batch = BatchProduksi::find($providedBatch);
                    if (!$batch) {
                        throw new \Exception('Batch produksi tidak ditemukan.');
                    }
                    $validated['batch_produksi_id'] = $batch->id;
                } else {
                    $batch = BatchProduksi::firstOrCreate(
                        ['kode_batch' => $providedBatch],
                        [
                            'kandang_id' => $validated['kandang_id'],
                            'tanggal_mulai' => $validated['tanggal_mulai'],
                            'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
                            'jumlah_awal' => $jumlahAwal,
                            'jumlah_saat_ini' => $produksi->tipe_produksi === 'puyuh' ? $jumlahAwal : null,
                            'fase' => 'layer',
                            'status' => 'aktif',
                            'catatan' => $validated['catatan'] ?? null,
                        ]
                    );
                    $validated['batch_produksi_id'] = $batch->id;
                }
            }

            $produksi->update($validated);

            $produksi->loadMissing('kandang');
            $produksi->kandang?->syncMaintenanceStatus();

            if ($originalKandangId && $originalKandangId !== $produksi->kandang_id) {
                Kandang::find($originalKandangId)?->syncMaintenanceStatus();
            }

            $produksi->loadMissing(['batchProduksi', 'pembesaran', 'penetasan']);
            $identifier = $produksi->batch_label;

            return redirect()->route('admin.produksi')
                           ->with('success', sprintf('Produksi %s berhasil diperbarui.', $identifier));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produksi $produksi)
    {
        $kandangId = $produksi->kandang_id;
        $produksi->loadMissing(['batchProduksi', 'pembesaran', 'penetasan']);
        $identifier = $produksi->batch_label;

        DB::beginTransaction();
        try {
            // Rollback transfers if applicable
            if ($produksi->pembesaran_id) {
                $pembesaran = Pembesaran::find($produksi->pembesaran_id);
                if ($pembesaran) {
                    $pembesaran->decrement('indukan_ditransfer', $produksi->jumlah_indukan);
                }
            }

            if ($produksi->penetasan_id && $produksi->jumlah_telur) {
                $penetasan = Penetasan::find($produksi->penetasan_id);
                if ($penetasan) {
                    $penetasan->decrement('telur_infertil_ditransfer', $produksi->jumlah_telur);
                }
            }

            $produksi->delete();

            DB::commit();
            if ($kandangId) {
                Kandang::find($kandangId)?->syncMaintenanceStatus();
            }
            return redirect()->route('admin.produksi')
                           ->with('success', sprintf('Produksi %s berhasil dihapus.', $identifier));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Update status of the specified resource.
     */
    public function updateStatus(Request $request, Produksi $produksi)
    {
        $kandangId = $produksi->kandang_id;
        $validated = $request->validate([
            'status' => 'required|in:aktif,tidak_aktif',
            'tanggal_akhir' => 'nullable|date',
        ]);

        try {
            $produksi->update($validated);

            $produksi->loadMissing('kandang');
            $produksi->kandang?->syncMaintenanceStatus();

            if ($kandangId && $kandangId !== $produksi->kandang_id) {
                Kandang::find($kandangId)?->syncMaintenanceStatus();
            }

            $produksi->loadMissing(['batchProduksi', 'pembesaran', 'penetasan']);
            $identifier = $produksi->batch_label;

            return redirect()->back()
                           ->with('success', sprintf('Status produksi %s berhasil diperbarui.', $identifier));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    protected function loadProduksiSumberList()
    {
        $lockedSourceIds = Produksi::query()
            ->where('jenis_input', 'dari_produksi')
            ->whereNotNull('produksi_sumber_id')
            ->pluck('produksi_sumber_id')
            ->filter()
            ->unique();

        $candidatesQuery = Produksi::with(['kandang', 'batchProduksi', 'pembesaran', 'penetasan'])
            ->where('tipe_produksi', 'puyuh')
            ->where('status', 'aktif');

        if ($lockedSourceIds->isNotEmpty()) {
            $candidatesQuery->whereNotIn('id', $lockedSourceIds->values());
        }

        $candidates = $candidatesQuery
            ->orderByDesc('tanggal_mulai')
            ->get();

        $withStats = $this->attachTelurStatsToProduksi($candidates);

        return $withStats->filter(function ($produksi) {
            return ($produksi->total_telur_tersedia ?? 0) > 0;
        })->values();
    }

    protected function attachTelurStatsToProduksi($produksiList)
    {
        $collection = collect($produksiList)->filter();

        if ($collection->isEmpty()) {
            return collect();
        }

        $batchIds = $collection->pluck('batch_produksi_id')->filter()->unique()->values();

        $laporanAggregates = collect();
        if ($batchIds->isNotEmpty()) {
            $laporanAggregates = LaporanHarian::select('batch_produksi_id')
                ->selectRaw('COALESCE(SUM(produksi_telur), 0) as total_telur')
                ->selectRaw('COALESCE(SUM(penjualan_telur_butir), 0) as total_telur_terjual')
                ->whereIn('batch_produksi_id', $batchIds)
                ->groupBy('batch_produksi_id')
                ->get()
                ->keyBy('batch_produksi_id');
        }

        $produksiIds = $collection->pluck('id')->filter()->unique()->values();

        $transferAggregates = collect();
        if ($produksiIds->isNotEmpty()) {
            $transferAggregates = Produksi::select('produksi_sumber_id', DB::raw('COALESCE(SUM(jumlah_telur), 0) as total_dialihkan'))
                ->whereNotNull('produksi_sumber_id')
                ->whereIn('produksi_sumber_id', $produksiIds)
                ->groupBy('produksi_sumber_id')
                ->get()
                ->keyBy('produksi_sumber_id');
        }

        return $collection->map(function ($produksi) use ($laporanAggregates, $transferAggregates) {
            $batchId = $produksi->batch_produksi_id;
            $laporan = $batchId ? $laporanAggregates->get($batchId) : null;
            $totalTelur = (int) ($laporan->total_telur ?? 0);
            $totalTerjual = (int) ($laporan->total_telur_terjual ?? 0);
            $dialihkan = (int) optional($transferAggregates->get($produksi->id))->total_dialihkan;
            $tersedia = max($totalTelur - $totalTerjual - $dialihkan, 0);

            $produksi->total_telur_tercatat = $totalTelur;
            $produksi->total_telur_terjual = $totalTerjual;
            $produksi->total_telur_sudah_dialihkan = $dialihkan;
            $produksi->total_telur_tersedia = $tersedia;

            return $produksi;
        });
    }

    protected function syncTelurTurunanFromPuyuh(?Produksi $sumberProduksi): void
    {
        if (!$sumberProduksi || $sumberProduksi->tipe_produksi !== 'puyuh') {
            return;
        }

        $turunan = $sumberProduksi->produksiTurunan()
            ->where('jenis_input', 'dari_produksi')
            ->get();

        if ($turunan->isEmpty()) {
            return;
        }

        $stats = $this->attachTelurStatsToProduksi(collect([$sumberProduksi]))->first();
        $totalTelurTersedia = (int) ($stats->total_telur_tersedia ?? 0);

        foreach ($turunan as $childProduksi) {
            $childProduksi->forceFill(['jumlah_telur' => $totalTelurTersedia])->save();
        }
    }

    protected function ensureTrayEntryBelongsToProduksi(Produksi $produksi, LaporanHarian $laporan): void
    {
        if (empty($produksi->batch_produksi_id) || $laporan->batch_produksi_id !== $produksi->batch_produksi_id) {
            abort(404);
        }
    }

    protected function generateDefaultTrayName(LaporanHarian $laporan): string
    {
        $tanggal = optional($laporan->tanggal)->locale('id')->translatedFormat('d M Y');
        $suffix = $laporan->id ? ' #' . $laporan->id : '';

        return trim(($tanggal ? 'Tray ' . $tanggal : 'Tray') . $suffix);
    }

    protected function logTrayHistory(Produksi $produksi, LaporanHarian $laporan, string $action, array $oldValues = []): ?TrayHistory
    {
        if (!in_array($action, ['created', 'updated', 'deleted'], true)) {
            return null;
        }

        if (!Schema::hasTable('vf_tray_histories')) {
            return null;
        }

        return TrayHistory::create([
            'produksi_id' => $produksi->id,
            'laporan_harian_id' => $laporan->id,
            'action' => $action,
            'nama_tray' => $laporan->nama_tray ?? $this->generateDefaultTrayName($laporan),
            'tanggal' => optional($laporan->tanggal)->toDateString(),
            'jumlah_telur' => $laporan->produksi_telur,
            'keterangan' => $laporan->keterangan_tray,
            'old_nama_tray' => $oldValues['nama_tray'] ?? null,
            'old_jumlah_telur' => $oldValues['jumlah_telur'] ?? null,
            'old_keterangan' => $oldValues['keterangan_tray'] ?? null,
            'pengguna_id' => Auth::id(),
        ]);
    }

    protected function formatTrayPayload(LaporanHarian $laporan): array
    {
        return [
            'id' => $laporan->id,
            'tanggal' => optional($laporan->tanggal)->locale('id')->translatedFormat('d M Y') ?? '-',
            'tanggal_raw' => optional($laporan->tanggal)->toDateString(),
            'jumlah_telur' => $laporan->produksi_telur,
            'nama_tray' => $laporan->nama_tray,
            'keterangan_tray' => $laporan->keterangan_tray,
            'dibuat_pada' => optional($laporan->dibuat_pada)->locale('id')->format('d/m/Y, g:i:s A') ?? '-',
        ];
    }

    protected function formatTrayHistoryPayload(?TrayHistory $history): ?array
    {
        if (!$history) {
            return null;
        }

        return [
            'id' => $history->id,
            'action' => $history->action,
            'nama_tray' => $history->nama_tray,
            'jumlah_telur' => $history->jumlah_telur,
            'tanggal' => optional($history->tanggal)->locale('id')->translatedFormat('d F Y') ?? '-',
            'timestamp' => optional($history->created_at)->locale('id')->format('d/m/Y, g:i:s A') ?? '-',
            'keterangan' => $history->keterangan,
            'old_nama_tray' => $history->old_nama_tray,
            'old_jumlah_telur' => $history->old_jumlah_telur,
            'old_keterangan' => $history->old_keterangan,
        ];
    }

    /**
     * Recalculate populasi produksi berdasarkan semua laporan harian (robust method)
     */
    private function recalculatePopulationFromLaporan(Produksi $produksi)
    {
        // Dapatkan populasi awal dari sumber data
        $initialJantan = 0;
        $initialBetina = 0;

        if ($produksi->pembesaran_id) {
            // Dari pembesaran
            $pembesaran = $produksi->pembesaran;
            if ($pembesaran) {
                $jenisKelamin = strtolower($pembesaran->jenis_kelamin ?? '');
                $totalIndukan = $produksi->jumlah_indukan ?? 0;

                if ($jenisKelamin === 'jantan') {
                    $initialJantan = $totalIndukan;
                    $initialBetina = 0;
                } elseif ($jenisKelamin === 'betina') {
                    $initialJantan = 0;
                    $initialBetina = $totalIndukan;
                } elseif ($jenisKelamin === 'campuran') {
                    // Jika ada breakdown tersimpan, gunakan itu
                    if ($produksi->jumlah_jantan && $produksi->jumlah_betina) {
                        $initialJantan = $produksi->jumlah_jantan;
                        $initialBetina = $produksi->jumlah_betina;
                    } else {
                        // Default split
                        $initialJantan = (int) ceil($totalIndukan / 2);
                        $initialBetina = $totalIndukan - $initialJantan;
                    }
                } else {
                    // Default ke betina jika tidak diketahui
                    $initialBetina = $totalIndukan;
                }
            }
        } elseif ($produksi->penetasan_id) {
            // Dari penetasan
            $penetasan = $produksi->penetasan;
            if ($penetasan) {
                $totalIndukan = $produksi->jumlah_indukan ?? 0;
                // Penetasan biasanya campuran, tapi gunakan data yang tersimpan
                if ($produksi->jumlah_jantan && $produksi->jumlah_betina) {
                    $initialJantan = $produksi->jumlah_jantan;
                    $initialBetina = $produksi->jumlah_betina;
                } else {
                    // Default split untuk penetasan
                    $initialJantan = (int) ceil($totalIndukan / 2);
                    $initialBetina = $totalIndukan - $initialJantan;
                }
            }
        } elseif ($produksi->produksi_sumber_id) {
            // Dari produksi lain
            $sumber = $produksi->produksiSumber;
            if ($sumber) {
                $initialJantan = $sumber->jumlah_jantan ?? 0;
                $initialBetina = $sumber->jumlah_betina ?? 0;
            }
        } else {
            // Manual input - gunakan data yang tersimpan saat create
            $initialJantan = $produksi->jumlah_jantan ?? 0;
            $initialBetina = $produksi->jumlah_betina ?? 0;
        }

        // Pastikan total awal sesuai
        $totalAwal = $initialJantan + $initialBetina;
        if ($totalAwal === 0 && $produksi->jumlah_indukan) {
            // Fallback jika tidak ada data gender, bagi rata
            $initialJantan = (int) ceil($produksi->jumlah_indukan / 2);
            $initialBetina = $produksi->jumlah_indukan - $initialJantan;
        }

        // Ambil semua laporan untuk batch ini
        $laporans = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
            ->where('tampilkan_di_histori', true)
            ->get();

        // Hitung total penjualan dan kematian
        $totalPenjualanJantan = 0;
        $totalPenjualanBetina = 0;
        $totalKematianJantan = 0;
        $totalKematianBetina = 0;

        foreach ($laporans as $laporan) {
            // Hitung penjualan
            if ($laporan->penjualan_puyuh_ekor > 0) {
                $jenis = strtolower($laporan->jenis_kelamin_penjualan ?? '');
                if ($jenis === 'campuran') {
                    $totalPenjualanJantan += $laporan->penjualan_puyuh_jantan ?? 0;
                    $totalPenjualanBetina += $laporan->penjualan_puyuh_betina ?? 0;
                } elseif ($jenis === 'jantan') {
                    $totalPenjualanJantan += $laporan->penjualan_puyuh_ekor;
                } elseif ($jenis === 'betina') {
                    $totalPenjualanBetina += $laporan->penjualan_puyuh_ekor;
                }
            }

            // Hitung kematian
            if ($laporan->jumlah_kematian > 0) {
                $jenis = strtolower($laporan->jenis_kelamin_kematian ?? '');
                if ($jenis === 'jantan') {
                    $totalKematianJantan += $laporan->jumlah_kematian;
                } elseif ($jenis === 'betina') {
                    $totalKematianBetina += $laporan->jumlah_kematian;
                } elseif ($jenis === 'campuran') {
                    // Distribusi proporsional berdasarkan stok saat ini (setelah undo)
                    $stokJantan = max(0, $initialJantan - $totalPenjualanJantan - $totalKematianJantan);
                    $stokBetina = max(0, $initialBetina - $totalPenjualanBetina - $totalKematianBetina);
                    $totalStok = $stokJantan + $stokBetina;
                    if ($totalStok > 0) {
                        $totalKematianJantan += (int) round($laporan->jumlah_kematian * ($stokJantan / $totalStok));
                        $totalKematianBetina += $laporan->jumlah_kematian - $totalKematianJantan;
                    } else {
                        $totalKematianBetina += $laporan->jumlah_kematian;
                    }
                }
            }
        }

        // Hitung populasi akhir: awal - penjualan - kematian
        $produksi->jumlah_jantan = max(0, $initialJantan - $totalPenjualanJantan - $totalKematianJantan);
        $produksi->jumlah_betina = max(0, $initialBetina - $totalPenjualanBetina - $totalKematianBetina);
        $produksi->jumlah_indukan = $produksi->jumlah_jantan + $produksi->jumlah_betina;

        $produksi->save();
    }
}
