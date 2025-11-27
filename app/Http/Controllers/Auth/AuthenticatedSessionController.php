<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ==========================================
 * Controller : AuthenticatedSessionController
 * Deskripsi  : Menangani tampilan login, autentikasi pengguna, dan proses logout.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        /**
         * Menampilkan tampilan login.
         */
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        /**
         * Memproses permintaan autentikasi (login).
         * Mendukung permintaan AJAX dan non-AJAX.
         */
        if ($request->expectsJson()) {
            try {
                $request->authenticate();

                $request->session()->regenerate();

                // Determine redirect URL based on role
                $redirectUrl = 'http://localhost/vigazafarm/public/dashboard';
                if (Auth::user()->peran === 'operator') {
                    $redirectUrl = 'http://localhost/vigazafarm/public/admin/dashboard';
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang ' . explode(' ', Auth::user()->nama)[0],
                    'redirect' => $redirectUrl
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah.'
                ], 422);
            }
        }

        // Default behavior for non-AJAX requests
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect operator to admin dashboard
        if (Auth::user()->peran === 'operator') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /**
         * Menghancurkan sesi autentikasi (logout) dan mengarahkan ke halaman utama.
         */
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
