@extends('admin.layouts.app')

@section('title', 'Manajemen Sistem')

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .sistem-container {
        padding: 20px;
    }

    .sistem-section {
        margin-bottom: 40px;
    }

    .sistem-section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'AlanSans', sans-serif;
    }

    .sistem-section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .dashboard-settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        justify-content: center;
    }

    .goals-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 280px; /* Fixed height for consistency */
        width: 100%;
        max-width: 250px;
        margin: 0;
    }

    .goals-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin: 15px 0 10px 0;
        font-family: 'AlanSans', sans-serif;
    }

    .goals-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        margin: 0 auto;
    }

    .goals-card-icon.production { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .goals-card-icon.revenue { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .goals-card-icon.efficiency { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .goals-card-icon.quality { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

    .goals-description {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.4;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60px; /* Ensure consistent description height */
        font-family: 'AlanSans', sans-serif;
    }

    .goals-card-actions {
        width: 100%;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .goals-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        font-family: 'AlanSans', sans-serif;
    }

    .goals-btn-primary {
        background: #2563eb;
        color: white;
    }

    .goals-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-settings-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .goals-card {
            max-width: 100%;
            height: 260px; /* Slightly smaller height for mobile */
            padding: 20px;
        }

        .sistem-container {
            padding: 15px;
        }

        .sistem-section-title {
            font-size: 1.3rem;
        }

        .goals-card-icon {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }

        .goals-card-title {
            font-size: 1rem;
        }

        .goals-description {
            font-size: 0.8rem;
            min-height: 50px; /* Smaller min-height for mobile */
        }

        .goals-btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')
<div class="sistem-container">
    <!-- Setting Dashboard Section -->
    <div class="sistem-section">
        <h2 class="sistem-section-title">
            <i class="fas fa-chart-line"></i>
            Pengaturan Dashboard
        </h2>

        <div class="dashboard-settings-grid">
            <!-- Set Goals Card -->
            <div class="goals-card">
                <div class="goals-card-icon production">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="goals-card-title">Goals</h3>
                <div class="goals-description">
                    Atur target dan goals untuk dashboard utama. Tetapkan target penjualan, produksi, dan pesanan bulanan.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.dashboard') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-cog"></i>
                        Set Goals
                    </a>
                </div>
            </div>
            <!-- Feed/Vitamin Settings Card -->
            <div class="goals-card">
                <div class="goals-card-icon efficiency">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="goals-card-title">Pakan & Vitamin</h3>
                <div class="goals-description">
                    Buat daftar pakan dan vitamin lengkap dengan harga serta satuan untuk dipakai di form produksi.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.pakanvitamin') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-sliders-h"></i>
                        Set Pakan/Vitamin
                    </a>
                </div>
            </div>
            <!-- Matrix Settings Card -->
            <div class="goals-card">
                <div class="goals-card-icon revenue">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="goals-card-title">Set Matriks</h3>
                <div class="goals-description">
                    Tetapkan target pendapatan, pengeluaran, dan laba agar KPI dashboard keuangan terupdate otomatis.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.matriks') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-sliders-h"></i>
                        Set Matriks
                    </a>
                </div>
            </div>

            <!-- Performance Chart Settings Card -->
            <div class="goals-card">
                <div class="goals-card-icon quality">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="goals-card-title">Grafik Performance</h3>
                <div class="goals-description">
                    Atur label radar chart, seri, dan nilai untuk menampilkan performa operasional sesuai kebutuhan.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.performance') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-chart-radar"></i>
                        Set Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Settings Section -->
    <div class="sistem-section">
        <h2 class="sistem-section-title">
            <i class="fas fa-database"></i>
            Pengaturan Database
        </h2>

        <div class="dashboard-settings-grid">
            <!-- Backup Database Card -->
            <div class="goals-card">
                <div class="goals-card-icon production">
                    <i class="fas fa-download"></i>
                </div>
                <h3 class="goals-card-title">Backup Database</h3>
                <div class="goals-description">
                    Buat cadangan data database secara berkala untuk keamanan dan pemulihan data.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.database.backup') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-cloud-download-alt"></i>
                        Backup Sekarang
                    </a>
                </div>
            </div>

            <!-- Restore Database Card -->
            <div class="goals-card">
                <div class="goals-card-icon efficiency">
                    <i class="fas fa-upload"></i>
                </div>
                <h3 class="goals-card-title">Restore Database</h3>
                <div class="goals-description">
                    Pulihkan data dari file backup yang telah dibuat sebelumnya.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.database.restore') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Restore Data
                    </a>
                </div>
            </div>

            <!-- Database Connection Card -->
            <div class="goals-card">
                <div class="goals-card-icon revenue">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="goals-card-title">Informasi Database</h3>
                <div class="goals-description">
                    Lihat informasi lengkap tentang konfigurasi dan status database yang sedang digunakan.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.database.info') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-info"></i>
                        Lihat Info
                    </a>
                </div>
            </div>

            <!-- Database Optimization Card -->
            <div class="goals-card">
                <div class="goals-card-icon quality">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h3 class="goals-card-title">Optimasi Database</h3>
                <div class="goals-description">
                    Lakukan optimasi performa database dengan indexing dan maintenance rutin.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.database.optimization') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-wrench"></i>
                        Optimalkan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- IoT Settings Section -->
    <div class="sistem-section">
        <h2 class="sistem-section-title">
            <i class="fas fa-satellite-dish"></i>
            Pengaturan IoT
        </h2>

        <div class="dashboard-settings-grid">
            <div class="goals-card">
                <div class="goals-card-icon efficiency">
                    <i class="fas fa-wifi"></i>
                </div>
                <h3 class="goals-card-title">Suhu & Kelembapan</h3>
                <div class="goals-description">
                    Kelola integrasi IoT untuk pembacaan suhu dan kelembapan kandang secara real-time.
                </div>
                <div class="goals-card-actions">
                    <a href="{{ route('admin.sistem.iot') }}" class="goals-btn goals-btn-primary">
                        <i class="fas fa-plug"></i>
                        Pengaturan IoT
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Remove old JavaScript functions that are no longer needed
</script>
@endpush

