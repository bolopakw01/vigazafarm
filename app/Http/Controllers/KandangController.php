<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kandang;

/**
 * ==========================================
 * Controller : KandangController
 * Deskripsi  : Mengelola CRUD data kandang beserta pencarian dan paginasi untuk tampilan admin.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class KandangController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Menampilkan daftar kandang dengan dukungan pencarian dan paginasi.
         */
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = Kandang::with(['penetasan', 'pembesaran', 'produksi']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_kandang', 'like', "%{$search}%")
                  ->orWhere('nama_kandang', 'like', "%{$search}%")
                  ->orWhere('tipe_kandang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $kandang = $perPage === 'all'
            ? $query->orderBy('nama_kandang')->get()
            : $query->orderBy('nama_kandang')->paginate($perPage);

        return view('admin.pages.kandang.index-kandang', compact('kandang'));
    }

    public function create()
    {
        /**
         * Menampilkan form pembuatan data kandang baru.
         */
        return view('admin.pages.kandang.create-kandang');
    }

    public function store(Request $request)
    {
        /**
         * Memvalidasi dan menyimpan data kandang baru ke database.
         */
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer|min:0',
            'tipe_kandang' => 'required|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:100',
        ]);

        // Generate kode_kandang secara otomatis jika tidak disediakan
        if (!isset($data['kode_kandang']) || empty($data['kode_kandang'])) {
            $data['kode_kandang'] = $this->generateKodeKandang();
        }

        $kandang = Kandang::create($data);

        $kandang->syncMaintenanceStatus();

        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dibuat');
    }

    public function show(Kandang $kandang)
    {
        /**
         * Menampilkan detail sebuah kandang.
         */
        return view('admin.pages.kandang.show-kandang', compact('kandang'));
    }

    public function edit(Kandang $kandang)
    {
        /**
         * Menampilkan form edit untuk data kandang yang dipilih.
         */
        return view('admin.pages.kandang.edit-kandang', compact('kandang'));
    }

    public function update(Request $request, Kandang $kandang)
    {
        /**
         * Memvalidasi dan memperbarui data kandang yang dipilih.
         */
        $data = $request->validate([
            'nama_kandang' => 'required|string|max:191',
            'kapasitas_maksimal' => 'nullable|integer|min:0',
            'tipe_kandang' => 'required|string|max:100',
            'status' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:100',
        ]);

        // Generate kode_kandang secara otomatis jika tidak disediakan dan record saat ini tidak memilikinya
        if (!isset($data['kode_kandang']) || empty($data['kode_kandang'])) {
            if (empty($kandang->kode_kandang)) {
                $data['kode_kandang'] = $this->generateKodeKandang();
            }
        }

        $kandang->update($data);

        $kandang->syncMaintenanceStatus();

        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil diperbarui');
    }

    public function destroy(Kandang $kandang)
    {
        // Cek keterkaitan data sebelum hapus
        $relations = [
            'penetasan' => $kandang->penetasan()->select('id', 'batch', 'status')->get(),
            'pembesaran' => $kandang->pembesaran()->select('id', 'batch_produksi_id', 'status_batch')->get(),
            'produksi' => $kandang->produksi()->select('id', 'batch_produksi_id', 'status')->get(),
        ];

        // Karantina dicatat di tabel kesehatan dengan kandang_tujuan_id
        $karantina = \App\Models\Kesehatan::query()
            ->where('kandang_tujuan_id', $kandang->id)
            ->where('tipe_kegiatan', \App\Models\Kesehatan::TIPE_KARANTINA)
            ->where('karantina_dikembalikan', false)
            ->select('id', 'batch_produksi_id', 'tanggal', 'nama_vaksin_obat')
            ->get();

        $hasRelations = $relations['penetasan']->isNotEmpty()
            || $relations['pembesaran']->isNotEmpty()
            || $relations['produksi']->isNotEmpty()
            || $karantina->isNotEmpty();

        if ($hasRelations) {
            $detail = collect();

            $relations['penetasan']->each(function ($item) use ($detail) {
                $detail->push("Penetasan batch {$item->batch} (status: " . ($item->status ?? '-') . ' )');
            });

            $relations['pembesaran']->each(function ($item) use ($detail) {
                $detail->push("Pembesaran batch {$item->batch_produksi_id} (status: " . ($item->status_batch ?? '-') . ' )');
            });

            $relations['produksi']->each(function ($item) use ($detail) {
                $detail->push("Produksi batch {$item->batch_produksi_id} (status: " . ($item->status ?? '-') . ' )');
            });

            $karantina->each(function ($item) use ($detail) {
                $label = $item->batch_produksi_id ? "Batch {$item->batch_produksi_id}" : 'Tanpa batch';
                $detail->push("Karantina {$label} (tanggal: " . ($item->tanggal ?? '-') . ' )');
            });

            return redirect()
                ->route('admin.kandang')
                ->with('kandang_blocked', [
                    'nama' => $kandang->nama_kandang,
                    'detail' => $detail->values()->all(),
                ]);
        }

        $kandang->delete();
        return redirect()->route('admin.kandang')->with('success', 'Kandang berhasil dihapus');
    }

    /**
     * Generate kode kandang unik dengan mempertimbangkan record soft-delete.
     */
    protected function generateKodeKandang(): string
    {
        $prefix = 'KDG-';
        $latestNumber = Kandang::withTrashed()
            ->where('kode_kandang', 'like', $prefix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(kode_kandang, ?) AS UNSIGNED)) as max_code', [strlen($prefix) + 1])
            ->value('max_code');

        $nextNumber = ((int) $latestNumber) + 1;

        do {
            $candidate = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Kandang::withTrashed()->where('kode_kandang', $candidate)->exists());

        return $candidate;
    }
}
