<!-- Start Page title and tab -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="header-action">
                <h1 class="page-title">Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Vigaza Farm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pembesaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="section-body mt-4">
    <div class="container-fluid">
        <div class="tab-content">
            <div class="tab-pane active" id="pembesaran-all">
                <div class="card">
                    <div class="card-body">
                        <a href="javascript:void();" data-toggle="modal" data-target="#modal-insert">
                            <button type="button" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> Tambah Pembesaran
                            </button>
                        </a>
                        <div class="table-responsive">
                            <br />
                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Periode/Batch</th>
                                        <th>Kandang</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Jumlah DOC</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($data) && !empty($data)): ?>
                                        <?php $no = 1; foreach ($data as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($p->periode ?? $p->batch_penetasan ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($p->nama_kandang ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($p->tanggal_mulai)) ?></td>
                                            <td><?= number_format($p->jumlah_bibit ?? $p->jml_awal ?? 0) ?></td>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                switch($p->status) {
                                                    case 'aktif': $status_class = 'badge-success'; break;
                                                    case 'selesai': $status_class = 'badge-primary'; break;
                                                    case 'dijual': $status_class = 'badge-info'; break;
                                                    default: $status_class = 'badge-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $status_class ?>"><?= ucfirst($p->status) ?></span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        onclick="showDetailPembesaran('<?= $p->id_pembesaran ?>', '<?= htmlspecialchars($p->periode ?? $p->batch_penetasan) ?>', '<?= htmlspecialchars($p->batch_penetasan) ?>', '<?= htmlspecialchars($p->nama_kandang ?? 'N/A') ?>', '<?= date('d/m/Y', strtotime($p->tanggal_mulai)) ?>', '<?= $p->tanggal_selesai ? date('d/m/Y', strtotime($p->tanggal_selesai)) : 'Belum selesai' ?>', '<?= number_format($p->jumlah_bibit) ?>', '<?= number_format($p->jumlah_hidup ?? 0) ?>', '<?= number_format($p->jumlah_mati ?? 0) ?>', '<?= $p->berat_rata ?? 0 ?> kg', '<?= number_format($p->konsumsi_pakan ?? 0) ?> kg', 'Rp <?= number_format($p->total_biaya ?? 0) ?>', '<?= ucfirst($p->status) ?>', '<?= htmlspecialchars($p->catatan ?? '') ?>')" 
                                                        title="Detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('pembesaran/edit/' . $p->id_pembesaran) ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('pembesaran/hapus/' . $p->id_pembesaran) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin hapus data ini?')" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data pembesaran</td>
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

<!-- MODAL INSERT PEMBESARAN -->
<div id="modal-insert" class="modal fade" style="width: 100%;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Data Pembesaran</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('pembesaran/simpan'); ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="periode" placeholder="Periode/Batch" required value="<?= 'PEM' . date('Ymd') . '001' ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="batch_penetasan" placeholder="Batch Penetasan (Asal)">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="id_kandang" required>
                                    <option value="">Pilih Kandang</option>
                                    <option value="1">Kandang A</option>
                                    <option value="2">Kandang B</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="date" class="form-control" name="tanggal_mulai" placeholder="Tanggal Mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" class="form-control" name="jumlah_bibit" placeholder="Jumlah DOC" required>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" name="target_hari" placeholder="Target Panen (Hari)" value="35" required>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="persiapan">Persiapan</option>
                                    <option value="aktif" selected>Aktif</option>
                                </select>
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
function showDetailPembesaran(id, periode, batch, kandang, tanggalMulai, tanggalSelesai, jumlahBibit, jumlahHidup, jumlahMati, beratRata, konsumsiPakan, totalBiaya, status, catatan) {
    Swal.fire({
        title: '<strong>Detail Pembesaran</strong>',
        html: `
            <div class="text-left" style="line-height: 2;">
                <table class="table table-borderless">
                    <tr><td><strong>Periode:</strong></td><td>${periode}</td></tr>
                    <tr><td><strong>Batch Penetasan:</strong></td><td>${batch}</td></tr>
                    <tr><td><strong>Kandang:</strong></td><td>${kandang}</td></tr>
                    <tr><td><strong>Tanggal Mulai:</strong></td><td>${tanggalMulai}</td></tr>
                    <tr><td><strong>Tanggal Selesai:</strong></td><td>${tanggalSelesai}</td></tr>
                    <tr><td><strong>Jumlah Bibit:</strong></td><td>${jumlahBibit}</td></tr>
                    <tr><td><strong>Jumlah Hidup:</strong></td><td>${jumlahHidup}</td></tr>
                    <tr><td><strong>Jumlah Mati:</strong></td><td>${jumlahMati}</td></tr>
                    <tr><td><strong>Berat Rata-rata:</strong></td><td>${beratRata}</td></tr>
                    <tr><td><strong>Konsumsi Pakan:</strong></td><td>${konsumsiPakan}</td></tr>
                    <tr><td><strong>Total Biaya:</strong></td><td>${totalBiaya}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge badge-${status.toLowerCase() === 'selesai' ? 'success' : status.toLowerCase() === 'proses' ? 'warning' : status.toLowerCase() === 'dijual' ? 'info' : 'danger'}">${status}</span></td></tr>
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
