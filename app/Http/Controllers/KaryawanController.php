<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * ==========================================
 * Controller : KaryawanController
 * Deskripsi  : Mengatur manajemen data karyawan termasuk unggah foto profil dan pengaturan peran.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        /**
         * Menampilkan daftar karyawan dengan filter pencarian dan paginasi.
         */
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = User::query();

        // Kecualikan pengguna yang sedang login
        $query->where('id', '!=', Auth::id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nama_pengguna', 'like', "%{$search}%")
                  ->orWhere('surel', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%")
                  ->orWhere('peran', 'like', "%{$search}%");
            });
        }

        $karyawan = $perPage === 'all'
            ? $query->orderBy('nama')->get()
            : $query->orderBy('nama')->paginate($perPage);

        return view('admin.pages.karyawan.index-karyawan', compact('karyawan'));
    }

    public function create()
    {
        /**
         * Menampilkan form pembuatan karyawan baru.
         */
        return view('admin.pages.karyawan.create-karyawan');
    }

    public function store(Request $request)
    {
        /**
         * Memvalidasi input dan menyimpan karyawan baru (termasuk unggah foto profil).
         */
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_pengguna' => 'required|string|max:255|unique:vf_pengguna,nama_pengguna',
            'surel' => 'required|email|max:255|unique:vf_pengguna,surel',
            'nomor_telepon' => 'required|string|max:30',
            'kata_sandi' => 'required|string|min:8',
            'peran' => 'required|string|in:owner,operator',
            'alamat' => 'nullable|string|max:500',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $this->validationMessages(), $this->validationAttributes());

        // Tangani unggah file
        if ($request->hasFile('foto_profil')) {
            $destination = public_path('foto_profil');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file = $request->file('foto_profil');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $data['foto_profil'] = $filename;
        }

        $data['kata_sandi'] = Hash::make($data['kata_sandi']);

        User::create($data);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dibuat');
    }

    public function show(User $karyawan)
    {
        /**
         * Menampilkan detail data karyawan.
         */
        return view('admin.pages.karyawan.show-karyawan', compact('karyawan'));
    }

    public function edit(User $karyawan)
    {
        /**
         * Menampilkan form edit untuk data karyawan.
         */
        return view('admin.pages.karyawan.edit-karyawan', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
    {
        /**
         * Memvalidasi dan memperbarui data karyawan termasuk pengelolaan foto profil.
         */
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_pengguna' => 'required|string|max:255|unique:vf_pengguna,nama_pengguna,' . $karyawan->id,
            'surel' => 'required|email|max:255|unique:vf_pengguna,surel,' . $karyawan->id,
            'nomor_telepon' => 'required|string|max:30',
            'peran' => 'required|string|in:owner,operator',
            'alamat' => 'nullable|string|max:500',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $this->validationMessages(), $this->validationAttributes());

        $removePhoto = $request->boolean('remove_profile_picture');
        $destination = public_path('foto_profil');

        // Tangani unggah file
        if ($request->hasFile('foto_profil')) {
            // Hapus file lama jika ada
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            if ($karyawan->foto_profil) {
                $existingPath = $destination . DIRECTORY_SEPARATOR . $karyawan->foto_profil;
                if (File::exists($existingPath)) {
                    File::delete($existingPath);
                }
            }

            $file = $request->file('foto_profil');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $data['foto_profil'] = $filename;
        } elseif ($removePhoto && $karyawan->foto_profil) {
            $existingPath = $destination . DIRECTORY_SEPARATOR . $karyawan->foto_profil;
            if (File::exists($existingPath)) {
                File::delete($existingPath);
            }
            $data['foto_profil'] = null;
        }

        // Hanya perbarui kata sandi jika disediakan
        if ($request->filled('kata_sandi')) {
            $request->validate([
                'kata_sandi' => 'string|min:8',
            ], $this->validationMessages(), $this->validationAttributes());
            $data['kata_sandi'] = Hash::make($request->kata_sandi);
        }

        $karyawan->update($data);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(User $karyawan)
    {
        /**
         * Menghapus karyawan beserta foto profil dari penyimpanan jika ada.
         */
        // Hapus foto profil jika ada
        if ($karyawan->foto_profil) {
            $photoPath = public_path('foto_profil/' . $karyawan->foto_profil);
            if (File::exists($photoPath)) {
                File::delete($photoPath);
            }
        }

        $karyawan->delete();
        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dihapus');
    }

    protected function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email' => ':attribute harus berupa alamat surel yang valid.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'unique' => ':attribute sudah digunakan.',
            'in' => ':attribute tidak valid.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat: :values.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'nama' => 'Nama',
            'nama_pengguna' => 'Username',
            'surel' => 'Email',
            'nomor_telepon' => 'Nomor telepon',
            'kata_sandi' => 'Kata sandi',
            'peran' => 'Peran',
            'alamat' => 'Alamat',
            'foto_profil' => 'Foto profil',
        ];
    }
}