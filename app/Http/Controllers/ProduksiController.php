<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Kandang;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
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
            // Reset egg production data if present
            if ($laporan->produksi_telur > 0 || (Schema::hasColumn('laporan_harian', 'input_telur') && $laporan->input_telur > 0)) {
                $laporan->produksi_telur = 0;
                if (Schema::hasColumn('laporan_harian', 'input_telur')) {
                    $laporan->input_telur = 0;
                }
                // Optionally clear sisa_telur so it doesn't carry stale value
                if (Schema::hasColumn('laporan_harian', 'sisa_telur')) {
                    $laporan->sisa_telur = null;
                }
            }

            // Reset feed consumption data if present
            if ($laporan->konsumsi_pakan_kg !== null) {
                $laporan->konsumsi_pakan_kg = 0;
                // Optionally clear sisa_pakan_kg so it doesn't carry stale value
                if (Schema::hasColumn('laporan_harian', 'sisa_pakan_kg')) {
                    $laporan->sisa_pakan_kg = null;
                }
            }

            // Reset vitamin data if present
            if ($laporan->vitamin_terpakai !== null) {
                $laporan->vitamin_terpakai = 0;
                // Optionally clear sisa_vitamin_liter so it doesn't carry stale value
                if (Schema::hasColumn('laporan_harian', 'sisa_vitamin_liter')) {
                    $laporan->sisa_vitamin_liter = null;
                }
            }

            // Reset death data if present
            if ($laporan->jumlah_kematian > 0) {
                $laporan->jumlah_kematian = 0;
                // Optionally clear related death fields
                $laporan->jenis_kelamin_kematian = null;
                $laporan->keterangan_kematian = null;
            }

            $laporan->save();

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
        $kandangList = Kandang::whereIn('status', ['aktif', 'maintenance'])
                              ->orderBy('nama_kandang')
                              ->get();
        
        // Get penetasan with available infertile eggs and load kandang relation
        // Only get completed penetasan with available infertile eggs
        $penetasanList = Penetasan::with('kandang')
                                  ->where('status', 'selesai')
                                  ->whereRaw('(telur_tidak_fertil - COALESCE(telur_infertil_ditransfer, 0)) > 0')
                                  ->orderBy('tanggal_menetas', 'desc')
                                  ->get();
        
        // Get pembesaran with available breeding stock and load kandang relation
        // Only get completed pembesaran with available stock
        $pembesaranList = Pembesaran::with('kandang')
                                    ->where('status_batch', 'selesai')
                                    ->whereRaw('(COALESCE(jumlah_siap, 0) - COALESCE(indukan_ditransfer, 0)) > 0')
                                    ->orderBy('tanggal_siap', 'desc')
                                    ->get();
        
        return view('admin.pages.produksi.create-produksi', compact('kandangList', 'penetasanList', 'pembesaranList'));
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
            'kandang_id' => 'required|exists:kandang,id',
            'jenis_input' => 'required|in:manual,dari_pembesaran,dari_penetasan',
            'batch_produksi_id' => 'nullable|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,tidak_aktif',
            'catatan' => 'nullable|string',
            'harga_per_kg' => 'nullable|numeric|min:0',
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
                'pembesaran_id' => 'required|exists:pembesaran,id',
                'jumlah_indukan' => 'required|integer|min:1',
                'jenis_kelamin' => 'nullable|in:jantan,betina,campuran',
                'jumlah_jantan' => 'nullable|integer|min:0',
                'jumlah_betina' => 'nullable|integer|min:0',
            ]);
        } elseif ($jenisInput === 'dari_penetasan') {
            $rules = array_merge($rules, [
                'penetasan_id' => 'required|exists:penetasan,id',
                'jumlah_telur' => 'required|integer|min:1',
                'berat_rata_telur' => 'nullable|numeric|min:0',
            ]);
        }

        $validated = $request->validate($rules, [
            'jumlah_jantan.min' => 'Jumlah jantan tidak boleh negatif',
            'jumlah_betina.min' => 'Jumlah betina tidak boleh negatif',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
        ]);

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
                
                // Check available stock
                $tersedia = $pembesaran->jumlah_siap - ($pembesaran->indukan_ditransfer ?? 0);
                if ($validated['jumlah_indukan'] > $tersedia) {
                    throw new \Exception("Jumlah indukan melebihi stok tersedia ({$tersedia})");
                }

                // Update pembesaran
                $pembesaran->increment('indukan_ditransfer', $validated['jumlah_indukan']);
                
                // Check if all stock transferred
                if ($pembesaran->indukan_ditransfer >= $pembesaran->jumlah_siap) {
                    $pembesaran->update(['status_batch' => 'selesai']);
                }
            }

            // Handle transfer from penetasan (infertile eggs)
            if ($validated['jenis_input'] === 'dari_penetasan' && $validated['penetasan_id']) {
                $penetasan = Penetasan::findOrFail($validated['penetasan_id']);
                
                // Check available stock
                $tersedia = $penetasan->telur_tidak_fertil - ($penetasan->telur_infertil_ditransfer ?? 0);
                if ($validated['jumlah_telur'] > $tersedia) {
                    throw new \Exception("Jumlah telur melebihi stok tersedia ({$tersedia})");
                }

                // Update penetasan
                $penetasan->increment('telur_infertil_ditransfer', $validated['jumlah_telur']);
            }

            Log::info('Creating production record', $validated);

            // Create produksi record
            $produksi = Produksi::create($validated);
            
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
        $produksi->load(['kandang', 'penetasan', 'pembesaran']);

        $laporanHarian = collect();

        if (!empty($produksi->batch_produksi_id)) {
            $laporanHarian = LaporanHarian::where('batch_produksi_id', $produksi->batch_produksi_id)
                ->orderByDesc('tanggal')
                ->orderByDesc('dibuat_pada')
                ->get();
        }

        $eggsPerTray = (int) config('produksi.eggs_per_tray', 30);

        $summary = [
            'total_telur' => $laporanHarian->sum('produksi_telur'),
            'total_kematian' => $laporanHarian->sum('jumlah_kematian'),
            'total_penjualan_telur' => $laporanHarian->sum('penjualan_telur_butir'),
            'total_penjualan_puyuh' => $laporanHarian->sum('penjualan_puyuh_ekor'),
            'total_pendapatan' => $laporanHarian->sum('pendapatan_harian'),
            'last_sisa_pakan' => optional($laporanHarian->first())->sisa_pakan_kg,
            'last_sisa_telur' => optional($laporanHarian->first())->sisa_telur,
            'laporan_count' => $laporanHarian->count(),
        ];

        $summary['total_telur_rusak'] = $laporanHarian->sum('telur_rusak');
        $summary['eggs_per_tray'] = max($eggsPerTray, 1);
        $summary['total_tray'] = $summary['eggs_per_tray'] > 0
            ? $summary['total_telur'] / $summary['eggs_per_tray']
            : 0;

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

        $currentMale = max($initialMale - $deathByGender['jantan'], 0);
        $currentFemale = max($initialFemale - $deathByGender['betina'], 0);
        $currentPopulationFromGender = max($currentMale + $currentFemale, 0);

        if ($currentPopulationFromGender === 0 && ($produksi->jumlah_indukan ?? 0) > 0) {
            $currentPopulationFromGender = max(($produksi->jumlah_indukan ?? 0) - $summary['total_kematian'], 0);
        }

        $summary['total_kematian_jantan'] = $deathByGender['jantan'];
        $summary['total_kematian_betina'] = $deathByGender['betina'];
        $summary['total_kematian_campuran'] = $deathByGender['campuran'];
        $summary['initial_jantan'] = $initialMale;
        $summary['initial_betina'] = $initialFemale;
        $summary['current_jantan'] = $currentMale;
        $summary['current_betina'] = $currentFemale;
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

        return view($view, compact(
            'produksi',
            'laporanHarian',
            'summary',
            'latestLaporan',
            'todayLaporan',
            'pencatatanProduksi',
            'historyClearRoute',
            'existingEntriesByTab'
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
        $request->merge($requestData);

        // Get active tab to determine required fields
        $activeTab = $request->input('active_tab');

        // Build validation rules based on active tab
        $rules = [
            'tanggal' => 'required|date',
            'active_tab' => 'required|in:telur,penjualan,pakan,vitamin,kematian,laporan',
            'produksi_telur' => 'nullable|integer|min:0',
            'jumlah_kematian' => 'nullable|integer|min:1',
            'jenis_kelamin_kematian' => 'nullable|in:jantan,betina',
            'keterangan_kematian' => 'nullable|string|max:225',
            'konsumsi_pakan_kg' => 'nullable|numeric|min:0',
            'sisa_pakan_kg' => 'nullable|numeric|min:0',
            'sisa_tray_bal' => 'nullable|numeric|min:0',
            'sisa_tray_lembar' => 'nullable|integer|min:0',
            'sisa_vitamin_liter' => 'nullable|numeric|min:0',
            'vitamin_terpakai' => 'nullable|numeric|min:0',
            'sisa_telur' => 'nullable|integer|min:0',
            'penjualan_telur_butir' => 'nullable|integer|min:0',
            'penjualan_puyuh_ekor' => 'nullable|integer|min:0',
            'pendapatan_harian' => 'nullable|numeric|min:0',
            'catatan_kejadian' => 'nullable|string|max:2500',
        ];

        // Make the main field required based on active tab
        switch ($activeTab) {
            case 'telur':
                $rules['produksi_telur'] = 'required|integer|min:0';
                break;
            case 'penjualan':
                $rules['penjualan_telur_butir'] = 'required|integer|min:0';
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
            'penjualan_puyuh_ekor.min' => 'Penjualan puyuh ekor tidak boleh negatif.',
            'pendapatan_harian.numeric' => 'Pendapatan harian harus berupa angka.',
            'pendapatan_harian.min' => 'Pendapatan harian tidak boleh negatif.',
            'catatan_kejadian.required' => 'Catatan kejadian harus diisi.',
            'catatan_kejadian.max' => 'Catatan kejadian maksimal 2500 karakter.',
            'sisa_telur.required_without_all' => 'Isi minimal salah satu data stok telur atau tray.',
            'penjualan_telur_butir.required' => 'Jumlah telur terjual harus diisi.',
        ]);

        $activeTab = $validated['active_tab'];

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
                if (isset($validated['penjualan_telur_butir']) && $validated['penjualan_telur_butir'] !== '') {
                    $updateData['penjualan_telur_butir'] = (int) $validated['penjualan_telur_butir'];
                }
                if (isset($validated['pendapatan_harian']) && $validated['pendapatan_harian'] !== '') {
                    $updateData['pendapatan_harian'] = (float) $validated['pendapatan_harian'];
                }
                break;

            case 'pakan':
                if (isset($validated['konsumsi_pakan_kg']) && $validated['konsumsi_pakan_kg'] !== null && $validated['konsumsi_pakan_kg'] !== '') {
                    $updateData['konsumsi_pakan_kg'] = (float) $validated['konsumsi_pakan_kg'];
                    $updateData['sisa_pakan_kg'] = isset($validated['sisa_pakan_kg']) && $validated['sisa_pakan_kg'] !== null && $validated['sisa_pakan_kg'] !== '' ? (float) $validated['sisa_pakan_kg'] : null;
                }
                break;

            case 'vitamin':
                if (isset($validated['vitamin_terpakai']) && $validated['vitamin_terpakai'] !== null && $validated['vitamin_terpakai'] !== '') {
                    $updateData['vitamin_terpakai'] = (float) $validated['vitamin_terpakai'];
                    $updateData['sisa_vitamin_liter'] = isset($validated['sisa_vitamin_liter']) && $validated['sisa_vitamin_liter'] !== ''
                        ? (float) $validated['sisa_vitamin_liter']
                        : null;
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

        // Handle other fields that might be submitted from any tab
        $otherFields = ['penjualan_puyuh_ekor'];
        foreach ($otherFields as $field) {
            if (isset($validated[$field]) && $validated[$field] !== null && $validated[$field] !== '') {
                $updateData[$field] = $validated[$field];
            }
        }

        $laporan->fill($updateData);
        $laporan->save();

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

        // Header dengan informasi tanggal dan batch
        $segments[] = "LAPORAN HARIAN PRODUKSI PUYUH";
        $segments[] = "Batch: {$produksi->batch_produksi_id}";
        $segments[] = "Tanggal: {$tanggalFormatted}";
        $segments[] = str_repeat("=", 50);

        // Telur summary
        $totalTelur = $laporanHarian->sum('produksi_telur');
        $penjualanTelur = $laporanHarian->sum('penjualan_telur_butir');
        $sisaTelur = optional($laporanHarian->first(fn ($item) => $item->sisa_telur !== null))->sisa_telur;

        if ($totalTelur > 0 || $penjualanTelur > 0 || $sisaTelur !== null) {
            $segments[] = "";
            $segments[] = "PRODUKSI TELUR:";
            $segments[] = "• Total telur dipanen: {$formatNumber($totalTelur)} butir";

            if ($penjualanTelur > 0) {
                $segments[] = "• Telur terjual: {$formatNumber($penjualanTelur)} butir";
            }

            if ($sisaTelur !== null) {
                $segments[] = "• Sisa telur: {$formatNumber($sisaTelur)} butir";
            }

            // Hitung persentase penjualan jika ada data penjualan
            if ($totalTelur > 0 && $penjualanTelur > 0) {
                $persentasePenjualan = round(($penjualanTelur / $totalTelur) * 100, 1);
                $segments[] = "• Persentase penjualan: {$persentasePenjualan}%";
            }
        }

        // Pakan summary
        $totalPakan = $laporanHarian->sum('konsumsi_pakan_kg');
        $sisaPakan = optional($laporanHarian->first(fn ($item) => $item->sisa_pakan_kg !== null))->sisa_pakan_kg;

        if ($totalPakan > 0 || $sisaPakan !== null) {
            $segments[] = "";
            $segments[] = "KONSUMSI PAKAN:";
            $segments[] = "• Total pakan terpakai: {$formatNumber($totalPakan, 2)} kg";

            if ($sisaPakan !== null) {
                $segments[] = "• Sisa pakan: {$formatNumber($sisaPakan, 2)} kg";
                $totalTersedia = $totalPakan + $sisaPakan;
                if ($totalTersedia > 0) {
                    $segments[] = "• Total pakan tersedia: {$formatNumber($totalTersedia, 2)} kg";
                }
            }
        }

        // Vitamin summary
        $totalVitamin = $laporanHarian->sum('vitamin_terpakai');
        $sisaVitamin = optional($laporanHarian->first(fn ($item) => $item->sisa_vitamin_liter !== null))->sisa_vitamin_liter;

        if ($totalVitamin > 0 || $sisaVitamin !== null) {
            $segments[] = "";
            $segments[] = "KONSUMSI VITAMIN:";
            $segments[] = "• Total vitamin terpakai: {$formatNumber($totalVitamin, 2)} liter";

            if ($sisaVitamin !== null) {
                $segments[] = "• Sisa vitamin: {$formatNumber($sisaVitamin, 2)} liter";
                $totalTersediaVitamin = $totalVitamin + $sisaVitamin;
                if ($totalTersediaVitamin > 0) {
                    $segments[] = "• Total vitamin tersedia: {$formatNumber($totalTersediaVitamin, 2)} liter";
                }
            }
        }

        // Death summary
        $totalKematian = $laporanHarian->sum('jumlah_kematian');
        if ($totalKematian > 0) {
            $segments[] = "";
            $segments[] = "KESEHATAN & MORTALITAS:";

            $genderBreakdown = [];
            $genderMap = ['jantan' => 'jantan', 'betina' => 'betina', 'campuran' => 'campuran'];
            foreach ($genderMap as $genderKey => $label) {
                $amount = $laporanHarian
                    ->where('jenis_kelamin_kematian', $genderKey)
                    ->sum('jumlah_kematian');
                if ($amount > 0) {
                    $genderBreakdown[] = "{$formatNumber($amount)} ekor {$label}";
                }
            }

            $segments[] = "• Total kematian: {$formatNumber($totalKematian)} ekor";

            if (!empty($genderBreakdown)) {
                $segments[] = "• Rincian kematian: " . implode(', ', $genderBreakdown);
            }

            // Hitung mortalitas rate jika ada data populasi
            $currentPopulation = $laporanHarian->max('jumlah_burung') ?? $produksi->jumlah_indukan;
            if ($currentPopulation > 0) {
                $mortalityRate = round(($totalKematian / $currentPopulation) * 100, 2);
                $segments[] = "• Tingkat mortalitas: {$mortalityRate}% (dari populasi {$formatNumber($currentPopulation)} ekor)";
            }
        }

        // Ringkasan dan rekomendasi
        $segments[] = "";
        $segments[] = "RINGKASAN HARIAN:";

        $summaryPoints = [];

        if ($totalTelur > 0) {
            $summaryPoints[] = "Produksi telur mencapai {$formatNumber($totalTelur)} butir";
        }

        if ($totalPakan > 0) {
            $summaryPoints[] = "Konsumsi pakan sebesar {$formatNumber($totalPakan, 2)} kg";
        }

        if ($totalKematian > 0) {
            $summaryPoints[] = "Terdapat {$formatNumber($totalKematian)} ekor kematian";
        }

        if (empty($summaryPoints)) {
            $summaryPoints[] = "Belum ada aktivitas produksi tercatat";
        }

        foreach ($summaryPoints as $point) {
            $segments[] = "• {$point}";
        }

        // Footer
        $segments[] = "";
        $segments[] = "Laporan ini dibuat secara otomatis pada " . now()->locale('id')->format('d F Y, H:i') . " WIB";
        $segments[] = "Sistem Manajemen Produksi Puyuh - Vigazafarm";

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
        $kandangList = Kandang::whereIn('status', ['aktif', 'maintenance'])
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
            'kandang_id' => 'required|exists:kandang,id',
            'batch_produksi_id' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,tidak_aktif',
            'catatan' => 'nullable|string',
            'harga_per_kg' => 'nullable|numeric|min:0',
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
}
