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
        $current = Carbon::now();
        $selectedMonth = (int) $request->query('month', $current->month);
        $selectedYear = (int) $request->query('year', $current->year);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = $current->month;
        }

        if ($selectedYear < 2000 || $selectedYear > ($current->year + 5)) {
            $selectedYear = $current->year;
        }

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $periodLabel = $bulan[$selectedMonth] . ' ' . $selectedYear;

        $goals = $this->fetchDashboardGoals();
        $matrixCards = app(SistemController::class)->getMatrixSnapshot($selectedMonth, $selectedYear);

        return view('admin.dashboard-admin', compact('goals', 'matrixCards', 'selectedMonth', 'selectedYear', 'periodLabel'));
    }
}
