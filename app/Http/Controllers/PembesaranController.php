<?php

namespace App\Http\Controllers;

use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Kandang;
use Illuminate\Http\Request;

class PembesaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembesaran = Pembesaran::with(['kandang', 'penetasan'])
            ->orderBy('dibuat_pada', 'desc')
            ->paginate(10);

        return view('admin.pages.pembesaran.index-pembesaran', compact('pembesaran'));
    }

    /**
     * Show the form for creating a new resource from penetasan.
     */
    public function createFromPenetasan($penetasanId)
    {
        $penetasan = Penetasan::with('kandang')->findOrFail($penetasanId);
        
        // Validasi status penetasan harus selesai
        if ($penetasan->status !== 'selesai') {
            return redirect()->route('admin.penetasan')
                ->with('error', 'Hanya penetasan dengan status "selesai" yang dapat dipindahkan ke pembesaran.');
        }

        // Validasi harus ada jumlah DOC
        if (!$penetasan->jumlah_doc || $penetasan->jumlah_doc <= 0) {
            return redirect()->route('admin.penetasan')
                ->with('error', 'Penetasan harus memiliki jumlah DOC yang valid untuk dipindahkan ke pembesaran.');
        }

        // Ambil kandang pembesaran
        $kandangList = Kandang::where('tipe_kandang', 'pembesaran')
            ->where('status', 'aktif')
            ->get();

        return view('admin.pages.pembesaran.create-from-penetasan', compact('penetasan', 'kandangList'));
    }

    /**
     * Store a newly created resource in storage from penetasan.
     */
    public function storeFromPenetasan(Request $request, $penetasanId)
    {
        $validated = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'catatan' => 'nullable|string',
        ]);

        $penetasan = Penetasan::findOrFail($penetasanId);

        // Validasi jumlah tidak melebihi DOC yang tersedia
        if ($validated['jumlah_anak_ayam'] > $penetasan->jumlah_doc) {
            return back()->withInput()
                ->withErrors(['jumlah_anak_ayam' => 'Jumlah anak ayam tidak boleh melebihi jumlah DOC yang tersedia (' . $penetasan->jumlah_doc . ')']);
        }

        Pembesaran::create([
            'penetasan_id' => $penetasan->id,
            'kandang_id' => $validated['kandang_id'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'jumlah_anak_ayam' => $validated['jumlah_anak_ayam'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? 'campuran',
            'status_batch' => 'Aktif',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil ditambahkan dari penetasan batch: ' . $penetasan->batch);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kandangList = Kandang::where('tipe_kandang', 'pembesaran')
            ->where('status', 'aktif')
            ->get();

        // Ambil daftar penetasan yang selesai dan punya DOC
        $penetasanList = Penetasan::where('status', 'selesai')
            ->where('jumlah_doc', '>', 0)
            ->orderBy('tanggal_menetas', 'desc')
            ->get();

        // Generate batch code otomatis
        $today = date('Ymd');
        $lastBatch = Pembesaran::whereDate('dibuat_pada', today())
            ->latest('dibuat_pada')
            ->first();
        
        if ($lastBatch && $lastBatch->batch_produksi_id) {
            // Extract nomor urut dari batch terakhir
            $lastNumber = intval(substr($lastBatch->batch_produksi_id, -3));
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }
        
        $generatedBatch = 'PB-' . $today . '-' . $nextNumber;

        return view('admin.pages.pembesaran.create-pembesaran', compact('kandangList', 'penetasanList', 'generatedBatch'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'penetasan_id' => 'nullable|exists:penetasan,id',
            'batch_produksi_id' => 'required|string|unique:pembesaran,batch_produksi_id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'umur_hari' => 'nullable|integer|min:0',
            'tanggal_siap' => 'nullable|date|after:tanggal_masuk',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'target_berat_akhir' => 'nullable|numeric|min:0',
            'kondisi_doc' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Set default values
        $validated['status_batch'] = 'Aktif';
        $validated['jenis_kelamin'] = $validated['jenis_kelamin'] ?? 'campuran';

        Pembesaran::create($validated);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil ditambahkan dengan batch: ' . $validated['batch_produksi_id']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembesaran $pembesaran)
    {
        $pembesaran->load(['kandang', 'penetasan']);
        
        // Hitung metrics
        $populasiAwal = $pembesaran->jumlah_anak_ayam;
        $totalMati = \App\Models\Kematian::totalKematianByBatch($pembesaran->batch_produksi_id);
        $populasiSaatIni = $populasiAwal - $totalMati;
        $mortalitas = \App\Models\Kematian::hitungMortalitasKumulatif($pembesaran->batch_produksi_id, $populasiAwal);
        
        // Hitung total konsumsi pakan & biaya
        $totalPakan = \App\Models\Pakan::totalKonsumsiByBatch($pembesaran->batch_produksi_id);
        $totalBiayaPakan = \App\Models\Pakan::totalBiayaByBatch($pembesaran->batch_produksi_id);
        
        // Hitung total biaya kesehatan & vaksinasi
        $totalBiayaKesehatan = \App\Models\Kesehatan::getTotalBiayaKesehatan($pembesaran->batch_produksi_id);
        
        // Hitung umur hari (menggunakan startOfDay agar hasilnya integer)
        $umurHari = \Carbon\Carbon::parse($pembesaran->tanggal_masuk)->startOfDay()->diffInDays(\Carbon\Carbon::now()->startOfDay());
        
        // Get stok pakan untuk dropdown
        $stokPakanList = \App\Models\StokPakan::where('stok_kg', '>', 0)
            ->orderBy('nama_pakan')
            ->get();
        
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
            'paramBeratStandar',
            'paramSuhuStandar',
            'paramKelembabanStandar',
            'reminders'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembesaran $pembesaran)
    {
        $kandangList = Kandang::where('tipe_kandang', 'pembesaran')
            ->where('status', 'aktif')
            ->get();

        return view('admin.pages.pembesaran.edit-pembesaran', compact('pembesaran', 'kandangList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembesaran $pembesaran)
    {
        $validated = $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal_masuk' => 'required|date',
            'jumlah_anak_ayam' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:betina,jantan,campuran',
            'tanggal_siap' => 'nullable|date|after_or_equal:tanggal_masuk',
            'jumlah_siap' => 'nullable|integer|min:0',
            'umur_hari' => 'nullable|integer|min:0',
            'berat_rata_rata' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $pembesaran->update($validated);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembesaran $pembesaran)
    {
        $pembesaran->delete();

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil dihapus.');
    }
}
