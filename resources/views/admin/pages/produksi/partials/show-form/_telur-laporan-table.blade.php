<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fa-solid fa-list-check me-2"></i>Rekap Catatan Harian</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Populasi</th>
                            <th>Produksi Telur</th>
                            <th>Telur Terjual</th>
                            <th>Kematian</th>
                            <th>Konsumsi Pakan (kg)</th>
                            <th>Sisa Pakan (kg)</th>
                            <th>Sisa Telur</th>
                            <th>Sisa Tray (bal)</th>
                            <th>Sisa Tray (lembar)</th>
                            <th>Sisa Vitamin (L)</th>
                            <th>Pendapatan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanHarian as $laporan)
                            <tr>
                                <td>{{ $laporan->tanggal?->translatedFormat('d F Y') ?? '-' }}</td>
                                <td>{{ number_format($laporan->jumlah_burung ?? 0) }}</td>
                                <td>{{ $laporan->produksi_telur !== null ? number_format($laporan->produksi_telur) : '-' }}</td>
                                <td>{{ $laporan->penjualan_telur_butir !== null ? number_format($laporan->penjualan_telur_butir) : '-' }}</td>
                                <td>{{ number_format($laporan->jumlah_kematian ?? 0) }}</td>
                                <td>{{ $laporan->konsumsi_pakan_kg !== null ? number_format($laporan->konsumsi_pakan_kg, 2, ',', '.') : '-' }}</td>
                                <td>{{ $laporan->sisa_pakan_kg !== null ? number_format($laporan->sisa_pakan_kg, 2, ',', '.') : '-' }}</td>
                                <td>{{ $laporan->sisa_telur !== null ? number_format($laporan->sisa_telur) : '-' }}</td>
                                <td>{{ $laporan->sisa_tray_bal !== null ? number_format($laporan->sisa_tray_bal, 2, ',', '.') : '-' }}</td>
                                <td>{{ $laporan->sisa_tray_lembar !== null ? number_format($laporan->sisa_tray_lembar) : '-' }}</td>
                                <td>{{ $laporan->sisa_vitamin_liter !== null ? number_format($laporan->sisa_vitamin_liter, 2, ',', '.') : '-' }}</td>
                                <td>{{ $laporan->pendapatan_harian !== null ? 'Rp ' . number_format($laporan->pendapatan_harian, 0, ',', '.') : '-' }}</td>
                                <td style="max-width: 220px; white-space: pre-wrap;">{{ $laporan->catatan_kejadian ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-3">Belum ada catatan harian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
