<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * ==========================================
 * Controller : DashboardController
 * Deskripsi  : Menangani akses dashboard utama dan pengalihan pengguna berdasarkan peran.
 * Dibuat     : 27 November 2025
 * Penulis    : Bolopa Kakungnge Walinono
 * ==========================================
 */
class DashboardController extends Controller
{
    protected function fetchDashboardGoals(): array
    {
        return app(SistemController::class)->getDashboardGoals();
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/mimin');
        }

        if (Auth::user()->peran === 'operator') {
            return redirect()->route('admin.dashboard');
        }

        return app(AdminController::class)->dashboard($request);
    }
}
