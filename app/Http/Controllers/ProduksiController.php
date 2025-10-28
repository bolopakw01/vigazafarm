<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\Kandang;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $penetasanList = Penetasan::with('kandang')
                                  ->whereIn('status', ['selesai', 'proses'])
                                  ->whereRaw('(telur_tidak_fertil - COALESCE(telur_infertil_ditransfer, 0)) > 0')
                                  ->orderBy('tanggal_menetas', 'desc')
                                  ->get();
        
        // Get pembesaran with available breeding stock and load kandang relation
        $pembesaranList = Pembesaran::with('kandang')
                                    ->whereIn('status_batch', ['aktif', 'Aktif', 'selesai'])
                                    ->where(function($query) {
                                        $query->whereRaw('(COALESCE(jumlah_siap, 0) - COALESCE(indukan_ditransfer, 0)) > 0')
                                              ->orWhere('status_batch', 'Aktif'); // Always show active batches
                                    })
                                    ->orderBy('tanggal_masuk', 'desc')
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
        
        // Create a new request with mapped data
        $request = new Request($mappedData);

        $validated = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'jenis_input' => 'required|in:manual,dari_pembesaran,dari_penetasan',
            'batch_produksi_id' => 'nullable|string|max:50',
            'jumlah_indukan' => 'nullable|integer|min:1',
            'umur_mulai_produksi' => 'nullable|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai',
            'catatan' => 'nullable|string',
            // For transfer from pembesaran
            'pembesaran_id' => 'nullable|exists:pembesaran,id',
            // For transfer from penetasan (infertile eggs)
            'penetasan_id' => 'nullable|exists:penetasan,id',
            'jumlah_telur' => 'nullable|integer|min:0',
            'berat_rata_telur' => 'nullable|numeric|min:0',
            'harga_per_kg' => 'nullable|numeric|min:0',
        ]);

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
            return redirect()->route('admin.produksi')
                           ->with('success', 'Data produksi berhasil ditambahkan!');

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
        return view('admin.pages.produksi.show-produksi', compact('produksi'));
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
        $validated = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'batch_produksi_id' => 'required|string|max:50',
            'jumlah_indukan' => 'required|integer|min:1',
            'umur_mulai_produksi' => 'nullable|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:aktif,selesai',
            'catatan' => 'nullable|string',
            'jumlah_telur' => 'nullable|integer|min:0',
            'berat_rata_telur' => 'nullable|numeric|min:0',
            'harga_per_kg' => 'nullable|numeric|min:0',
        ]);

        try {
            $produksi->update($validated);

            return redirect()->route('admin.produksi')
                           ->with('success', 'Data produksi berhasil diperbarui!');

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

            $produksi->delete();

            DB::commit();
            return redirect()->route('admin.produksi')
                           ->with('success', 'Data produksi berhasil dihapus!');

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
            'status' => 'required|in:aktif,selesai',
            'tanggal_akhir' => 'nullable|date',
        ]);

        try {
            $produksi->update($validated);

            return redirect()->back()
                           ->with('success', 'Status produksi berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
