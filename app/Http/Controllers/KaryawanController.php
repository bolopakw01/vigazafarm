<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search', '');

        $query = User::query();

        // Exclude current logged in user
        $query->where('id', '!=', Auth::id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nama_pengguna', 'like', "%{$search}%")
                  ->orWhere('surel', 'like', "%{$search}%")
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
        return view('admin.pages.karyawan.create-karyawan');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_pengguna' => 'required|string|max:255|unique:pengguna,nama_pengguna',
            'surel' => 'required|email|max:255|unique:pengguna,surel',
            'kata_sandi' => 'required|string|min:8',
            'peran' => 'required|string|in:owner,operator',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_profil', $filename);
            $data['foto_profil'] = $filename;
        }

        $data['kata_sandi'] = Hash::make($data['kata_sandi']);

        User::create($data);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dibuat');
    }

    public function show(User $karyawan)
    {
        return view('admin.pages.karyawan.show-karyawan', compact('karyawan'));
    }

    public function edit(User $karyawan)
    {
        return view('admin.pages.karyawan.edit-karyawan', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_pengguna' => 'required|string|max:255|unique:pengguna,nama_pengguna,' . $karyawan->id,
            'surel' => 'required|email|max:255|unique:pengguna,surel,' . $karyawan->id,
            'peran' => 'required|string|in:owner,operator',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('foto_profil')) {
            // Delete old file if exists
            if ($karyawan->foto_profil && Storage::exists('public/foto_profil/' . $karyawan->foto_profil)) {
                Storage::delete('public/foto_profil/' . $karyawan->foto_profil);
            }

            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_profil', $filename);
            $data['foto_profil'] = $filename;
        }

        // Only update password if provided
        if ($request->filled('kata_sandi')) {
            $request->validate([
                'kata_sandi' => 'string|min:8',
            ]);
            $data['kata_sandi'] = Hash::make($request->kata_sandi);
        }

        $karyawan->update($data);

        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy(User $karyawan)
    {
        // Delete profile picture if exists
        if ($karyawan->foto_profil && Storage::exists('public/foto_profil/' . $karyawan->foto_profil)) {
            Storage::delete('public/foto_profil/' . $karyawan->foto_profil);
        }

        $karyawan->delete();
        return redirect()->route('admin.karyawan')->with('success', 'Karyawan berhasil dihapus');
    }
}