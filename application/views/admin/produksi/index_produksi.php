<!-- Start Page title and tab -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="header-action">
                <h1 class="page-title">Produksi</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Vigaza Farm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Produksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="section-body mt-4">
    <div class="container-fluid">
        <div class="tab-content">
            <div class="tab-pane active" id="produksi-all">
                <div class="card">
                    <div class="card-body">
                        <a href="javascript:void();" data-toggle="modal" data-target="#modal-insert">
                            <button type="button" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> Tambah Produksi
                            </button>
                        </a>
                        <div class="table-responsive">
                            <br />
                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Kandang</th>
                                        <th>Jumlah Telur</th>
                                        <th>Berat (Kg)</th>
                                        <th>Kualitas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($produksi) && !empty($produksi)): ?>
                                        <?php $no = 1; foreach ($produksi as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($p->tanggal)) ?></td>
                                            <td><?= htmlspecialchars($p->nama_kandang ?? 'N/A') ?></td>
                                            <td><?= number_format($p->jml_telur ?? $p->jumlah ?? 0) ?></td>
                                            <td><?= number_format($p->berat_telur ?? $p->berat ?? 0, 2) ?></td>
                                            <td>
                                                <?php 
                                                $kualitas_class = '';
                                                $kualitas = $p->grade ?? $p->kualitas ?? 'A';
                                                switch($kualitas) {
                                                    case 'A': $kualitas_class = 'badge-success'; break;
                                                    case 'B': $kualitas_class = 'badge-warning'; break;
                                                    case 'C': $kualitas_class = 'badge-danger'; break;
                                                    default: $kualitas_class = 'badge-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $kualitas_class ?>">Kualitas <?= $kualitas ?></span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="showDetailProduksi('<?= $p->id_produksi ?>', '<?= date('d/m/Y', strtotime($p->tanggal)) ?>', '<?= htmlspecialchars($p->nama_kandang ?? 'N/A') ?>', '<?= number_format($p->jml_telur ?? $p->jumlah ?? 0) ?>', '<?= number_format($p->jumlah_ayam_awal ?? 0) ?>', '<?= number_format($p->jumlah_ayam_saat_ini ?? 0) ?>', '<?= number_format($p->total_kematian ?? 0) ?>', '<?= ($p->berat_telur ?? $p->berat ?? 0) ?> kg', '<?= number_format($p->harga_satuan ?? 0) ?>', '<?= number_format($p->total_nilai ?? 0) ?>', '<?= $p->kualitas ?? 'N/A' ?>', '<?= htmlspecialchars($p->catatan ?? '') ?>')" 
                                                        title="Detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('produksi/edit/' . $p->id_produksi) ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('produksi/delete/' . $p->id_produksi) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data produksi</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INSERT PRODUKSI -->
<div id="modal-insert" class="modal fade" style="width: 100%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Data Produksi</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('produksi/tambah'); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="date" class="form-control" name="tanggal" placeholder="Tanggal" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="id_kandang" required>
                                    <option value="">Pilih Kandang</option>
                                    <?php if (isset($kandang) && !empty($kandang)): ?>
                                        <?php foreach ($kandang as $k): ?>
                                            <option value="<?= $k->id_kandang ?>"><?= htmlspecialchars($k->nama_kandang) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" name="jumlah_telur" placeholder="Jumlah Telur" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" class="form-control" name="berat_total" placeholder="Berat Total (Kg)" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="kualitas" required>
                                    <option value="">Pilih Kualitas</option>
                                    <option value="A">Kualitas A</option>
                                    <option value="B">Kualitas B</option>
                                    <option value="C">Kualitas C</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" name="harga_per_kg" placeholder="Harga per Kg" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea class="form-control" name="catatan" placeholder="Catatan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDetailProduksi(id, tanggal, kandang, jumlahProduksi, jumlahAyamAwal, jumlahAyamSaatIni, totalKematian, beratProduksi, hargaSatuan, totalNilai, kualitas, catatan) {
    Swal.fire({
        title: '<strong>Detail Produksi</strong>',
        html: `
            <div class="text-left" style="line-height: 2;">
                <table class="table table-borderless">
                    <tr><td><strong>Tanggal:</strong></td><td>${tanggal}</td></tr>
                    <tr><td><strong>Kandang:</strong></td><td>${kandang}</td></tr>
                    <tr><td><strong>Jumlah Produksi:</strong></td><td>${jumlahProduksi}</td></tr>
                    <tr><td><strong>Ayam Awal:</strong></td><td>${jumlahAyamAwal}</td></tr>
                    <tr><td><strong>Ayam Saat Ini:</strong></td><td>${jumlahAyamSaatIni}</td></tr>
                    <tr><td><strong>Total Kematian:</strong></td><td>${totalKematian}</td></tr>
                    <tr><td><strong>Berat Produksi:</strong></td><td>${beratProduksi}</td></tr>
                    <tr><td><strong>Harga Satuan:</strong></td><td>Rp ${parseInt(hargaSatuan).toLocaleString()}</td></tr>
                    <tr><td><strong>Total Nilai:</strong></td><td>Rp ${parseInt(totalNilai).toLocaleString()}</td></tr>
                    <tr><td><strong>Kualitas:</strong></td><td><span class="badge badge-${kualitas === 'A' ? 'success' : kualitas === 'B' ? 'warning' : 'danger'}">Kualitas ${kualitas}</span></td></tr>
                    <tr><td><strong>Catatan:</strong></td><td>${catatan || 'Tidak ada catatan'}</td></tr>
                </table>
            </div>
        `,
        width: 600,
        showConfirmButton: true,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#3085d6'
    });
}
</script>
