<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;
use App\Models\Kandang;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * ==========================================
 * Controller : PenetasanController
 * Deskripsi  : Mengelola proses penetasan mulai pembuatan batch, pembaruan status, hingga penyelesaian.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class PenetasanController extends Controller
{
    public function create()
    {
        /**
         * Menampilkan form pembuatan penetasan baru (pilih kandang dan tanggal simpan telur).
         */
        $kandang = Kandang::whereRaw('LOWER(tipe_kandang) = ?', ['penetasan'])
            ->where('status', 'aktif')
            ->orderBy('nama_kandang')
            ->get();
        return view('admin.pages.penetasan.create-penetasan', compact('kandang'));
    }

    public function store(Request $request)
    {
        /**
         * Menyimpan data penetasan baru, melakukan perhitungan estimasi tanggal menetas, dan meng-generate batch.
         */
        $data = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'tanggal_simpan_telur' => 'required|date',
            'estimasi_tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_telur' => 'required|integer|min:1',
            'tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_menetas' => 'nullable|integer|min:0',
            'jumlah_doc' => 'nullable|integer|min:0',
            'suhu_penetasan' => 'nullable|numeric|min:0|max:50',
            'kelembaban_penetasan' => 'nullable|numeric|min:0|max:100',
            'telur_tidak_fertil' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:100',
        ]);

        $kandang = Kandang::findOrFail($data['kandang_id']);
        $kapasitasTersisa = $kandang->kapasitas_tersisa;
        $jumlahTelur = (int) $data['jumlah_telur'];

        if ($kapasitasTersisa <= 0) {
            return back()->withInput()->withErrors([
                'kandang_id' => sprintf(
                    'Kandang %s sudah penuh. Kosongkan terlebih dahulu sebelum menambah batch baru.',
                    $kandang->nama_kandang ?? ('#' . $kandang->id)
                ),
            ]);
        }

        if ($jumlahTelur > $kapasitasTersisa) {
            return back()->withInput()->withErrors([
                'jumlah_telur' => sprintf(
                    'Jumlah telur (%s) melebihi sisa kapasitas %s pada kandang %s.',
                    number_format($jumlahTelur),
                    number_format($kapasitasTersisa),
                    $kandang->nama_kandang ?? ('#' . $kandang->id)
                ),
            ]);
        }

        if (empty($data['estimasi_tanggal_menetas'])) {
            $data['estimasi_tanggal_menetas'] = Carbon::parse($data['tanggal_simpan_telur'])->addDays(17);
        }

        // Auto-generate unique batch code
        $data['batch'] = $this->generateUniqueBatch();

        // Calculate persentase_tetas if data is complete
        if (isset($data['jumlah_telur']) && isset($data['jumlah_menetas']) && $data['jumlah_telur'] > 0) {
            $data['persentase_tetas'] = ($data['jumlah_menetas'] / $data['jumlah_telur']) * 100;
        }

        // Set status default to 'proses'
        $data['status'] = 'proses';
        $data['fase_penetasan'] = 'setter';
        $data['created_by'] = Auth::id();

        $penetasan = Penetasan::create($data);

        return redirect()
            ->route('admin.penetasan')
            ->with('success', sprintf('Penetasan batch %s berhasil ditambahkan.', $penetasan->batch));
    }

    /**
     * Generate unique batch code for penetasan
     */
    private function generateUniqueBatch()
    {
        do {
            // Format: PTN-YYYYMMDD-XXX (PTN = Penetasan)
            $date = date('Ymd');
            $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $batch = "PTN-{$date}-{$random}";
            
            // Periksa apakah batch sudah ada
            $exists = Penetasan::where('batch', $batch)->exists();
        } while ($exists);

        return $batch;
    }

    public function show(Penetasan $penetasan)
    {
        /**
         * Menampilkan detail sebuah penetasan.
         */
        return view('admin.pages.penetasan.show', compact('penetasan'));
    }

    public function edit(Penetasan $penetasan)
    {
        /**
         * Menampilkan form edit untuk penetasan yang dipilih.
         */
        $kandang = Kandang::where(function ($query) {
                $query->whereRaw('LOWER(tipe_kandang) = ?', ['penetasan'])
                    ->where('status', 'aktif');
            })
            ->when($penetasan->kandang_id, function ($query) use ($penetasan) {
                $query->orWhere('id', $penetasan->kandang_id);
            })
            ->orderBy('nama_kandang')
            ->get();
        return view('admin.pages.penetasan.edit-penetasan', compact('penetasan', 'kandang'));
    }

    public function update(Request $request, Penetasan $penetasan)
    {
        /**
         * Memvalidasi dan memperbarui data penetasan, termasuk penetapan status jika tanggal menetas terisi.
         */
        $data = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'tanggal_simpan_telur' => 'required|date',
            'estimasi_tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'tanggal_masuk_hatcher' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_telur' => 'required|integer|min:1',
            'tanggal_menetas' => 'nullable|date|after_or_equal:tanggal_simpan_telur',
            'jumlah_menetas' => 'nullable|integer|min:0',
            'jumlah_doc' => 'nullable|integer|min:0',
            'suhu_penetasan' => 'nullable|numeric|min:0|max:50',
            'kelembaban_penetasan' => 'nullable|numeric|min:0|max:100',
            'telur_tidak_fertil' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:100',
            'status' => 'nullable|in:proses,selesai,gagal',
            'fase_penetasan' => 'nullable|in:setter,hatcher',
        ]);

        $user = Auth::user();
        $canOwnerOverride = $user && in_array($user->peran, ['owner', 'super_admin']);
        $ownerOverrideActive = $canOwnerOverride && $request->boolean('owner_override_active');

        if (empty($data['estimasi_tanggal_menetas'])) {
            $data['estimasi_tanggal_menetas'] = Carbon::parse($data['tanggal_simpan_telur'])->addDays(17);
        }

        // Calculate persentase_tetas if data is complete
        if ($ownerOverrideActive && isset($data['jumlah_telur'], $data['jumlah_menetas']) && $data['jumlah_telur'] > 0) {
            $data['persentase_tetas'] = ($data['jumlah_menetas'] / $data['jumlah_telur']) * 100;
        } else {
            unset($data['persentase_tetas']);
        }

        // Auto-set status to selesai if tanggal_menetas is filled and status not manually set
        if ($ownerOverrideActive && !empty($data['tanggal_menetas']) && !isset($data['status'])) {
            $data['status'] = 'selesai';
        }

        if ($ownerOverrideActive) {
            $faseOverride = $data['fase_penetasan'] ?? null;

            if ($faseOverride === 'setter') {
                $data['tanggal_masuk_hatcher'] = null;
            }

            if (($faseOverride ?? $penetasan->fase_penetasan) === 'hatcher' && empty($data['tanggal_masuk_hatcher'])) {
                $data['tanggal_masuk_hatcher'] = $penetasan->target_hatcher_date
                    ? $penetasan->target_hatcher_date->copy()
                    : Carbon::parse($data['tanggal_simpan_telur'])->addDays(14);
            }
        } else {
            unset(
                $data['status'],
                $data['tanggal_menetas'],
                $data['jumlah_menetas'],
                $data['jumlah_doc'],
                $data['fase_penetasan'],
                $data['tanggal_masuk_hatcher']
            );
        }

        $data['updated_by'] = Auth::id();

        $penetasan->update($data);

        $identifier = $penetasan->batch ? 'batch ' . $penetasan->batch : '#' . $penetasan->id;

        return redirect()
            ->route('admin.penetasan')
            ->with('success', sprintf('Penetasan %s berhasil diperbarui.', $identifier));
    }

    public function updateStatus(Request $request, Penetasan $penetasan)
    {
        /**
         * Mengubah status penetasan (hanya untuk owner).
         */
        // Only owner can update status
    if (Auth::user()->peran !== 'owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:proses,selesai,gagal',
        ]);

        $penetasan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $penetasan->status
        ]);
    }

    public function moveToHatcher(Request $request, Penetasan $penetasan)
    {
        /**
         * Memindahkan batch dari Setter ke Hatcher saat mencapai target hari ke-14.
         */
        if ($penetasan->fase_penetasan === 'hatcher' && !empty($penetasan->tanggal_masuk_hatcher)) {
            return response()->json([
                'message' => 'Batch sudah berada dalam fase Hatcher.'
            ], 422);
        }

        $data = $request->validate([
            'kandang_id' => 'required|exists:vf_kandang,id',
            'tanggal_masuk_hatcher' => 'nullable|date',
        ]);

        $kandang = Kandang::findOrFail($data['kandang_id']);
        $kandangStatus = strtolower((string) $kandang->status);
        $allowedStatuses = ['aktif', 'active', 'berjalan', 'proses'];

        if (!in_array($kandangStatus, $allowedStatuses, true)) {
            return response()->json([
                'message' => 'Kandang yang dipilih tidak aktif untuk penetasan.'
            ], 422);
        }

        $targetDate = $penetasan->target_hatcher_date;
        $today = Carbon::now()->startOfDay();

        if ($targetDate && $today->lt($targetDate->copy()->startOfDay())) {
            return response()->json([
                'message' => 'Batch belum memasuki jadwal Hatcher.'
            ], 422);
        }

        $requestedDate = !empty($data['tanggal_masuk_hatcher'])
            ? Carbon::parse($data['tanggal_masuk_hatcher'])->startOfDay()
            : Carbon::now()->startOfDay();

        if ($targetDate && $requestedDate->lt($targetDate->copy()->startOfDay())) {
            $requestedDate = $targetDate->copy()->startOfDay();
        }

        $penetasan->kandang_id = $data['kandang_id'];
        $penetasan->fase_penetasan = 'hatcher';
        $penetasan->tanggal_masuk_hatcher = $requestedDate;
        $penetasan->updated_by = Auth::id();
        $penetasan->save();

        return response()->json([
            'success' => true,
            'message' => 'Batch berhasil dipindahkan ke Hatcher.',
            'data' => [
                'fase_penetasan' => $penetasan->fase_penetasan,
                'tanggal_masuk_hatcher' => optional($penetasan->tanggal_masuk_hatcher)->format('Y-m-d'),
                'kandang_id' => $penetasan->kandang_id,
            ],
        ]);
    }

    public function finish(Request $request, Penetasan $penetasan)
    {
        /**
         * Menyelesaikan proses penetasan: mencatat jumlah DOQ, jumlah menetas, dan menetapkan status selesai.
         */
        if ($penetasan->status === 'selesai') {
            return response()->json([
                'message' => 'Batch sudah berstatus selesai.'
            ], 422);
        }

        $maxTelurRule = $penetasan->jumlah_telur ? '|max:' . $penetasan->jumlah_telur : '';

        $data = $request->validate([
            'jumlah_doc' => 'required|integer|min:0' . $maxTelurRule,
            'jumlah_menetas' => 'nullable|integer|min:0' . $maxTelurRule,
            'tanggal_menetas' => 'nullable|date',
        ]);

        if (isset($data['jumlah_menetas']) && $data['jumlah_doc'] > $data['jumlah_menetas']) {
            return response()->json([
                'errors' => [
                    'jumlah_doc' => ['Jumlah DOQ tidak boleh melebihi jumlah menetas.'],
                ],
            ], 422);
        }

        $penetasan->jumlah_doc = $data['jumlah_doc'];

        if (isset($data['jumlah_menetas'])) {
            $penetasan->jumlah_menetas = $data['jumlah_menetas'];
        } elseif (empty($penetasan->jumlah_menetas)) {
            $penetasan->jumlah_menetas = $data['jumlah_doc'];
        }

        $penetasan->tanggal_menetas = isset($data['tanggal_menetas'])
            ? Carbon::parse($data['tanggal_menetas'])
            : ($penetasan->tanggal_menetas ?? Carbon::now());

        if ($penetasan->jumlah_telur && $penetasan->jumlah_menetas) {
            $penetasan->persentase_tetas = ($penetasan->jumlah_menetas / max(1, $penetasan->jumlah_telur)) * 100;
        }

        if ($penetasan->fase_penetasan !== 'hatcher') {
            $penetasan->fase_penetasan = 'hatcher';
        }

        if (empty($penetasan->tanggal_masuk_hatcher)) {
            if ($penetasan->target_hatcher_date) {
                $penetasan->tanggal_masuk_hatcher = $penetasan->target_hatcher_date->copy();
            } elseif ($penetasan->tanggal_menetas) {
                $penetasan->tanggal_masuk_hatcher = $penetasan->tanggal_menetas->copy()->subDays(3);
            } else {
                $start = $penetasan->tanggal_simpan_telur instanceof Carbon
                    ? $penetasan->tanggal_simpan_telur->copy()
                    : Carbon::parse($penetasan->tanggal_simpan_telur ?? Carbon::now());

                $penetasan->tanggal_masuk_hatcher = $start->addDays(14);
            }
        }

        $penetasan->status = 'selesai';
        $penetasan->save();

        return response()->json([
            'success' => true,
            'message' => 'Penetasan berhasil diselesaikan.',
            'data' => [
                'status' => $penetasan->status,
                'jumlah_doc' => $penetasan->jumlah_doc,
                'jumlah_menetas' => $penetasan->jumlah_menetas,
                'tanggal_menetas' => optional($penetasan->tanggal_menetas)->format('Y-m-d'),
                'persentase_tetas' => $penetasan->persentase_tetas,
            ],
        ]);
    }


    public function destroy(Penetasan $penetasan)
    {
        /**
         * Menghapus record penetasan dari database.
         */
        $identifier = $penetasan->batch ? 'batch ' . $penetasan->batch : '#' . $penetasan->id;
        $penetasan->delete();

        return redirect()
            ->route('admin.penetasan')
            ->with('success', sprintf('Penetasan %s berhasil dihapus.', $identifier));
    }
}
