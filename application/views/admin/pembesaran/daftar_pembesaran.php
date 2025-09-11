<!-- Load SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page Title -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="header-action">
                <h1 class="page-title">Daftar Semua Pembesaran</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Vigaza Farm</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pembesaran') ?>">Pembesaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daftar</li>
                </ol>
            </div>
            <div class="header-action">
                <a href="<?= base_url('pembesaran/tambah') ?>" class="btn btn-vigaza">
                    <i class="fa fa-plus"></i> Tambah Periode
                </a>
                <a href="<?= base_url('pembesaran') ?>" class="btn btn-outline-vigaza">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="section-body mt-4">
    <div class="container-fluid">
        <!-- SweetAlert Notifications -->
        <?php if ($this->session->flashdata('success')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '<?= $this->session->flashdata('success') ?>',
                        timer: 3000,
                        timerProgressBar: true,
                        confirmButtonColor: '#0eaab4'
                    });
                });
            </script>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '<?= $this->session->flashdata('error') ?>',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0eaab4'
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Filter Card -->
        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-filter text-vigaza"></i> 
                            Filter & Pencarian
                        </h3>
                        <div class="card-options">
                            <button class="btn btn-sm btn-vigaza" id="toggle-filter">
                                <i class="fa fa-chevron-down"></i> Filter
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="filter-panel" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" id="filter-status">
                                        <option value="">Semua Status</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="panen">Panen</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kandang</label>
                                    <select class="form-control" id="filter-kandang">
                                        <option value="">Semua Kandang</option>
                                        <?php if (!empty($kandang)): ?>
                                            <?php foreach ($kandang as $k): ?>
                                            <option value="<?= $k->nama_kandang ?>"><?= $k->nama_kandang ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="filter-tanggal">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button class="btn btn-vigaza btn-block" id="apply-filter">
                                            <i class="fa fa-search"></i> Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-table text-vigaza"></i> 
                            Data Pembesaran
                        </h3>
                        <div class="card-options">
                            <span class="badge badge-vigaza" id="total-records">
                                Total: <?= !empty($data) ? count($data) : 0 ?> record
                            </span>
                            <div class="btn-group ml-2">
                                <button class="btn btn-sm btn-outline-vigaza" onclick="exportData('excel')">
                                    <i class="fa fa-file-excel-o"></i> Excel
                                </button>
                                <button class="btn btn-sm btn-outline-vigaza" onclick="exportData('pdf')">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabel-pembesaran">
                                <thead class="bg-vigaza text-white">
                                    <tr>
                                        <th>No</th>
                                        <th><i class="fa fa-tag"></i> Periode</th>
                                        <th><i class="fa fa-home"></i> Kandang</th>
                                        <th><i class="fa fa-calendar"></i> Tgl Masuk</th>
                                        <th><i class="fa fa-users"></i> Populasi</th>
                                        <th><i class="fa fa-clock-o"></i> Umur</th>
                                        <th><i class="fa fa-calendar-check-o"></i> Target Panen</th>
                                        <th><i class="fa fa-bar-chart"></i> Progress</th>
                                        <th><i class="fa fa-info-circle"></i> Status</th>
                                        <th><i class="fa fa-cog"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data)): ?>
                                        <?php $no = 1; foreach ($data as $item): ?>
                                        <?php 
                                            $umur_hari = floor((strtotime(date('Y-m-d')) - strtotime($item->tgl_masuk)) / (60*60*24));
                                            $target_hari = floor((strtotime($item->target_panen) - strtotime($item->tgl_masuk)) / (60*60*24));
                                            $progress = $target_hari > 0 ? round(($umur_hari / $target_hari) * 100, 2) : 0;
                                            $progress = min($progress, 100);
                                            
                                            $progress_class = '';
                                            $progress_text = '';
                                            if ($item->status == 'selesai' || $item->status == 'panen') {
                                                $progress = 100;
                                                $progress_class = 'bg-success';
                                                $progress_text = 'Selesai';
                                            } else if ($progress >= 90) {
                                                $progress_class = 'bg-warning';
                                                $progress_text = 'Siap panen';
                                            } else if ($progress >= 70) {
                                                $progress_class = 'bg-info';
                                                $progress_text = 'Tahap akhir';
                                            } else {
                                                $progress_class = 'bg-vigaza';
                                                $progress_text = 'Dalam proses';
                                            }
                                            
                                            $status_class = '';
                                            switch ($item->status) {
                                                case 'aktif': $status_class = 'badge-warning'; break;
                                                case 'selesai': $status_class = 'badge-success'; break;
                                                case 'panen': $status_class = 'badge-success'; break;
                                                default: $status_class = 'badge-secondary';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-vigaza"><?= $item->periode ?></strong>
                                                <br><small class="text-muted">ID: <?= $item->id_pembesaran ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-vigaza"><?= $item->nama_kandang ?></span>
                                            </td>
                                            <td>
                                                <strong><?= date('d/m/Y', strtotime($item->tgl_masuk)) ?></strong>
                                                <br><small class="text-muted"><?= date('H:i', strtotime($item->tgl_masuk)) ?></small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= number_format($item->jml_saat_ini ?? $item->jml_awal) ?></strong>
                                                    <small class="text-muted">dari <?= number_format($item->jml_awal) ?></small>
                                                </div>
                                                <?php if (isset($item->jml_saat_ini) && $item->jml_saat_ini < $item->jml_awal): ?>
                                                    <?php $mortalitas = round((($item->jml_awal - $item->jml_saat_ini) / $item->jml_awal) * 100, 1); ?>
                                                    <small class="text-danger">Mortalitas: <?= $mortalitas ?>%</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= $umur_hari ?> hari</span>
                                                <?php if ($item->status == 'aktif'): ?>
                                                    <br><small class="text-muted">Target: <?= $target_hari ?> hari</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= date('d/m/Y', strtotime($item->target_panen)) ?></strong>
                                                <?php if ($item->status == 'aktif'): ?>
                                                    <?php 
                                                    $hari_tersisa = floor((strtotime($item->target_panen) - strtotime(date('Y-m-d'))) / (60*60*24));
                                                    if ($hari_tersisa < 0): ?>
                                                        <br><small class="text-danger">Terlambat <?= abs($hari_tersisa) ?> hari</small>
                                                    <?php elseif ($hari_tersisa == 0): ?>
                                                        <br><small class="text-warning">Hari ini</small>
                                                    <?php else: ?>
                                                        <br><small class="text-muted"><?= $hari_tersisa ?> hari lagi</small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="progress mb-1" style="height: 15px;">
                                                    <div class="progress-bar <?= $progress_class ?>" 
                                                         role="progressbar" style="width: <?= $progress ?>%" 
                                                         aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                                        <small><?= $progress ?>%</small>
                                                    </div>
                                                </div>
                                                <small class="text-muted"><?= $progress_text ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $status_class ?>"><?= ucfirst($item->status) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('pembesaran/detail/' . $item->id_pembesaran) ?>" 
                                                       class="btn btn-info btn-sm" title="Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if ($item->status == 'aktif'): ?>
                                                    <a href="<?= base_url('pembesaran/edit/' . $item->id_pembesaran) ?>" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-success btn-sm" 
                                                            onclick="panenPembesaran('<?= $item->id_pembesaran ?>', '<?= $item->periode ?>')" 
                                                            title="Panen">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-danger btn-sm" 
                                                            onclick="deletePembesaran('<?= $item->id_pembesaran ?>', '<?= $item->periode ?>')" 
                                                            title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i> Belum ada data pembesaran.
                                                    <a href="<?= base_url('pembesaran/tambah') ?>" class="btn btn-vigaza btn-sm ml-2">
                                                        <i class="fa fa-plus"></i> Tambah Sekarang
                                                    </a>
                                                </div>
                                            </td>
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

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#tabel-pembesaran').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 3, "desc" ]],
        "pageLength": 25,
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 8]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 8]
                }
            }
        ],
        "columnDefs": [
            { "orderable": false, "targets": [7, 9] }
        ]
    });

    // Toggle filter panel
    $('#toggle-filter').click(function() {
        $('#filter-panel').slideToggle();
        var icon = $(this).find('i');
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Apply filters
    $('#apply-filter').click(function() {
        var status = $('#filter-status').val();
        var kandang = $('#filter-kandang').val();
        var tanggal = $('#filter-tanggal').val();

        // Apply status filter
        if (status) {
            table.column(8).search(status);
        } else {
            table.column(8).search('');
        }

        // Apply kandang filter
        if (kandang) {
            table.column(2).search(kandang);
        } else {
            table.column(2).search('');
        }

        // Apply tanggal filter (you can enhance this)
        if (tanggal) {
            // Custom date filtering logic can be added here
        }

        table.draw();
        
        // Update record count
        setTimeout(function() {
            var info = table.page.info();
            $('#total-records').text('Ditampilkan: ' + info.recordsDisplay + ' dari ' + info.recordsTotal + ' record');
        }, 100);
    });

    // Reset filters
    $('#filter-status, #filter-kandang, #filter-tanggal').change(function() {
        if (!$(this).val()) {
            $('#apply-filter').click();
        }
    });
});

function panenPembesaran(id, periode) {
    Swal.fire({
        title: 'Selesaikan Pembesaran?',
        text: `Apakah Anda yakin ingin menyelesaikan periode "${periode}"? Status akan diubah menjadi "Panen".`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#0eaab4',
        confirmButtonText: 'Ya, Panen!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('pembesaran/panen/') ?>${id}`;
        }
    });
}

function deletePembesaran(id, periode) {
    Swal.fire({
        title: 'Hapus Pembesaran?',
        text: `Apakah Anda yakin ingin menghapus periode "${periode}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#0eaab4',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= base_url('pembesaran/hapus/') ?>${id}`;
        }
    });
}

function exportData(type) {
    if (type === 'excel') {
        $('#tabel-pembesaran').DataTable().button('.buttons-excel').trigger();
    } else if (type === 'pdf') {
        $('#tabel-pembesaran').DataTable().button('.buttons-pdf').trigger();
    }
}
</script>
