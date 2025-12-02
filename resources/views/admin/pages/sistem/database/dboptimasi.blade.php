@extends('admin.layouts.app')

@section('title', 'Optimasi Database')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Sistem', 'link' => route('admin.sistem')],
        ['label' => 'Database'],
        ['label' => 'Optimasi Database'],
    ];
@endphp

@push('styles')
<style>
    @font-face {
        font-family: 'AlanSans';
        src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    .set-database-wrapper {
        padding: 20px;
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px 30px;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .card-body {
        padding: 30px;
    }

    .set-database-title {
        font-size: 1.75rem;
        font-weight: 600;
        font-family: 'AlanSans', sans-serif;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .set-database-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
        margin: 0;
    }

    .alert-box {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .alert-info {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
    }

    .btn-outline-primary {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
    }

    .btn-outline-primary:hover {
        background: #2563eb;
        color: white;
    }

    .header-right {
        display: flex;
        align-items: center;
    }

    .table-optimasi tbody td { vertical-align: middle; }
    .badge-engine { background: #eef2ff; color: #4338ca; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="set-database-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-left">
                <h1 class="set-database-title">
                    <i class="fas fa-bolt"></i>
                    Optimasi Database
                </h1>
                <p class="set-database-subtitle">Monitoring ukuran tabel dan optimasi rutin untuk performa database yang optimal.</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.sistem') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif

            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="margin: 0 0 5px 0; color: #495057;">Statistik & Optimasi Database</h4>
                    <small class="text-muted">Terakhir dijalankan: {{ $last_optimization ? \Carbon\Carbon::parse($last_optimization)->diffForHumans() : 'Belum pernah' }}</small>
                </div>
                <form method="POST" action="{{ route('admin.sistem.database.optimization.run') }}" class="run-optimization-form">
                    @csrf
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-bolt me-2"></i>Jalankan Optimasi
                    </button>
                </form>
            </div>

            @php
                $formatBytes = function ($bytes) {
                    if ($bytes <= 0) {
                        return '0 B';
                    }
                    $units = ['B','KB','MB','GB','TB'];
                    $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
                    return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
                };
            @endphp

            <div class="table-responsive">
                <table class="table table-optimasi align-middle">
                    <thead>
                        <tr>
                            <th>Tabel</th>
                            <th>Engine</th>
                            <th>Baris</th>
                            <th>Ukuran Data</th>
                            <th>Ukuran Index</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $table)
                            <tr>
                                <td class="fw-semibold">{{ $table['name'] }}</td>
                                <td><span class="badge badge-engine">{{ $table['engine'] ?? 'N/A' }}</span></td>
                                <td>{{ number_format($table['rows']) }}</td>
                                <td>{{ $formatBytes($table['data_length']) }}</td>
                                <td>{{ $formatBytes($table['index_length']) }}</td>
                                <td>{{ $formatBytes($table['size']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada statistik tabel yang dapat ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                Optimasi menjalankan perintah <code>OPTIMIZE TABLE</code> dan <code>ANALYZE TABLE</code> pada seluruh tabel aktif.
            </div>
        </div>
    </div>
</div>
@endsection

    @push('scripts')
    <script src="{{ asset('bolopa/plugin/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const optimizeForm = document.querySelector('.run-optimization-form');

        if (optimizeForm) {
            optimizeForm.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Jalankan optimasi?',
                    text: 'Proses ini akan menjalankan OPTIMIZE & ANALYZE TABLE pada seluruh tabel aktif.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, jalankan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        optimizeForm.submit();
                    }
                });
            });
        }
    });
    </script>
    @endpush

