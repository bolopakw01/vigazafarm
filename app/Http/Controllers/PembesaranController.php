<?php

namespace App\Http\Controllers;

use App\Models\Pembesaran;
use App\Models\Penetasan;
use App\Models\Kandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $kandangList = Kandang::orderBy('nama_kandang')->get();

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

        $pembesaran = Pembesaran::create([
            'penetasan_id' => $penetasan->id,
            'kandang_id' => $validated['kandang_id'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'jumlah_anak_ayam' => $validated['jumlah_anak_ayam'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? 'campuran',
            'status_batch' => 'Aktif',
            'catatan' => $validated['catatan'] ?? null,
        ]);

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

        $pembesaran = Pembesaran::create($validated);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran berhasil ditambahkan dengan batch: ' . ($pembesaran->batch_produksi_id ?? $validated['batch_produksi_id']));
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
        $kandangList = Kandang::orderBy('nama_kandang')->get();

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

        // Owner atau Super Admin bisa update status
    $user = Auth::user();
        if ($user && ($user->peran === 'owner' || $user->peran === 'super_admin')) {
            $validated = array_merge($validated, $request->validate([
                'status_batch' => 'nullable|in:Aktif,Selesai',
                'tanggal_selesai' => 'nullable|date',
            ]));
        }

        $pembesaran->update($validated);

        return redirect()->route('admin.pembesaran')
            ->with('success', 'Data pembesaran dengan batch: ' . ($pembesaran->batch_produksi_id ?? ('ID ' . $pembesaran->id)) . ' berhasil diperbarui.');
    }

    /**
     * Selesaikan batch pembesaran
     */
    public function selesaikanBatch(Pembesaran $pembesaran)
    {
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
        $batchLabel = $pembesaran->batch_produksi_id ?? null;
        $identifier = 'ID: ' . $pembesaran->id;

        $pembesaran->delete();

        return redirect()->route('admin.pembesaran')
            ->with(
                'success',
                'Data pembesaran ' . $identifier . ($batchLabel ? ' (Batch: ' . $batchLabel . ')' : '') . ' berhasil dihapus.'
            );
    }
}
