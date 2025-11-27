<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Kandang;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\LaporanHarian;
use App\Models\TrayHistory;
use App\Models\FeedVitaminItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
    public function resetLaporan(Produksi $produksi, LaporanHarian $laporan)
    {
        try {
            // Reset data produksi telur jika ada
            if ($laporan->produksi_telur > 0 || (Schema::hasColumn('vf_laporan_harian', 'input_telur') && $laporan->input_telur > 0)) {
                $laporan->produksi_telur = 0;
                if (Schema::hasColumn('vf_laporan_harian', 'input_telur')) {
                    $laporan->input_telur = 0;
                }
                // Opsional: hapus sisa_telur agar tidak membawa nilai lama
                if (Schema::hasColumn('vf_laporan_harian', 'sisa_telur')) {
                    $laporan->sisa_telur = null;
                }
            }

            // Reset data konsumsi pakan jika ada
            if ($laporan->konsumsi_pakan_kg !== null) {
                $laporan->konsumsi_pakan_kg = 0;
                // Opsional: hapus sisa_pakan_kg agar tidak membawa nilai lama
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

            // Reset vitamin data if present
            if ($laporan->vitamin_terpakai !== null) {
                $laporan->vitamin_terpakai = 0;
                // Optionally clear sisa_vitamin_liter so it doesn't carry stale value
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

            // Reset death data if present
            if (($laporan->jumlah_kematian ?? 0) > 0) {
                $laporan->jumlah_kematian = 0;
                $laporan->jenis_kelamin_kematian = null;
                $laporan->keterangan_kematian = null;
            }

            // Reset sales data (telur & puyuh) if present
            if (($laporan->penjualan_telur_butir ?? 0) > 0 || ($laporan->penjualan_puyuh_ekor ?? 0) > 0) {
                $laporan->penjualan_telur_butir = 0;
                $laporan->penjualan_puyuh_ekor = 0;
                $laporan->pendapatan_harian = 0;
                $laporan->tray_penjualan_id = null;
                $laporan->nama_tray_penjualan = null;
                $laporan->harga_per_butir = null;

                if (Schema::hasColumn('vf_laporan_harian', 'jenis_kelamin_penjualan')) {
                    $laporan->jenis_kelamin_penjualan = null;
                }
            }

            $laporan->save();

            $this->syncTelurTurunanFromPuyuh($produksi);

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
        $kandangList = Kandang::where(function ($query) {
                    $query->whereRaw('LOWER(tipe_kandang) = ?', ['produksi'])
                          ->whereIn('status', ['aktif', 'maintenance']);
                      })
                      ->orderBy('nama_kandang')
                      ->get();
        
        // Dapatkan pembesaran dengan stok breeding yang tersedia dan muat relasi kandang
        // Hanya dapat pembesaran yang telah selesai dengan stok tersedia
        $pembesaranList = Pembesaran::with('kandang')
                                    ->where('status_batch', 'selesai')
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
            // Generate batch ID if not provided
            if (empty($validated['batch_produksi_id'])) {
                $date = Carbon::parse($validated['tanggal_mulai']);
                $prefix = $validated['jenis_input'] === 'dari_penetasan' ? 'TELUR-INF' : 'PROD';
                $count = Produksi::whereDate('tanggal_mulai', $date)->count() + 1;
                $validated['batch_produksi_id'] = sprintf('%s-%s-%04d', $prefix, $date->format('Ymd'), $count);
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
            if ($sumberProduksi) {
                $this->syncTelurTurunanFromPuyuh($sumberProduksi);
            }
            
            Log::info('Production record created with ID: ' . $produksi->id);

            DB::commit();
            Log::info('Transaction committed successfully');
            $redirectUrl = route('admin.produksi');
            Log::info('Redirecting to: ' . $redirectUrl);
            $message = sprintf(
                'Produksi batch %s berhasil ditambahkan.',
                $produksi->batch_produksi_id ?? ('#' . $produksi->id)
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

        // Hitung sisa telur: total telur awal produksi - telur yang sudah dimasukkan ke tray
        $totalTelurAwal = $produksi->jumlah_telur ?? 0;
        $summary['sisa_telur'] = max(0, $totalTelurAwal - $totalTelurAktif);

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
                ->sum('penjualan_puyuh_ekor'),
            'betina' => $laporanHarian
                ->where('jenis_kelamin_penjualan', 'betina')
                ->sum('penjualan_puyuh_ekor'),
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
        $request->merge($requestData);

        // Dapatkan tab aktif untuk menentukan field yang diperlukan
        $activeTab = $request->input('active_tab');

        // Build validation rules based on active tab
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
            'jenis_kelamin_penjualan' => 'nullable|in:jantan,betina',
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
                    $rules['jenis_kelamin_penjualan'] = 'required|in:jantan,betina';
                    $rules['penjualan_puyuh_ekor'] = 'required|integer|min:1';
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

        // Find existing record or create new one
        $existingLaporan = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
            ->where('tanggal', $validated['tanggal'])
            ->first();

        // For telur/pakan/vitamin/kematian/laporan inputs, always create a new record to track each submission
        if (in_array($activeTab, ['telur', 'penjualan', 'pakan', 'vitamin', 'kematian', 'laporan'], true)) {
            $laporan = new LaporanHarian([
                'batch_produksi_id' => $produksi->batch_produksi_id,
                'tanggal' => $validated['tanggal'],
                'jumlah_burung' => $produksi->jumlah_indukan ?? 0,
            ]);
        }

        $isNewRecord = !$laporan->exists;

        // Only update fields that are relevant to the active tab
        $updateData = ['pengguna_id' => Auth::id()];

        switch ($activeTab) {
            case 'telur':
                if (isset($validated['produksi_telur']) && $validated['produksi_telur'] !== null && $validated['produksi_telur'] !== '') {
                    // For telur, store the input amount directly (each input creates a new record)
                    $updateData['produksi_telur'] = $validated['produksi_telur'];
                    $updateData['input_telur'] = $validated['produksi_telur'];
                }
                break;

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

                        $updateData['tray_penjualan_id'] = $validated['tray_penjualan'];
                        $updateData['penjualan_telur_butir'] = $validated['jumlah_telur_terjual'];
                        $updateData['harga_per_butir'] = $validated['harga_penjualan'];
                        $updateData['pendapatan_harian'] = $validated['jumlah_telur_terjual'] * $validated['harga_penjualan'];
                        $updateData['nama_tray_penjualan'] = $selectedTray->nama_tray;
                    }
                } else {
                    $jumlahTerjual = isset($validated['penjualan_puyuh_ekor']) ? (int) $validated['penjualan_puyuh_ekor'] : 0;
                    $hargaSatuan = isset($validated['harga_penjualan']) ? (float) $validated['harga_penjualan'] : 0;

                    if ($jumlahTerjual > 0) {
                        $updateData['penjualan_puyuh_ekor'] = $jumlahTerjual;
                        $updateData['jenis_kelamin_penjualan'] = $validated['jenis_kelamin_penjualan'] ?? null;
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
        $laporan->save();

        if ($activeTab === 'telur' && empty($laporan->nama_tray)) {
            $laporan->nama_tray = $this->generateDefaultTrayName($laporan);
            $laporan->save();
        }

        $this->syncTelurTurunanFromPuyuh($produksi);

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
        ]);

        if (empty($produksi->batch_produksi_id)) {
            return response()->json([
                'message' => 'Produksi ini belum memiliki kode batch.',
            ], 422);
        }

        $tanggal = Carbon::parse($validated['tanggal'])->toDateString();
        $tanggalFormatted = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        $laporanHarian = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('dibuat_pada')
            ->get();

        if ($laporanHarian->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada pencatatan lain pada tanggal tersebut.',
            ], 404);
        }

        $formatNumber = function ($value, $decimals = 0) {
            return number_format((float) ($value ?? 0), $decimals, ',', '.');
        };

        $segments = [];

        // Header profesional dengan informasi lengkap
        $segments[] = "═══════════════════════════════════════════════════════════════";
        $segments[] = "                    LAPORAN HARIAN PRODUKSI PUYUH";
        $segments[] = "═══════════════════════════════════════════════════════════════";
        $segments[] = "";
        $segments[] = "📋 INFORMASI BATCH";
        $segments[] = "• Kode Batch     : {$produksi->batch_produksi_id}";
        $kandangNama = $produksi->kandang ? $produksi->kandang->nama_kandang : 'Tidak ditentukan';
        $segments[] = "• Kandang        : {$kandangNama}";
        $segments[] = "• Tanggal Laporan: {$tanggalFormatted}";
        $segments[] = "• Periode Produksi: " . Carbon::parse($produksi->tanggal_mulai)->locale('id')->translatedFormat('d M Y') .
                      ($produksi->tanggal_akhir ? ' - ' . Carbon::parse($produksi->tanggal_akhir)->locale('id')->translatedFormat('d M Y') : ' (Sedang Berlangsung)');
        $segments[] = "• Status Batch   : " . ucfirst($produksi->status ?? 'aktif');
        $segments[] = "";

        // Ringkasan Eksekutif
        $totalTelur = $laporanHarian->sum('produksi_telur');
        $totalTray = $laporanHarian->whereNotNull('nama_tray')->where('produksi_telur', '>', 0)->count();
        $totalPenjualan = $laporanHarian->sum('penjualan_telur_butir');
        $totalPendapatan = $laporanHarian->sum('pendapatan_harian');
        $totalKematian = $laporanHarian->sum('jumlah_kematian');

        $segments[] = "📊 RINGKASAN EKSEKUTIF";
        $segments[] = "• Total Produksi Telur : {$formatNumber($totalTelur)} butir";
        $segments[] = "• Jumlah Tray Dibuat   : {$formatNumber($totalTray)} tray";
        $segments[] = "• Telur Terjual        : {$formatNumber($totalPenjualan)} butir";
        $segments[] = "• Total Pendapatan     : Rp {$formatNumber($totalPendapatan)}";
        $segments[] = "• Kematian Hari Ini    : {$formatNumber($totalKematian)} ekor";
        $segments[] = "";

        // Detail Produksi Telur
        if ($totalTelur > 0) {
            $segments[] = "🥚 DETAIL PRODUKSI TELUR";
            $segments[] = "• Total telur dipanen hari ini : {$formatNumber($totalTelur)} butir";

            // Breakdown per tray
            $trayEntries = $laporanHarian->whereNotNull('nama_tray')->where('produksi_telur', '>', 0);
            if ($trayEntries->count() > 0) {
                $segments[] = "• Rincian per tray:";
                foreach ($trayEntries as $tray) {
                    $trayName = $tray->nama_tray ?? 'Tray tanpa nama';
                    $trayEggs = $tray->produksi_telur;
                    $segments[] = "  - {$trayName}: {$formatNumber($trayEggs)} butir";
                }
            }

            // Penjualan detail
            if ($totalPenjualan > 0) {
                $segments[] = "• Penjualan telur hari ini : {$formatNumber($totalPenjualan)} butir";
                $totalRevenue = $laporanHarian->sum('pendapatan_harian');
                if ($totalRevenue > 0) {
                    $avgPrice = $totalPenjualan > 0 ? round($totalRevenue / $totalPenjualan, 0) : 0;
                    $segments[] = "• Pendapatan penjualan     : Rp {$formatNumber($totalRevenue)}";
                    $segments[] = "• Harga rata-rata per butir: Rp {$formatNumber($avgPrice)}";
                }

                $persentasePenjualan = round(($totalPenjualan / $totalTelur) * 100, 1);
                $segments[] = "• Persentase penjualan     : {$persentasePenjualan}% dari total produksi";
            }

            // Sisa telur
            $sisaTelur = optional($laporanHarian->first(fn ($item) => $item->sisa_telur !== null))->sisa_telur;
            if ($sisaTelur !== null) {
                $segments[] = "• Sisa telur di gudang     : {$formatNumber($sisaTelur)} butir";
            }

            // Telur rusak
            $totalTelurRusak = $laporanHarian->sum('telur_rusak');
            if ($totalTelurRusak > 0) {
                $segments[] = "• Telur rusak/ditolak       : {$formatNumber($totalTelurRusak)} butir";
            }
            $segments[] = "";
        }

        // Konsumsi Pakan
        $totalPakan = $laporanHarian->sum('konsumsi_pakan_kg');
        if ($totalPakan > 0) {
            $segments[] = "🌾 KONSUMSI PAKAN";
            $segments[] = "• Total pakan terpakai hari ini : {$formatNumber($totalPakan, 2)} kg";

            $sisaPakan = optional($laporanHarian->first(fn ($item) => $item->sisa_pakan_kg !== null))->sisa_pakan_kg;
            if ($sisaPakan !== null) {
                $segments[] = "• Sisa pakan di gudang         : {$formatNumber($sisaPakan, 2)} kg";
                $totalTersedia = $totalPakan + $sisaPakan;
                $segments[] = "• Total pakan tersedia          : {$formatNumber($totalTersedia, 2)} kg";

                // Hitung estimasi hari tersisa
                if ($totalPakan > 0) {
                    $hariTersisa = floor($sisaPakan / $totalPakan);
                    $segments[] = "• Estimasi pakan tersisa untuk  : {$hariTersisa} hari (berdasarkan konsumsi hari ini)";
                }
            }
            $segments[] = "";
        }

        // Konsumsi Vitamin
        $totalVitamin = $laporanHarian->sum('vitamin_terpakai');
        if ($totalVitamin > 0) {
            $segments[] = "💊 KONSUMSI VITAMIN";
            $segments[] = "• Total vitamin terpakai hari ini : {$formatNumber($totalVitamin, 2)} liter";

            $sisaVitamin = optional($laporanHarian->first(fn ($item) => $item->sisa_vitamin_liter !== null))->sisa_vitamin_liter;
            if ($sisaVitamin !== null) {
                $segments[] = "• Sisa vitamin di gudang         : {$formatNumber($sisaVitamin, 2)} liter";
                $totalTersediaVitamin = $totalVitamin + $sisaVitamin;
                $segments[] = "• Total vitamin tersedia          : {$formatNumber($totalTersediaVitamin, 2)} liter";
            }
            $segments[] = "";
        }

        // Kesehatan dan Mortalitas
        if ($totalKematian > 0) {
            $segments[] = "🏥 KESEHATAN & MORTALITAS";
            $segments[] = "• Total kematian hari ini : {$formatNumber($totalKematian)} ekor";

            $genderBreakdown = [];
            $genderMap = ['jantan' => 'Jantan', 'betina' => 'Betina', 'campuran' => 'Campuran'];
            foreach ($genderMap as $genderKey => $label) {
                $amount = $laporanHarian
                    ->where('jenis_kelamin_kematian', $genderKey)
                    ->sum('jumlah_kematian');
                if ($amount > 0) {
                    $genderBreakdown[] = "{$formatNumber($amount)} ekor {$label}";
                }
            }

            if (!empty($genderBreakdown)) {
                $segments[] = "• Rincian kematian berdasarkan jenis kelamin: " . implode(', ', $genderBreakdown);
            }

            // Hitung mortalitas rate
            $currentPopulation = $laporanHarian->max('jumlah_burung') ?? $produksi->jumlah_indukan;
            if ($currentPopulation > 0) {
                $mortalityRate = round(($totalKematian / $currentPopulation) * 100, 2);
                $segments[] = "• Tingkat mortalitas hari ini : {$mortalityRate}% (dari populasi {$formatNumber($currentPopulation)} ekor)";

                // Analisis kesehatan
                if ($mortalityRate > 5) {
                    $segments[] = "• ⚠️  PERHATIAN: Tingkat mortalitas tinggi (>5%) - Perlu perhatian khusus";
                } elseif ($mortalityRate > 2) {
                    $segments[] = "• ⚠️  PERHATIAN: Tingkat mortalitas sedang (2-5%) - Monitor kondisi kesehatan";
                } else {
                    $segments[] = "• ✅ Kondisi kesehatan dalam batas normal";
                }
            }

            // Keterangan kematian
            $deathNotes = $laporanHarian->whereNotNull('keterangan_kematian')->pluck('keterangan_kematian')->filter()->unique();
            if ($deathNotes->count() > 0) {
                $segments[] = "• Catatan kematian: " . $deathNotes->implode('; ');
            }
            $segments[] = "";
        }

        // Analisis Performa
        $segments[] = "📈 ANALISIS PERFORMA HARIAN";

        $performancePoints = [];

        // Analisis produksi telur
        if ($totalTelur > 0 && $produksi->jumlah_indukan > 0) {
            $productivityRate = round(($totalTelur / $produksi->jumlah_indukan) * 100, 2);
            $performancePoints[] = "• Produktivitas telur: {$productivityRate}% (telur per indukan per hari)";

            if ($productivityRate >= 80) {
                $performancePoints[] = "  ✅ Produktivitas sangat baik (≥80%)";
            } elseif ($productivityRate >= 60) {
                $performancePoints[] = "  ⚠️  Produktivitas cukup baik (60-79%)";
            } else {
                $performancePoints[] = "  ❌ Produktivitas rendah (<60%) - Perlu evaluasi";
            }
        }

        // Analisis efisiensi pakan
        if ($totalTelur > 0 && $totalPakan > 0) {
            $feedEfficiency = round($totalTelur / $totalPakan, 2);
            $performancePoints[] = "• Efisiensi pakan: {$feedEfficiency} butir telur per kg pakan";

            if ($feedEfficiency >= 15) {
                $performancePoints[] = "  ✅ Efisiensi pakan sangat baik (≥15 butir/kg)";
            } elseif ($feedEfficiency >= 10) {
                $performancePoints[] = "  ⚠️  Efisiensi pakan cukup baik (10-14 butir/kg)";
            } else {
                $performancePoints[] = "  ❌ Efisiensi pakan rendah (<10 butir/kg) - Perlu optimasi";
            }
        }

        if (empty($performancePoints)) {
            $performancePoints[] = "• Belum cukup data untuk analisis performa";
        }

        $segments = array_merge($segments, $performancePoints);
        $segments[] = "";

        // Rekomendasi
        $segments[] = "💡 REKOMENDASI & TINDAK LANJUT";

        $recommendations = [];

        if ($totalKematian > 0) {
            $recommendations[] = "• Pantau kondisi kesehatan puyuh secara intensif";
            $recommendations[] = "• Periksa kualitas pakan dan vitamin yang diberikan";
            $recommendations[] = "• Pastikan kebersihan kandang dan ventilasi yang baik";
        }

        if ($totalTelur > 0 && $totalPenjualan > 0) {
            $unsoldEggs = $totalTelur - $totalPenjualan;
            if ($unsoldEggs > 100) {
                $recommendations[] = "• Optimalkan penjualan telur - masih ada {$formatNumber($unsoldEggs)} butir belum terjual";
            }
        }

        if ($totalTray > 0) {
            $recommendations[] = "• Pastikan tray disimpan dalam kondisi optimal untuk menjaga kualitas telur";
        }

        if (empty($recommendations)) {
            $recommendations[] = "• Lanjutkan pemantauan rutin produksi harian";
            $recommendations[] = "• Pastikan pencatatan data dilakukan secara konsisten";
        }

        $segments = array_merge($segments, $recommendations);
        $segments[] = "";

        // Footer profesional
        $segments[] = "═══════════════════════════════════════════════════════════════";
        $segments[] = "📝 CATATAN TAMBAHAN";
        $segments[] = "• Laporan ini dibuat secara otomatis oleh Sistem Manajemen Produksi Puyuh";
        $segments[] = "• Waktu pembuatan: " . now()->locale('id')->format('d F Y, H:i:s') . " WIB";
        $segments[] = "• Dicatat oleh: " . (Auth::user()->nama_pengguna ?? Auth::user()->username ?? 'Sistem');
        $segments[] = "• Periode pelaporan: Harian";
        $segments[] = "";
        $segments[] = "🏢 PT. VIGA ZA FARM - Manajemen Produksi Puyuh Terintegrasi";
        $segments[] = "═══════════════════════════════════════════════════════════════";

        if (empty(array_filter($segments, fn($s) => !empty(trim($s))))) {
            $segments = ["Belum ada data otomatis untuk tanggal ini. Lengkapi pencatatan terlebih dahulu."];
        }

        return response()->json([
            'summary' => implode("\n", $segments),
            'date' => $tanggal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produksi $produksi)
    {
        $kandangList = Kandang::where(function ($query) {
                    $query->whereRaw('LOWER(tipe_kandang) = ?', ['produksi'])
                          ->whereIn('status', ['aktif', 'maintenance']);
                      })
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
            $produksi->update($validated);

            $identifier = $produksi->batch_produksi_id ? 'batch ' . $produksi->batch_produksi_id : '#' . $produksi->id;

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

            $identifier = $produksi->batch_produksi_id ? 'batch ' . $produksi->batch_produksi_id : '#' . $produksi->id;

            $produksi->delete();

            DB::commit();
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
        $validated = $request->validate([
            'status' => 'required|in:aktif,tidak_aktif',
            'tanggal_akhir' => 'nullable|date',
        ]);

        try {
            $produksi->update($validated);

            $identifier = $produksi->batch_produksi_id ? 'batch ' . $produksi->batch_produksi_id : '#' . $produksi->id;

            return redirect()->back()
                           ->with('success', sprintf('Status produksi %s berhasil diperbarui.', $identifier));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    protected function loadProduksiSumberList()
    {
        $candidates = Produksi::with('kandang')
            ->where('tipe_produksi', 'puyuh')
            ->where('status', 'aktif')
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
}
