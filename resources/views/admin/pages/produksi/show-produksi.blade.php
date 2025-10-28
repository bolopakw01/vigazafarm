@extends('admin.layouts.app')

@section('title', 'Pencatatan Produksi - ' . $produksi->batch_produksi_id)

@push('styles')
{{-- Custom CSS for this page only (scoped to prevent sidebar conflicts) --}}
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-show-produksi.css') }}">
{{-- ApexCharts for graphs --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@php
    // Calculate production statistics
    $umurHari = $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->diffInDays(now()) : 0;

    // Mock data for demonstration (in real app, this would come from related tables)
    $totalProduksi = 0; // Total eggs/quail produced
    $produksiHarian = 0; // Daily production average
    $totalPendapatan = $produksi->harga_per_kg ? $totalProduksi * $produksi->harga_per_kg : 0;
    $efisiensiProduksi = 0; // Production efficiency percentage
@endphp

<div class="produksi-detail-wrapper">
<div class="container-fluid py-4">

    {{-- Header (tanpa card background) --}}
    <div class="bolopa-page-header mb-3">
        <div class="bolopa-logo-icon">
            <i class="fa-solid fa-industry"></i>
        </div>
        <div class="bolopa-header-content">
            <h5 class="bolopa-page-title">Pencatatan Produksi</h5>
            <div class="bolopa-page-subtitle">
                Batch: <a href="#">{{ $produksi->batch_produksi_id }}</a> &nbsp;|&nbsp;
                Kandang: <strong>{{ $produksi->kandang->nama_kandang ?? '-' }}</strong> &nbsp;|&nbsp;
                Tipe: <strong>{{ $produksi->tipe_produksi === 'telur' ? 'Telur' : 'Puyuh' }}</strong> &nbsp;|&nbsp;
                Umur: <strong>{{ $umurHari }} hari</strong>
            </div>
        </div>
        <div class="bolopa-header-action">
            <a href="{{ route('admin.produksi') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Menu KAI: empat kartu untuk manajemen produksi --}}
    <div class="bolopa-kai-cards mb-4">
        {{-- Total Produksi --}}
        <div class="bolopa-card-kai bolopa-kai-teal">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ number_format($totalProduksi) }}</div>
                <div class="bolopa-kai-label">Total Produksi {{ $produksi->tipe_produksi === 'telur' ? 'Telur' : 'Puyuh' }}</div>
            </div>
            <i class="fa-solid fa-{{ $produksi->tipe_produksi === 'telur' ? 'egg' : 'dove' }} bolopa-icon-faint"></i>
            <a href="#pencatatanHarian" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#pencatatanHarian">
                Detail <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Produksi Harian --}}
        <div class="bolopa-card-kai bolopa-kai-green">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ number_format($produksiHarian, 1) }}</div>
                <div class="bolopa-kai-label">Rata-rata Harian</div>
            </div>
            <i class="fa-solid fa-chart-line bolopa-icon-faint"></i>
            <a href="#grafikProduksi" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#grafikProduksi">
                Grafik <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Total Pendapatan --}}
        <div class="bolopa-card-kai bolopa-kai-indigo">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value" style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <span style="font-size: 0.35em; color: rgba(255, 255, 255, 0.7); margin-top: 0.2em;">Rp</span>
                    <span>{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                </div>
                <div class="bolopa-kai-label">Total Pendapatan</div>
            </div>
            <i class="fa-solid fa-coins bolopa-icon-faint"></i>
            <a href="#laporanKeuangan" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#laporanKeuangan">
                Rincian <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Efisiensi Produksi --}}
        <div class="bolopa-card-kai bolopa-kai-orange">
            <div class="bolopa-kai-content">
                <div class="bolopa-kai-value">{{ number_format($efisiensiProduksi, 1) }}<small style="font-size:0.45em;">%</small></div>
                <div class="bolopa-kai-label">Efisiensi Produksi</div>
            </div>
            <i class="fa-solid fa-gauge bolopa-icon-faint"></i>
            <a href="#analisisProduksi" class="bolopa-kai-more" data-bs-toggle="tab" data-bs-target="#analisisProduksi">
                Analisis <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    {{-- Notebook Container with Tabs --}}
    <div class="card shadow-sm">
        <div class="card-header p-0">
            <ul class="nav nav-tabs nav-fill" id="produksiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="infoBatch-tab" data-bs-toggle="tab" data-bs-target="#infoBatch" type="button" role="tab">
                        <i class="fa-solid fa-info-circle me-2"></i>Info Batch
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pencatatanHarian-tab" data-bs-toggle="tab" data-bs-target="#pencatatanHarian" type="button" role="tab">
                        <i class="fa-solid fa-calendar-day me-2"></i>Pencatatan Harian
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="grafikProduksi-tab" data-bs-toggle="tab" data-bs-target="#grafikProduksi" type="button" role="tab">
                        <i class="fa-solid fa-chart-bar me-2"></i>Grafik Produksi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="laporanKeuangan-tab" data-bs-toggle="tab" data-bs-target="#laporanKeuangan" type="button" role="tab">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>Laporan Keuangan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="analisisProduksi-tab" data-bs-toggle="tab" data-bs-target="#analisisProduksi" type="button" role="tab">
                        <i class="fa-solid fa-chart-pie me-2"></i>Analisis Produksi
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="produksiTabsContent">

                {{-- Tab Info Batch --}}
                <div class="tab-pane fade show active" id="infoBatch" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-3"><i class="fa-solid fa-info-circle text-primary me-2"></i>Informasi Batch Produksi</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">Detail Batch</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted">Batch ID:</td>
                                                    <td><strong>{{ $produksi->batch_produksi_id }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Tipe Produksi:</td>
                                                    <td>
                                                        @if($produksi->tipe_produksi == 'telur')
                                                            <span class="badge bg-info">Telur</span>
                                                        @else
                                                            <span class="badge bg-warning">Puyuh</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Kandang:</td>
                                                    <td>{{ $produksi->kandang->nama_kandang ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Status:</td>
                                                    <td>
                                                        @if($produksi->status == 'aktif')
                                                            <span class="badge bg-success">Aktif</span>
                                                        @elseif($produksi->status == 'selesai')
                                                            <span class="badge bg-primary">Selesai</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $produksi->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">Data Produksi</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-muted">Tanggal Mulai:</td>
                                                    <td>{{ $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Tanggal Akhir:</td>
                                                    <td>{{ $produksi->tanggal_akhir ? \Carbon\Carbon::parse($produksi->tanggal_akhir)->format('d/m/Y') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Jumlah Indukan:</td>
                                                    <td>{{ number_format($produksi->jumlah_indukan ?? 0) }} ekor</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Umur Mulai:</td>
                                                    <td>{{ $produksi->umur_mulai_produksi ?? '-' }} hari</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($produksi->catatan)
                            <div class="mt-3">
                                <h6 class="text-warning"><i class="fa-solid fa-sticky-note me-2"></i>Catatan</h6>
                                <div class="alert alert-light border">
                                    {{ $produksi->catatan }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <h6 class="mb-3"><i class="fa-solid fa-link text-info me-2"></i>Sumber Input</h6>

                            @if($produksi->pembesaran)
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fa-solid fa-dove me-2"></i>Dari Pembesaran
                                    </h6>
                                    <p class="card-text mb-1">
                                        <strong>Batch:</strong> {{ $produksi->pembesaran->batch_produksi_id }}
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong>Kandang:</strong> {{ $produksi->pembesaran->kandang->nama_kandang ?? '-' }}
                                    </p>
                                    <p class="card-text mb-0">
                                        <strong>Jumlah Transfer:</strong> {{ number_format($produksi->jumlah_indukan ?? 0) }} ekor
                                    </p>
                                </div>
                            </div>
                            @elseif($produksi->penetasan)
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="fa-solid fa-egg me-2"></i>Dari Penetasan
                                    </h6>
                                    <p class="card-text mb-1">
                                        <strong>Batch:</strong> {{ $produksi->penetasan->batch_penetasan_id }}
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong>Kandang:</strong> {{ $produksi->penetasan->kandang->nama_kandang ?? '-' }}
                                    </p>
                                    <p class="card-text mb-0">
                                        <strong>Jumlah Telur:</strong> {{ number_format($produksi->jumlah_telur ?? 0) }} butir
                                    </p>
                                </div>
                            </div>
                            @else
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title text-secondary">
                                        <i class="fa-solid fa-hand-paper me-2"></i>Input Manual
                                    </h6>
                                    <p class="card-text">
                                        Data produksi dimasukkan secara manual tanpa transfer dari batch lain.
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tab Pencatatan Harian --}}
                <div class="tab-pane fade" id="pencatatanHarian" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fa-solid fa-calendar-day text-success me-2"></i>Pencatatan Produksi Harian</h6>
                        <button class="btn btn-success btn-sm" onclick="tambahPencatatan()">
                            <i class="fa-solid fa-plus me-1"></i>Tambah Pencatatan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Produksi</th>
                                    <th>Kualitas</th>
                                    <th>Berat Rata-rata</th>
                                    <th>Harga</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pencatatanTable">
                                {{-- Data pencatatan akan dimuat via AJAX --}}
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data pencatatan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab Grafik Produksi --}}
                <div class="tab-pane fade" id="grafikProduksi" role="tabpanel">
                    <h6 class="mb-3"><i class="fa-solid fa-chart-bar text-primary me-2"></i>Grafik Produksi Harian</h6>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div id="produksiChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Ringkasan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <small class="text-muted">Total Produksi</small>
                                        <div class="h5 mb-0" id="totalProduksi">-</div>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Rata-rata Harian</small>
                                        <div class="h5 mb-0" id="rataRataHarian">-</div>
                                    </div>
                                    <div class="mb-0">
                                        <small class="text-muted">Hari Terproduktif</small>
                                        <div class="h6 mb-0" id="hariTerproduktif">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Laporan Keuangan --}}
                <div class="tab-pane fade" id="laporanKeuangan" role="tabpanel">
                    <h6 class="mb-3"><i class="fa-solid fa-file-invoice-dollar text-warning me-2"></i>Laporan Keuangan Produksi</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-arrow-up me-2"></i>Pendapatan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="h4 text-success mb-2" id="totalPendapatan">Rp 0</div>
                                    <small class="text-muted">Total pendapatan dari produksi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-arrow-down me-2"></i>Biaya Produksi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="h4 text-danger mb-2" id="totalBiaya">Rp 0</div>
                                    <small class="text-muted">Total biaya operasional</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Pendapatan per Periode</h6>
                        </div>
                        <div class="card-body">
                            <div id="keuanganChart" style="height: 250px;"></div>
                        </div>
                    </div>
                </div>

                {{-- Tab Analisis Produksi --}}
                <div class="tab-pane fade" id="analisisProduksi" role="tabpanel">
                    <h6 class="mb-3"><i class="fa-solid fa-chart-pie text-info me-2"></i>Analisis Efisiensi Produksi</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div id="efisiensiChart" style="height: 200px;"></div>
                                    <h6 class="mt-2">Efisiensi Produksi</h6>
                                    <p class="text-muted small" id="efisiensiText">0% dari target</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Faktor Pengaruh</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Kualitas Pakan</span>
                                            <span class="badge bg-success">Baik</span>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Kesehatan Indukan</span>
                                            <span class="badge bg-success">Sehat</span>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Suhu Kandang</span>
                                            <span class="badge bg-warning">Perlu Perhatian</span>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <div class="d-flex justify-content-between">
                                            <span>Ventilasi</span>
                                            <span class="badge bg-success">Optimal</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
{{-- Pass data to JavaScript --}}
<script>
    // Global config for AJAX endpoints
    window.produksiConfig = {
        baseUrl: '{{ url('/') }}',
        produksiId: {{ $produksi->id }},
        csrfToken: '{{ csrf_token() }}',
        tipeProduksi: '{{ $produksi->tipe_produksi }}'
    };
</script>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Produksi detail page loaded');

    // Initialize production chart
    initProduksiChart();

    // Initialize financial chart
    initKeuanganChart();

    // Initialize efficiency chart
    initEfisiensiChart();

    // Load pencatatan data
    loadPencatatanData();
});

// Production Chart
function initProduksiChart() {
    // Load data from backend
    fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan/statistics`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.data;
            const chartData = stats.daily_production || [];
            const categories = stats.dates || [];

            const options = {
                series: [{
                    name: 'Produksi Harian',
                    data: chartData
                }],
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#28a745'],
                xaxis: {
                    categories: categories
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Produksi'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#produksiChart"), options);
            chart.render();
        } else {
            console.error('Failed to load chart data:', data.message);
            // Fallback to empty chart
            initEmptyChart();
        }
    })
    .catch(error => {
        console.error('Error loading chart data:', error);
        initEmptyChart();
    });
}

function initEmptyChart() {
    const options = {
        series: [{
            name: 'Produksi Harian',
            data: []
        }],
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: false
            }
        },
        colors: ['#28a745'],
        xaxis: {
            categories: []
        },
        yaxis: {
            title: {
                text: 'Jumlah Produksi'
            }
        },
        noData: {
            text: 'Belum ada data produksi'
        }
    };

    const chart = new ApexCharts(document.querySelector("#produksiChart"), options);
    chart.render();
}

// Financial Chart
function initKeuanganChart() {
    // Load data from backend
    fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan/statistics`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.data;
            const chartData = stats.weekly_revenue || [];
            const categories = stats.weeks || [];

            const options = {
                series: [{
                    name: 'Pendapatan',
                    data: chartData
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#17a2b8'],
                xaxis: {
                    categories: categories
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#keuanganChart"), options);
            chart.render();
        } else {
            console.error('Failed to load financial chart data:', data.message);
            // Fallback to empty chart
            initEmptyFinancialChart();
        }
    })
    .catch(error => {
        console.error('Error loading financial chart data:', error);
        initEmptyFinancialChart();
    });
}

function initEmptyFinancialChart() {
    const options = {
        series: [{
            name: 'Pendapatan',
            data: []
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: {
                show: false
            }
        },
        colors: ['#17a2b8'],
        xaxis: {
            categories: []
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                }
            }
        },
        noData: {
            text: 'Belum ada data keuangan'
        }
    };

    const chart = new ApexCharts(document.querySelector("#keuanganChart"), options);
    chart.render();
}

// Efficiency Chart
function initEfisiensiChart() {
    // Load data from backend
    fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan/statistics`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.data;
            const efficiency = stats.average_efficiency || 0;

            const options = {
                series: [efficiency],
                chart: {
                    type: 'radialBar',
                    height: 200
                },
                colors: ['#ffc107'],
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '60%'
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                fontSize: '24px',
                                show: true
                            }
                        }
                    }
                },
                labels: ['Efisiensi']
            };

            const chart = new ApexCharts(document.querySelector("#efisiensiChart"), options);
            chart.render();
        } else {
            console.error('Failed to load efficiency data:', data.message);
            // Fallback to zero efficiency
            initEmptyEfficiencyChart();
        }
    })
    .catch(error => {
        console.error('Error loading efficiency data:', error);
        initEmptyEfficiencyChart();
    });
}

function initEmptyEfficiencyChart() {
    const options = {
        series: [0],
        chart: {
            type: 'radialBar',
            height: 200
        },
        colors: ['#ffc107'],
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '60%'
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        fontSize: '24px',
                        show: true
                    }
                }
            }
        },
        labels: ['Efisiensi'],
        noData: {
            text: 'Belum ada data efisiensi'
        }
    };

    const chart = new ApexCharts(document.querySelector("#efisiensiChart"), options);
    chart.render();
}

// Load pencatatan data
function loadPencatatanData() {
    fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderPencatatanTable(data.data);
            updateStatistics(data.data);
        } else {
            console.error('Failed to load pencatatan data:', data.message);
            renderPencatatanTable([]);
            updateStatistics([]);
        }
    })
    .catch(error => {
        console.error('Error loading pencatatan data:', error);
        renderPencatatanTable([]);
        updateStatistics([]);
    });
}

function updateStatistics(data) {
    if (!data || data.length === 0) {
        // Reset all statistics to default values
        document.getElementById('totalProduksi').textContent = '0';
        document.getElementById('rataRataHarian').textContent = '0';
        document.getElementById('hariTerproduktif').textContent = '-';
        document.getElementById('totalPendapatan').textContent = 'Rp 0';
        document.getElementById('totalBiaya').textContent = 'Rp 0';
        document.getElementById('efisiensiText').textContent = '0% dari target';
        return;
    }

    // Calculate statistics
    const totalProduksi = data.reduce((sum, item) => sum + parseInt(item.jumlah || 0), 0);
    const rataRataHarian = data.length > 0 ? (totalProduksi / data.length).toFixed(1) : 0;

    // Find most productive day
    const maxProduksi = Math.max(...data.map(item => parseInt(item.jumlah || 0)));
    const hariTerproduktif = data.find(item => parseInt(item.jumlah || 0) === maxProduksi);
    const hariTerproduktifText = hariTerproduktif ? formatDate(hariTerproduktif.tanggal) : '-';

    // Calculate total revenue
    const totalPendapatan = data.reduce((sum, item) => {
        const jumlah = parseInt(item.jumlah || 0);
        const harga = parseInt(item.harga || 0);
        return sum + (jumlah * harga);
    }, 0);

    // Update DOM elements
    document.getElementById('totalProduksi').textContent = totalProduksi.toLocaleString();
    document.getElementById('rataRataHarian').textContent = rataRataHarian;
    document.getElementById('hariTerproduktif').textContent = hariTerproduktifText;
    document.getElementById('totalPendapatan').textContent = 'Rp ' + totalPendapatan.toLocaleString();
    document.getElementById('totalBiaya').textContent = 'Rp 0'; // For now, no cost calculation

    // Calculate efficiency (mock calculation - you can adjust based on your business logic)
    const efficiency = Math.min((totalProduksi / (data.length * 100)) * 100, 100); // Assuming 100 is target per day
    document.getElementById('efisiensiText').textContent = efficiency.toFixed(1) + '% dari target';
}
    const tbody = document.getElementById('pencatatanTable');

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa-solid fa-inbox me-2"></i>Belum ada data pencatatan
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.map(item => `
        <tr>
            <td>${formatDate(item.tanggal)}</td>
            <td>${item.jumlah} ${window.produksiConfig.tipeProduksi === 'telur' ? 'butir' : 'ekor'}</td>
            <td><span class="badge bg-success">${item.kualitas}</span></td>
            <td>${item.berat_rata ? item.berat_rata + 'g' : '-'}</td>
            <td>Rp ${item.harga ? item.harga.toLocaleString() : '0'}</td>
            <td>${item.catatan || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-1" onclick="editPencatatan(${item.id})">
                    <i class="fa-solid fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="hapusPencatatan(${item.id})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Add new pencatatan
function tambahPencatatan() {
    Swal.fire({
        title: 'Tambah Pencatatan Produksi',
        html: `
            <form id="pencatatanForm">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Produksi</label>
                    <input type="number" class="form-control" id="jumlah" placeholder="Masukkan jumlah ${window.produksiConfig.tipeProduksi === 'telur' ? 'telur' : 'puyuh'}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kualitas</label>
                    <select class="form-control" id="kualitas">
                        <option value="Baik">Baik</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Buruk">Buruk</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Berat Rata-rata (gram)</label>
                    <input type="number" step="0.1" class="form-control" id="berat_rata" placeholder="Contoh: 45.5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga per Unit (Rp)</label>
                    <input type="number" class="form-control" id="harga" placeholder="Contoh: 12000">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="catatan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        width: '500px',
        preConfirm: () => {
            const formData = {
                tanggal: document.getElementById('tanggal').value,
                jumlah: document.getElementById('jumlah').value,
                kualitas: document.getElementById('kualitas').value,
                berat_rata: document.getElementById('berat_rata').value,
                harga: document.getElementById('harga').value,
                catatan: document.getElementById('catatan').value
            };

            // Basic validation
            if (!formData.tanggal || !formData.jumlah) {
                Swal.showValidationMessage('Tanggal dan jumlah produksi harus diisi');
                return false;
            }

            return formData;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to save data
            fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pencatatan berhasil ditambahkan',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload pencatatan data
                    loadPencatatanData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            })
            .catch(error => {
                console.error('Error saving pencatatan:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            });
        }
    });

    // Set default date to today
    document.getElementById('tanggal').valueAsDate = new Date();
}

// Edit pencatatan
function editPencatatan(id) {
    // First, get the current data
    fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pencatatan = data.data.find(item => item.id == id);
            if (pencatatan) {
                showEditForm(pencatatan);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Data tidak ditemukan',
                    text: 'Data pencatatan tidak ditemukan'
                });
            }
        }
    })
    .catch(error => {
        console.error('Error fetching pencatatan data:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat mengambil data'
        });
    });
}

function showEditForm(pencatatan) {
    Swal.fire({
        title: 'Edit Pencatatan Produksi',
        html: `
            <form id="editPencatatanForm">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="edit_tanggal" value="${pencatatan.tanggal}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Produksi</label>
                    <input type="number" class="form-control" id="edit_jumlah" value="${pencatatan.jumlah}" placeholder="Masukkan jumlah ${window.produksiConfig.tipeProduksi === 'telur' ? 'telur' : 'puyuh'}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kualitas</label>
                    <select class="form-control" id="edit_kualitas">
                        <option value="Baik" ${pencatatan.kualitas === 'Baik' ? 'selected' : ''}>Baik</option>
                        <option value="Sedang" ${pencatatan.kualitas === 'Sedang' ? 'selected' : ''}>Sedang</option>
                        <option value="Buruk" ${pencatatan.kualitas === 'Buruk' ? 'selected' : ''}>Buruk</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Berat Rata-rata (gram)</label>
                    <input type="number" step="0.1" class="form-control" id="edit_berat_rata" value="${pencatatan.berat_rata || ''}" placeholder="Contoh: 45.5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga per Unit (Rp)</label>
                    <input type="number" class="form-control" id="edit_harga" value="${pencatatan.harga || ''}" placeholder="Contoh: 12000">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" id="edit_catatan" rows="2" placeholder="Catatan tambahan (opsional)">${pencatatan.catatan || ''}</textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Batal',
        width: '500px',
        preConfirm: () => {
            const formData = {
                tanggal: document.getElementById('edit_tanggal').value,
                jumlah: document.getElementById('edit_jumlah').value,
                kualitas: document.getElementById('edit_kualitas').value,
                berat_rata: document.getElementById('edit_berat_rata').value,
                harga: document.getElementById('edit_harga').value,
                catatan: document.getElementById('edit_catatan').value
            };

            // Basic validation
            if (!formData.tanggal || !formData.jumlah) {
                Swal.showValidationMessage('Tanggal dan jumlah produksi harus diisi');
                return false;
            }

            return formData;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to update data
            fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan/${pencatatan.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pencatatan berhasil diupdate',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload pencatatan data
                    loadPencatatanData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat mengupdate data'
                    });
                }
            })
            .catch(error => {
                console.error('Error updating pencatatan:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat mengupdate data'
                });
            });
        }
    });
}

// Delete pencatatan
function hapusPencatatan(id) {
    Swal.fire({
        title: 'Hapus Pencatatan?',
        text: 'Data yang dihapus tidak dapat dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to delete data
            fetch(`${window.produksiConfig.baseUrl}/admin/produksi/${window.produksiConfig.produksiId}/pencatatan/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.produksiConfig.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data pencatatan berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload pencatatan data
                    loadPencatatanData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat menghapus data'
                    });
                }
            })
            .catch(error => {
                console.error('Error deleting pencatatan:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menghapus data'
                });
            });
        }
    });
}

// Utility function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Tab persistence
document.querySelectorAll('#produksiTabs .nav-link').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        const targetTab = e.target.getAttribute('data-bs-target');
        localStorage.setItem('activeProduksiTab', targetTab);
    });
});

// Restore active tab on page load
const activeTab = localStorage.getItem('activeProduksiTab');
if (activeTab) {
    const tabElement = document.querySelector(`[data-bs-target="${activeTab}"]`);
    if (tabElement) {
        tabElement.click();
    }
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif

</script>
@endpush</content>
<parameter name="filePath">d:\CODE\XAMPP\XAMPP-8.2.12\htdocs\vigazafarm\resources\views\admin\pages\produksi\show-produksi.blade.php
