<div class="col-12">
    <div class="row g-3">
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Produksi Telur</h6>
                    <div class="display-6 fw-bold mb-1">{{ number_format($summary['total_telur']) }} butir</div>
                    <small class="text-muted">Akumulasi keseluruhan catatan</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Telur Terjual</h6>
                    <div class="display-6 fw-bold mb-1">{{ number_format($summary['total_penjualan_telur']) }} butir</div>
                    <small class="text-muted">Total distribusi penjualan</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Pendapatan</h6>
                    <div class="display-6 fw-bold mb-1">{{ 'Rp ' . number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
                    <small class="text-muted">Pendapatan kumulatif harian</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Sisa Telur Terakhir</h6>
                    <div class="display-6 fw-bold mb-1">{{ $summary['last_sisa_telur'] !== null ? number_format($summary['last_sisa_telur']) . ' butir' : '-' }}</div>
                    <small class="text-muted">Sisa stok pada catatan terbaru</small>
                </div>
            </div>
        </div>
    </div>
</div>
