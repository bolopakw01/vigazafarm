<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;
use App\Models\Pembesaran;
use App\Models\Produksi;
use App\Models\Kandang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransferController extends Controller
{
    /**
     * Transfer DOC dari Penetasan ke Pembesaran
     */
    public function transferDocToPembesaran(Request $request, $penetasanId)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'jumlah_doc' => 'required|integer|min:1',
            'jenis_kelamin' => 'nullable|in:jantan,betina,campuran',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $penetasan = Penetasan::findOrFail($penetasanId);
            
            // Validasi ketersediaan DOC
            $docTersedia = $penetasan->doc_tersedia;
            if ($request->jumlah_doc > $docTersedia) {
                return redirect()->back()->with('error', "DOC tidak cukup. Tersedia: {$docTersedia}, diminta: {$request->jumlah_doc}");
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
                'catatan' => $request->catatan,
            ]);

            // Update tracking di penetasan
            $penetasan->increment('doc_ditransfer', $request->jumlah_doc);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil transfer {$request->jumlah_doc} DOC ke pembesaran (Batch: {$batchId})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal transfer DOC: ' . $e->getMessage());
        }
    }

    /**
     * Transfer Indukan dari Pembesaran ke Produksi
     */
    public function transferIndukanToProduksi(Request $request, $pembesaranId)
    {
        $request->validate([
            'kandang_id' => 'required|exists:kandang,id',
            'jumlah_indukan' => 'required|integer|min:1',
            'tanggal_mulai_produksi' => 'required|date',
            'catatan' => 'nullable|string',
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
                'catatan' => $request->catatan,
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
        $request->validate([
            'jumlah_telur' => 'required|integer|min:1',
            'harga_per_pcs' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
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
                'catatan' => 'Telur infertil dari penetasan. ' . ($request->catatan ?? ''),
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
        $penetasan = Penetasan::with('kandang')->findOrFail($penetasanId);
        $kandangList = Kandang::where('status', 'aktif')->get();
        
        return view('admin.pages.penetasan.transfer-penetasan', compact('penetasan', 'kandangList'));
    }

    /**
     * Tampilkan halaman transfer untuk pembesaran
     */
    public function showTransferPembesaran($pembesaranId)
    {
        $pembesaran = Pembesaran::with('kandang', 'penetasan')->findOrFail($pembesaranId);
        $kandangList = Kandang::where('status', 'aktif')->where('tipe_kandang', 'produksi')->get();
        
        return view('admin.pages.pembesaran.transfer-pembesaran', compact('pembesaran', 'kandangList'));
    }
}
