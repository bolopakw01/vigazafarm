<div class="col-12">
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Penjualan Puyuh</h6>
                    <div class="display-5 fw-bold mb-1">{{ number_format($summary['total_penjualan_puyuh']) }} ekor</div>
                    <small class="text-muted">Akumulasi dari catatan harian</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Kematian</h6>
                    <div class="display-5 fw-bold mb-1">{{ number_format($summary['total_kematian']) }} ekor</div>
                    <small class="text-muted">Sepanjang periode produksi</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Sisa Pakan Terakhir</h6>
                    <div class="display-5 fw-bold mb-1">{{ $summary['last_sisa_pakan'] !== null ? number_format($summary['last_sisa_pakan'], 2, ',', '.') : '0,00' }} kg</div>
                    <small class="text-muted">Berdasarkan catatan terbaru</small>
                </div>
            </div>
        </div>
    </div>
</div>
