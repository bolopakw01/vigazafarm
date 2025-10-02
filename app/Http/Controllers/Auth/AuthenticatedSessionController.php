<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
