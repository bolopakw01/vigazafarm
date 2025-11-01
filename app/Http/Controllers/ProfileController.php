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

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.pages.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $photoDirectory = public_path('foto_profil');
        $removePhoto = $request->boolean('remove_profile_picture');

        // Handle profile picture upload
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

        // Update user data
        $user->nama = $request->nama;
        if (Auth::user()->peran === 'owner') {
            $user->nama_pengguna = $request->username;
        }
        $user->surel = $request->email;
        $user->alamat = $request->alamat;

        // Handle password update
        if ($request->filled('password')) {
            $user->kata_sandi = Hash::make($request->password);
        }

        $user->save();

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
