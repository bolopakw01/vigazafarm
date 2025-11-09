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
                ->get();
        }

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

        $latestLaporan = $laporanHarian->first();
        $todayLaporan = $laporanHarian->firstWhere('tanggal', Carbon::today()->toDateString());

        $pencatatanProduksi = $produksi->pencatatanProduksi()
            ->orderByDesc('tanggal')
            ->get();

        $view = $produksi->tipe_produksi === 'telur'
            ? 'admin.pages.produksi.show-telur'
            : 'admin.pages.produksi.show-puyuh';

        return view($view, compact(
            'produksi',
            'laporanHarian',
            'summary',
            'latestLaporan',
            'todayLaporan',
            'pencatatanProduksi'
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

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jumlah_burung' => 'required|integer|min:0',
            'produksi_telur' => 'nullable|integer|min:0',
            'jumlah_kematian' => 'nullable|integer|min:0',
            'konsumsi_pakan_kg' => 'nullable|numeric|min:0',
            'sisa_pakan_kg' => 'nullable|numeric|min:0',
            'sisa_tray_bal' => 'nullable|numeric|min:0',
            'sisa_tray_lembar' => 'nullable|integer|min:0',
            'sisa_vitamin_liter' => 'nullable|numeric|min:0',
            'sisa_telur' => 'nullable|integer|min:0',
            'penjualan_telur_butir' => 'nullable|integer|min:0',
            'penjualan_puyuh_ekor' => 'nullable|integer|min:0',
            'pendapatan_harian' => 'nullable|numeric|min:0',
            'catatan_kejadian' => 'nullable|string|max:1000',
        ]);

        $laporan = LaporanHarian::updateOrCreate(
            [
                'batch_produksi_id' => $produksi->batch_produksi_id,
                'tanggal' => $validated['tanggal'],
            ],
            [
                'jumlah_burung' => $validated['jumlah_burung'],
                'produksi_telur' => $validated['produksi_telur'] ?? 0,
                'jumlah_kematian' => $validated['jumlah_kematian'] ?? 0,
                'konsumsi_pakan_kg' => $validated['konsumsi_pakan_kg'] ?? 0,
                'sisa_pakan_kg' => $validated['sisa_pakan_kg'] ?? null,
                'sisa_tray_bal' => $validated['sisa_tray_bal'] ?? null,
                'sisa_tray_lembar' => $validated['sisa_tray_lembar'] ?? null,
                'sisa_vitamin_liter' => $validated['sisa_vitamin_liter'] ?? null,
                'sisa_telur' => $validated['sisa_telur'] ?? null,
                'penjualan_telur_butir' => $validated['penjualan_telur_butir'] ?? null,
                'penjualan_puyuh_ekor' => $validated['penjualan_puyuh_ekor'] ?? null,
                'pendapatan_harian' => $validated['pendapatan_harian'] ?? null,
                'catatan_kejadian' => $validated['catatan_kejadian'] ?? null,
                'pengguna_id' => Auth::id(),
            ]
        );

        return redirect()
            ->route('admin.produksi.show', $produksi->id)
            ->with('success', $laporan->wasRecentlyCreated ? 'Laporan harian berhasil ditambahkan.' : 'Laporan harian berhasil diperbarui.');
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
