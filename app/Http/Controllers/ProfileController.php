<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * ==========================================
 * Controller : ProfileController
 * Deskripsi  : Mengelola pengaturan profil pengguna termasuk foto, data pribadi, dan penghapusan akun.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        /**
         * Menampilkan form edit profil pengguna saat ini.
         */
        return view('admin.pages.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /**
         * Memperbarui data profil pengguna termasuk foto profil dan kata sandi jika disediakan.
         */
        $user = $request->user();
        $photoDirectory = public_path('foto_profil');
        $removePhoto = $request->boolean('remove_profile_picture');
        $usernameChanged = false;

        // Tangani unggah foto profil
        if ($request->hasFile('profile_picture')) {
            if (!File::exists($photoDirectory)) {
                File::makeDirectory($photoDirectory, 0755, true);
            }

            if ($user->foto_profil) {
                $existingPath = $photoDirectory . DIRECTORY_SEPARATOR . $user->foto_profil;
                if (File::exists($existingPath)) {
                    File::delete($existingPath);
                }
            }

            $file = $request->file('profile_picture');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($photoDirectory, $filename);
            $user->foto_profil = $filename;
        } elseif ($removePhoto && $user->foto_profil) {
            $existingPath = $photoDirectory . DIRECTORY_SEPARATOR . $user->foto_profil;
            if (File::exists($existingPath)) {
                File::delete($existingPath);
            }
            $user->foto_profil = null;
        }

        // Perbarui data pengguna
        $user->nama = $request->nama;
        if ($user->peran === 'owner') {
            $usernameChanged = $user->nama_pengguna !== $request->username;
            $user->nama_pengguna = $request->username;
        }
        $user->surel = $request->email;
        $user->nomor_telepon = $request->nomor_telepon;
        $user->alamat = $request->alamat;

        // Tangani pembaruan kata sandi
        if ($request->filled('password')) {
            $user->kata_sandi = Hash::make($request->password);
        }

        $user->save();

        if ($usernameChanged) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::route('login')->with('status', 'Username berhasil diperbarui. Silakan login ulang menggunakan kredensial baru.');
        }

        return Redirect::route('profile.edit')->with('success', 'Profile berhasil diperbarui');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
