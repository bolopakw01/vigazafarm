<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill bg-primary-subtle text-primary">
                        <i class="fa-solid fa-hashtag me-1"></i>{{ $produksi->batch_produksi_id ?? 'Tanpa Kode Batch' }}
                    </span>
                    <span class="badge rounded-pill {{ $statusBadgeClass }}">
                        <i class="fa-solid fa-circle me-1"></i>{{ $status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                <h2 class="mb-1">Produksi Puyuh</h2>
                <p class="text-muted mb-2">
                    <i class="fa-solid fa-warehouse me-2 text-primary"></i>{{ $produksi->kandang->nama_kandang ?? 'Kandang belum ditetapkan' }}
                </p>
                <div class="text-muted small d-flex flex-wrap gap-3">
                    <span><i class="fa-regular fa-calendar me-1"></i>Mulai: {{ $tanggalMulai }}</span>
                    <span><i class="fa-solid fa-stopwatch me-1"></i>Selesai: {{ $tanggalAkhir }}</span>
                    <span><i class="fa-solid fa-crow me-1"></i>Populasi Awal: {{ number_format($produksi->jumlah_indukan ?? 0) }} ekor</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.produksi') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>
                <a href="{{ route('admin.produksi.edit', $produksi->id) }}" class="btn btn-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Data</a>
            </div>
        </div>
    </div>
</div>
