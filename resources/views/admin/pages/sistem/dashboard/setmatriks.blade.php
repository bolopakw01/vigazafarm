@extends('admin.layouts.app')

@section('title', 'Set Matriks Dashboard')

@section('content')
@php
    $snapshot = $snapshot ?? [];
    $targets = $targets ?? [];
@endphp

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-1">Pengaturan Matriks</h5>
                    <p class="text-muted mb-0">Sesuaikan target KPI keuangan yang akan muncul pada dashboard.</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.sistem.matriks.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="vstack gap-4">
                            @forelse($snapshot as $key => $card)
                                @php
                                    $label = $card['label'] ?? ucfirst($key);
                                    $actual = (int) ($card['actual'] ?? 0);
                                    $targetValue = old("targets.$key", (int) ($targets[$key]['target'] ?? $card['target'] ?? 0));
                                @endphp
                                <div class="p-3 rounded border bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <p class="text-uppercase fw-semibold text-muted mb-1">{{ $label }}</p>
                                            <h4 class="mb-0">Rp {{ number_format($actual, 0, ',', '.') }}</h4>
                                            <small class="text-muted">Nilai aktual dari data produksi</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge text-bg-primary">{{ $card['period'] ?? 'Realtime' }}</span>
                                        </div>
                                    </div>

                                    <label for="target-{{ $key }}" class="form-label fw-semibold">Target {{ $label }}</label>
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
                                <p class="text-center text-muted">Belum ada data matriks yang dapat ditampilkan.</p>
                            @endforelse
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">Simpan Target</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
