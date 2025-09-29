<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('admin.pages.penetasan.index-penetasan');
    }

    public function produksi()
    {
        return view('admin.pages.produksi.index-produksi');
    }
}
