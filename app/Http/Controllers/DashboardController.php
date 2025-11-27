<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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

    public function index()
    {
        /**
         * Menampilkan halaman dashboard utama.
         * Mengarahkan user berdasarkan peran (operator -> admin dashboard).
         */
        if (!Auth::check()) {
            return redirect('/mimin');
        }

        // Redirect operator ke admin dashboard
        if (Auth::user()->peran === 'operator') {
            return redirect()->route('admin.dashboard');
        }

        // Owner ke dashboard utama
        $goals = $this->fetchDashboardGoals();
        $matrixCards = app(SistemController::class)->getMatrixSnapshot();

        return view('admin.dashboard-admin', compact('goals', 'matrixCards'));
    }
}
