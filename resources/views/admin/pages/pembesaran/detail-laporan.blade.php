@extends('admin.layouts.app')

@section('title', 'Detail Laporan Harian')

@php
    $breadcrumbs = [
        ['label' => 'Backoffice', 'link' => route('admin.dashboard')],
        ['label' => 'Pembesaran', 'link' => route('admin.pembesaran')],
        ['label' => 'Detail Batch', 'link' => route('admin.pembesaran.show', $pembesaran->id), 'badge' => $pembesaran->batch_produksi_id],
        ['label' => 'Catatan Harian'],
    ];
@endphp

@push('styles')
<style>
/* Specific styles for detail laporan page - sesuai note.html */
.laporan-detail-wrapper {
    background-color: #E4E9F7 !important;
    min-height: calc(100vh - 72px) !important;
    padding: 0 !important;
}

.laporan-detail-container {
    max-width: 100% !important;
    padding: 1.5rem 1rem !important;
    margin: 0 auto !important;
}

.laporan-detail-card-main {
    border: 1px solid #e9ecef !important;
    border-radius: 0.6rem !important;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    overflow: hidden;
    max-width: 900px !important;
    margin: 0 auto !important;
}

.laporan-detail-card-header {
    background-color: #fff !important;
    border-bottom: 1px solid #e9ecef !important;
    font-weight: 600 !important;
    padding: 1rem 1.25rem !important;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.laporan-detail-card-body {
    padding: 1.25rem !important;
    background: #fff !important;
}

.laporan-detail-info-row {
    margin-bottom: 1rem !important;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (min-width: 768px) {
    .laporan-detail-info-row {
        flex-direction: row;
        justify-content: space-between;
    }
}

.laporan-detail-info-item small {
    color: #6c757d !important;
    font-size: 0.875rem !important;
    display: block !important;
    margin-bottom: 0.25rem !important;
}

.laporan-detail-info-item .fw-semibold {
    font-weight: 600 !important;
    color: #212529 !important;
    font-size: 1rem !important;
}

.laporan-detail-divider {
    border: 0 !important;
    border-top: 1px solid #dee2e6 !important;
    margin: 1rem 0 !important;
}

.laporan-detail-stats {
    margin-bottom: 1rem !important;
    display: flex !important;
    justify-content: space-around !important;
    align-items: center !important;
    gap: 1rem !important;
}

.laporan-detail-stats-item {
    padding: 0.5rem !important;
    flex: 1 !important;
    text-align: center !important;
}

.laporan-detail-stats-item .text-muted {
    font-size: 0.875rem !important;
    color: #6c757d !important;
    display: block !important;
    margin-bottom: 0.25rem !important;
}

.laporan-detail-stats-item .fw-semibold {
    font-weight: 600 !important;
    color: #212529 !important;
    font-size: 1.125rem !important;
}

.laporan-detail-section-title {
    font-weight: 600 !important;
    font-size: 1rem !important;
    color: #212529 !important;
    margin-bottom: 0.75rem !important;
}

.laporan-detail-note-card {
    background: #fff !important;
    border: 1px solid #e9ecef !important;
    border-radius: 0.6rem !important;
    box-shadow: 0 8px 28px rgba(15,23,42,0.06) !important;
    overflow: hidden;
}

.laporan-detail-note-body {
    padding: 1rem !important;
}

.laporan-detail-catatan-box {
    white-space: pre-line !important;
    color: #222 !important;
    font-size: 0.9375rem !important;
    line-height: 1.6 !important;
    font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
}

.laporan-detail-timestamp {
    margin-top: 1rem !important;
    font-size: 0.875rem !important;
    color: #6c757d !important;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

@media (min-width: 768px) {
    .laporan-detail-timestamp {
        flex-direction: row;
        justify-content: space-between;
    }
}

.laporan-detail-card-footer {
    background-color: #fff !important;
    border-top: 1px solid #e9ecef !important;
    padding: 1rem 1.25rem !important;
    text-align: right !important;
}

/* Button styles */
.laporan-detail-btn {
    font-size: 0.875rem !important;
    padding: 0.375rem 0.75rem !important;
    border-radius: 0.375rem !important;
    font-weight: 500 !important;
    border: 1px solid transparent !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.laporan-detail-btn-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #fff !important;
}

.laporan-detail-btn-primary:hover {
    background-color: #0b5ed7 !important;
    border-color: #0a58ca !important;
}

.laporan-detail-btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #fff !important;
}

.laporan-detail-btn-danger:hover {
    background-color: #bb2d3b !important;
    border-color: #b02a37 !important;
}

.laporan-detail-btn-secondary {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}

.laporan-detail-btn-secondary:hover {
    background-color: #5c636a !important;
    border-color: #565e64 !important;
}
</style>
@endpush

@section('content')
<div class="laporan-detail-wrapper">
    <div class="laporan-detail-container">
        <div class="laporan-detail-card-main">
            <div class="laporan-detail-card-header">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-clipboard-list me-2"></i>
                    <strong>Detail Laporan Harian</strong>
                </div>
                <a href="{{ route('admin.pembesaran.show', $pembesaran->id) }}" class="laporan-detail-btn laporan-detail-btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>Kembali
                </a>
            </div>

            <div class="laporan-detail-card-body">
                <div class="laporan-detail-info-row">
                    <div class="laporan-detail-info-item">
                        <small><i class="fa-solid fa-calendar-days me-1"></i>Tanggal Laporan</small>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
                    </div>
                    <div class="laporan-detail-info-item text-md-end">
                        <small><i class="fa-solid fa-user me-1"></i>Dibuat Oleh</small>
                        <div class="fw-semibold">
                            {{ $laporan->pengguna->nama_pengguna ?? 'N/A' }}
                            @if(auth()->id() === $laporan->pengguna_id)
                                <span class="badge bg-success ms-1" style="font-size: 0.7rem;">Anda</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="laporan-detail-divider" />

                <div class="laporan-detail-stats">
                    <div class="laporan-detail-stats-item">
                        <div class="text-muted small">Populasi</div>
                        <div class="fw-semibold">{{ number_format($laporan->jumlah_burung, 2) }}</div>
                    </div>
                    <div class="laporan-detail-stats-item">
                        <div class="text-muted small">Pakan (kg)</div>
                        <div class="fw-semibold">{{ $laporan->konsumsi_pakan_kg ?? 0 }}</div>
                    </div>
                    <div class="laporan-detail-stats-item">
                        <div class="text-muted small">Kematian</div>
                        <div class="fw-semibold">{{ $laporan->jumlah_kematian ?? 0 }}</div>
                    </div>
                    <div class="laporan-detail-stats-item">
                        <div class="text-muted small">Mortalitas</div>
                        <div class="fw-semibold">{{ number_format($laporan->mortalitas_kumulatif, 2) }}%</div>
                    </div>
                </div>

                <hr class="laporan-detail-divider" />

                <div class="mb-3">
                    <div class="laporan-detail-section-title">Catatan Lengkap</div>
                    <div class="laporan-detail-note-card">
                        <div class="laporan-detail-note-body">
                            <div class="laporan-detail-catatan-box">{{ $laporan->catatan_kejadian ?? 'Tidak ada catatan' }}</div>
                        </div>
                    </div>
                </div>

                <div class="laporan-detail-timestamp">
                    <div>Dibuat {{ \Carbon\Carbon::parse($laporan->dibuat_pada)->locale('id')->isoFormat('DD MMMM YYYY') }} pukul {{ \Carbon\Carbon::parse($laporan->dibuat_pada)->format('H.i') }}</div>
                    <div class="text-md-end">Diperbarui {{ \Carbon\Carbon::parse($laporan->diperbarui_pada)->locale('id')->isoFormat('DD MMMM YYYY') }} pukul {{ \Carbon\Carbon::parse($laporan->diperbarui_pada)->format('H.i') }}</div>
                </div>
            </div>

            @php
                $canEdit = auth()->id() === $laporan->pengguna_id || auth()->user()->peran === 'owner';
            @endphp

            @if($canEdit)
            <div class="laporan-detail-card-footer">
                <button class="laporan-detail-btn laporan-detail-btn-primary me-2" onclick="openEditModal()">
                    <i class="fa-solid fa-pen"></i>Edit
                </button>
                <button class="laporan-detail-btn laporan-detail-btn-danger" onclick="deleteLaporan()">
                    <i class="fa-solid fa-trash"></i>Hapus
                </button>
            </div>
            @else
            <div class="laporan-detail-card-footer">
                <div class="text-muted small">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Hanya pembuat laporan yang dapat mengedit atau menghapus catatan ini.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const pembesaranId = {{ $pembesaran->id }};
const laporanId = {{ $laporan->id }};
const baseUrl = '{{ url('/') }}';
const csrfToken = '{{ csrf_token() }}';
const currentCatatan = `{{ str_replace(['`', "\n", "\r"], ['\\`', '\\n', '\\r'], $laporan->catatan_kejadian ?? '') }}`;

function openEditModal() {
    Swal.fire({
        title: '<i class="fa-solid fa-pen me-2"></i>Edit Catatan Laporan',
        html: `
            <div style="text-align: left; overflow-wrap: break-word; word-break: break-word; min-width: 0;">
                <label for="swal-catatan" class="form-label fw-semibold mb-2" style="display: block;">Catatan Kejadian</label>
                <textarea
                    id="swal-catatan"
                    class="form-control"
                    rows="8"
                    placeholder="Masukkan catatan kejadian..."
                    style="min-width: 0; width: 100%; box-sizing: border-box; resize: vertical;">${currentCatatan.replace(/\\n/g, '\n').replace(/\\r/g, '\r')}</textarea>
                <small class="text-muted" style="display: block; margin-top: 0.5rem;">Tekan Enter untuk baris baru. Teks akan dibungkus otomatis.</small>
            </div>
        `,
        width: '640px',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: '<i class="fa-solid fa-save me-1"></i>Simpan Perubahan',
        cancelButtonText: '<i class="fa-solid fa-times me-1"></i>Batal',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const catatan = document.getElementById('swal-catatan').value;

            return fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/${laporanId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    catatan_kejadian: catatan
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (!result.success) {
                    throw new Error(result.message || 'Gagal memperbarui catatan');
                }
                return { catatan, result };
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const catatan = result.value.catatan;

            // Update catatan di halaman
            document.querySelector('.laporan-detail-catatan-box').textContent = catatan;

            // Update timestamp diperbarui
            const now = new Date();
            const options = { day: '2-digit', month: 'long', year: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            const timeStr = now.getHours().toString().padStart(2, '0') + '.' + now.getMinutes().toString().padStart(2, '0');

            const timestampDiv = document.querySelector('.laporan-detail-timestamp div:last-child');
            if (timestampDiv) {
                timestampDiv.textContent = `Diperbarui ${dateStr} pukul ${timeStr}`;
            }

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Catatan berhasil diperbarui',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function deleteLaporan() {
    Swal.fire({
        title: 'Hapus Laporan?',
        html: 'Yakin ingin menghapus laporan harian ini?<br><br><strong>Data yang dihapus tidak dapat dikembalikan.</strong>',
        icon: 'warning',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i>Ya, Hapus',
        cancelButtonText: '<i class="fa-solid fa-times me-1"></i>Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/${laporanId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(result => {
                if (!result.success) {
                    throw new Error(result.message || 'Gagal menghapus laporan');
                }
                return result;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Laporan berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Redirect ke halaman pembesaran tab Recording Harian
                window.location.href = `${baseUrl}/admin/pembesaran/${pembesaranId}#recordHarian`;
            });
        }
    });
}
</script>
@endpush