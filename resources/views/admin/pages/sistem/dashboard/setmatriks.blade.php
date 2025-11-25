@extends('admin.layouts.app')

@section('title', 'Set Matriks')

@php
    $snapshot = $snapshot ?? [];
    $targets = $targets ?? [];
@endphp

@push('styles')
<style>
    .matriks-wrapper { padding: 24px; }
    .matriks-card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(15,23,42,0.08); border:1px solid #e2e8f0; margin-bottom:24px; }
    .matriks-card .card-header { padding:26px 32px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
    .matriks-card .card-body { padding:32px; }
    .matriks-title { font-size:1.8rem; font-weight:600; margin:0; display:flex; align-items:center; gap:12px; color:#0f172a; }
    .matriks-sub { color:#475569; margin:0; font-size:0.95rem; }
    .alert-msg { padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:0.95rem; }
    .alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    .alert-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
    .section-title { font-size:1.1rem; font-weight:600; margin-bottom:16px; color:#1e293b; display:flex; align-items:center; gap:8px; }
    .matriks-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:20px; margin-bottom:32px; }
    .matriks-item { border:1px solid #e2e8f0; border-radius:14px; padding:24px; background:#f8fafc; transition: all 0.2s ease; }
    .matriks-item:hover { background:#fff; box-shadow:0 4px 12px rgba(15,23,42,0.05); }
    .matriks-item-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; }
    .matriks-item-info { flex:1; }
    .matriks-item-label { text-transform:uppercase; font-weight:600; color:#64748b; font-size:0.85rem; margin-bottom:4px; }
    .matriks-item-value { font-size:1.5rem; font-weight:700; color:#0f172a; margin:0; }
    .matriks-item-desc { color:#64748b; font-size:0.85rem; margin:0; }
    .matriks-item-badge { background:#2563eb; color:#fff; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .form-label { font-size:0.9rem; font-weight:600; color:#0f172a; margin-bottom:8px; display:block; }
    .form-control { width:100%; border:2px solid #e2e8f0; border-radius:10px; padding:12px 16px; font-size:0.95rem; transition:border-color .2s; }
    .form-control:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
    .form-control.is-invalid { border-color:#dc2626; }
    .invalid-feedback { display:block; color:#dc2626; font-size:0.85rem; margin-top:4px; }
    .btn { border:none; border-radius:999px; padding:12px 24px; font-weight:600; display:inline-flex; align-items:center; gap:8px; cursor:pointer; transition:opacity .2s ease, transform .2s ease; font-size:0.95rem; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn:hover { opacity:0.9; transform:translateY(-1px); }
    .form-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:24px; padding-top:24px; border-top:1px solid #e2e8f0; }
    .helper-text { font-size:0.85rem; color:#475569; display:flex; align-items:center; gap:6px; }
    .toggle-switch { position:relative; display:inline-block; width:52px; height:28px; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.3s; border-radius:28px; }
    .toggle-slider:before { position:absolute; content:""; height:20px; width:20px; left:4px; bottom:4px; background:#fff; transition:.3s; border-radius:50%; }
    input:checked + .toggle-slider { background:#2563eb; }
    input:checked + .toggle-slider:before { transform:translateX(24px); }
    .toggle-label { font-size:0.9rem; font-weight:600; color:#0f172a; margin-left:12px; }
    .empty-state { text-align:center; color:#64748b; font-style:italic; padding:40px 20px; }
    @media (max-width:768px){ .matriks-card .card-header, .form-footer { flex-direction:column; align-items:flex-start; } .matriks-item-header { flex-direction:column; gap:12px; } }
</style>
@endpush

@section('content')
<div class="matriks-wrapper">
    <div class="matriks-card">
        <div class="card-header">
            <div>
                <h1 class="matriks-title"><i class="fas fa-chart-line"></i> Konfigurasi Matriks KPI</h1>
                <p class="matriks-sub">Sesuaikan target KPI keuangan yang akan muncul pada dashboard utama.</p>
            </div>
            <a href="{{ route('admin.sistem') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert-msg alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert-msg alert-error">
                    <strong>Periksa input Anda:</strong>
                    <ul class="mb-0 mt-2 ps-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.sistem.matriks.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <h2 class="section-title"><i class="fas fa-target"></i> Target KPI Keuangan</h2>
                    <p class="helper-text"><i class="fas fa-info-circle text-primary"></i> Sesuaikan target berdasarkan nilai aktual dari data produksi terkini.</p>

                    @php
                        $currentStatus = filter_var(old('matriks_enabled', $matriks_enabled ?? true), FILTER_VALIDATE_BOOLEAN);
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-4 p-3 rounded border bg-light flex-wrap gap-3">
                        <div>
                            <h6 class="mb-1 fw-semibold">Status Matriks Dashboard</h6>
                            <small class="text-muted">Aktifkan atau nonaktifkan tampilan matriks pada dashboard utama</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <input type="hidden" name="matriks_enabled" value="0">
                            <label class="toggle-switch mb-0" aria-label="Toggle matriks">
                                <input type="checkbox" name="matriks_enabled" value="1" {{ $currentStatus ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">{{ $currentStatus ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>
                </div>

                <div class="matriks-grid">
                    @forelse($snapshot as $key => $card)
                        @php
                            $label = $card['label'] ?? ucfirst($key);
                            $actual = (int) ($card['actual'] ?? 0);
                            $targetValue = old("targets.$key", (int) ($targets[$key]['target'] ?? $card['target'] ?? 0));
                        @endphp
                        <div class="matriks-item">
                            <div class="matriks-item-header">
                                <div class="matriks-item-info">
                                    <div class="matriks-item-label">{{ $label }}</div>
                                    <div class="matriks-item-value">Rp {{ number_format($actual, 0, ',', '.') }}</div>
                                    <div class="matriks-item-desc">Nilai aktual dari data produksi</div>
                                </div>
                                <div class="matriks-item-badge">{{ $card['period'] ?? 'Realtime' }}</div>
                            </div>

                            <label for="target-{{ $key }}" class="form-label">Target {{ $label }}</label>
                            <input
                                id="target-{{ $key }}"
                                type="number"
                                name="targets[{{ $key }}]"
                                class="form-control @error("targets.$key") is-invalid @enderror"
                                min="0"
                                step="1000"
                                value="{{ $targetValue }}"
                                placeholder="Masukkan target Rp"
                                required
                            >
                            @error("targets.$key")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p>Belum ada data matriks yang dapat ditampilkan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="form-footer">
                    <div class="helper-text"><i class="fas fa-lightbulb text-warning"></i> Target akan mempengaruhi indikator pada dashboard utama.</div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Target</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('input[name="matriks_enabled"][type="checkbox"]');
        const statusLabel = document.querySelector('.toggle-label');
        if (!toggle || !statusLabel) {
            return;
        }
        toggle.addEventListener('change', function () {
            statusLabel.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
        });
    });
</script>
@endpush
