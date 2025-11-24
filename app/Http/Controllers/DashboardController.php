<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected function fetchDashboardGoals(): array
    {
        return app(SistemController::class)->getDashboardGoals();
    }

    public function index()
    {
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
