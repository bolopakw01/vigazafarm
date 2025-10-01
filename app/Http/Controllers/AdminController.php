<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penetasan;

class AdminController extends Controller
{
    public function kandang()
    {
        return view('admin.pages.kandang.index-kandang');
    }

    public function karyawan()
    {
        return view('admin.pages.karyawan.index-karyawan');
    }

    public function pembesaran()
    {
        return view('admin.pages.pembesaran.index-pembesaran');
    }

    public function penetasan()
    {
        $penetasan = Penetasan::paginate(10); // atau all() jika tidak pakai pagination
        return view('admin.pages.penetasan.index-penetasan', compact('penetasan'));
    }

    public function produksi()
    {
        return view('admin.pages.produksi.index-produksi');
    }
}
