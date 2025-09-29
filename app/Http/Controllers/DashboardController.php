<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/mimin');
        }

        if (Auth::user()->peran === 'owner') {
            return view('admin.dashboard-admin');
        } else {
            // Untuk operator, mungkin redirect ke halaman lain atau tampilkan pesan
            return view('dashboard'); // Atau redirect ke route tertentu
        }
    }
}
