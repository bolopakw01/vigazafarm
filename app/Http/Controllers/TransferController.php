<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\Produksi;
use App\Models\Kandang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * ==========================================
 * Controller : TransferController
 * Deskripsi  : Mengatur perpindahan DOQ, indukan, dan telur antar modul penetasan, pembesaran, serta produksi.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class TransferController extends Controller
{
    /**
     * Transfer DOQ dari Penetasan ke Pembesaran
     */
    public function transferDocToPembesaran(Request $request, $penetasanId)
    {
        /**
         * Melakukan transfer DOQ dari entri penetasan ke modul pembesaran, membuat batch pembesaran baru.
         */
        $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'jumlah_doc' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:jantan,betina,campuran',
            'catatan' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $penetasan = Penetasan::findOrFail($penetasanId);
            
            // Validasi ketersediaan DOQ
            $docTersedia = $penetasan->doc_tersedia;
            if ($request->jumlah_doc > $docTersedia) {
                return redirect()->back()->with('error', "DOQ tidak cukup. Tersedia: {$docTersedia}, diminta: {$request->jumlah_doc}");
            }

            // Buat batch produksi ID
            $batchId = 'PEMB-' . date('Ymd') . '-' . str_pad($penetasan->id, 4, '0', STR_PAD_LEFT);

            // Buat record pembesaran
            $pembesaran = Pembesaran::create([
                'kandang_id' => $request->kandang_id,
                'batch_produksi_id' => $batchId,
                'penetasan_id' => $penetasanId,
                'tanggal_masuk' => Carbon::now(),
                'jumlah_anak_ayam' => $request->jumlah_doc,
                'jenis_kelamin' => $request->jenis_kelamin ?? 'campuran',
                'status_batch' => 'aktif',
                'umur_hari' => 0,
                'kondisi_doc' => 'baik',
                'catatan' => $this->normalizeNote($request->catatan),
            ]);

            // Update tracking di penetasan
            $penetasan->increment('doc_ditransfer', $request->jumlah_doc);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil transfer {$request->jumlah_doc} DOQ ke pembesaran (Batch: {$batchId})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal transfer DOQ: ' . $e->getMessage());
        }
    }

    /**
     * Transfer Indukan dari Pembesaran ke Produksi
     */
    public function transferIndukanToProduksi(Request $request, $pembesaranId)
    {
        /**
         * Memindahkan indukan dari pembesaran ke modul produksi, membuat record produksi baru.
         */
        $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'jumlah_indukan' => 'required|integer|min:1',
            'tanggal_mulai_produksi' => 'required|date',
            'catatan' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $pembesaran = Pembesaran::findOrFail($pembesaranId);
            
            // Validasi ketersediaan indukan
            $indukanTersedia = $pembesaran->indukan_tersedia;
            if ($request->jumlah_indukan > $indukanTersedia) {
                return redirect()->back()->with('error', "Indukan tidak cukup. Tersedia: {$indukanTersedia}, diminta: {$request->jumlah_indukan}");
            }

            // Buat batch produksi ID
            $batchId = 'PROD-' . date('Ymd') . '-' . str_pad($pembesaran->id, 4, '0', STR_PAD_LEFT);

            // Hitung umur burung
            $umurHari = $pembesaran->umur_hari ?? Carbon::parse($pembesaran->tanggal_masuk)->diffInDays(Carbon::now());

            // Buat record produksi
            $produksi = Produksi::create([
                'kandang_id' => $request->kandang_id,
                'batch_produksi_id' => $batchId,
                'pembesaran_id' => $pembesaranId,
                'jumlah_indukan' => $request->jumlah_indukan,
                'umur_mulai_produksi' => $umurHari,
                'tanggal_mulai' => $request->tanggal_mulai_produksi,
                'status' => 'aktif',
                'catatan' => $this->normalizeNote($request->catatan),
            ]);

            // Update tracking di pembesaran
            $pembesaran->increment('indukan_ditransfer', $request->jumlah_indukan);

            // Update status pembesaran jika semua sudah ditransfer
            if ($pembesaran->indukan_ditransfer >= $pembesaran->jumlah_siap) {
                $pembesaran->update(['status_batch' => 'selesai']);
            }

            DB::commit();

            return redirect()->back()->with('success', "Berhasil transfer {$request->jumlah_indukan} indukan ke produksi (Batch: {$batchId})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal transfer indukan: ' . $e->getMessage());
        }
    }

    /**
     * Transfer Telur Infertil dari Penetasan ke Produksi (untuk dijual sebagai telur konsumsi)
     */
    public function transferTelurInfertilToProduksi(Request $request, $penetasanId)
    {
        /**
         * Mencatat telur infertil dari penetasan sebagai produksi (untuk penjualan telur konsumsi).
         */
        $request->validate([
            'jumlah_telur' => 'required|integer|min:1',
            'harga_per_pcs' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $penetasan = Penetasan::findOrFail($penetasanId);
            
            // Validasi ketersediaan telur infertil
            $telurTersedia = $penetasan->telur_infertil_tersedia;
            if ($request->jumlah_telur > $telurTersedia) {
                return redirect()->back()->with('error', "Telur infertil tidak cukup. Tersedia: {$telurTersedia}, diminta: {$request->jumlah_telur}");
            }

            // Buat batch ID
            $batchId = 'TELUR-INF-' . date('Ymd') . '-' . str_pad($penetasan->id, 4, '0', STR_PAD_LEFT);

            // Buat record produksi untuk telur infertil
            $produksi = Produksi::create([
                'kandang_id' => $penetasan->kandang_id,
                'batch_produksi_id' => $batchId,
                'penetasan_id' => $penetasanId,
                'jumlah_telur' => $request->jumlah_telur,
                'harga_per_pcs' => $request->harga_per_pcs ?? 0,
                'tanggal_mulai' => Carbon::now(),
                'status' => 'tidak_aktif', // Langsung dinonaktifkan karena hanya record penjualan
                'catatan' => $this->normalizeNote('Telur infertil dari penetasan.' . ($request->filled('catatan') ? ' ' . $request->catatan : '')),
            ]);

            // Update tracking di penetasan
            $penetasan->increment('telur_infertil_ditransfer', $request->jumlah_telur);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil mencatat {$request->jumlah_telur} telur infertil ke produksi (Batch: {$batchId})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal transfer telur infertil: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman transfer untuk penetasan
     */
    public function showTransferPenetasan($penetasanId)
    {
        /**
         * Menampilkan halaman transfer untuk penetasan, menampilkan daftar kandang tujuan.
         */
        $penetasan = Penetasan::with('kandang')->findOrFail($penetasanId);
        $kandangList = Kandang::query()
            ->statusIn(['aktif', 'maintenance'])
            ->orderBy('nama_kandang')
            ->get();
        
        return view('admin.pages.penetasan.transfer-penetasan', compact('penetasan', 'kandangList'));
    }

    /**
     * Tampilkan halaman transfer untuk pembesaran
     */
    public function showTransferPembesaran($pembesaranId)
    {
        /**
         * Menampilkan halaman transfer untuk pembesaran, menampilkan daftar kandang produksi.
         */
        $pembesaran = Pembesaran::with('kandang', 'penetasan')->findOrFail($pembesaranId);
        $kandangList = Kandang::query()
            ->statusIn(['aktif', 'maintenance'])
            ->typeIs('produksi')
            ->orderBy('nama_kandang')
            ->get();
        
        return view('admin.pages.pembesaran.transfer-pembesaran', compact('pembesaran', 'kandangList'));
    }

    private function normalizeNote(?string $note): ?string
    {
        if ($note === null) {
            return null;
        }

        $trimmed = trim($note);

        if ($trimmed === '') {
            return null;
        }

        return Str::limit($trimmed, 100, '');
    }
}
