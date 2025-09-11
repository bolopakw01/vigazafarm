<!-- Start Page title and tab -->
<div class="section-body">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="header-action">
                <h1 class="page-title">Penetasan</h1>
                <ol class="breadcrumb page-breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Vigaza Farm</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Penetasan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="section-body mt-4">
    <div class="container-fluid">
        <div class="tab-content">
            <div class="tab-pane active" id="penetasan-all">
                <div class="card">
                    <div class="card-body">
                        <a href="<?= base_url('penetasan/tambah') ?>">
                            <button type="button" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> Tambah Penetasan
                            </button>
                        </a>
                        <div class="table-responsive">
                            <br />
                            <table class="table table-hover js-basic-example dataTable table-striped table_custom border-style spacing5">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Batch</th>
                                        <th>Mesin</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Jumlah Telur</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($penetasan) && !empty($penetasan)): ?>
                                        <?php $no = 1; foreach ($penetasan as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($p['batch']) ?></td>
                                            <td><?= htmlspecialchars($p['nama_mesin'] ?? 'N/A') ?></td>
                                            <td><?= date('d/m/Y', strtotime($p['tanggal_mulai'])) ?></td>
                                            <td><?= number_format($p['jumlah_telur']) ?></td>
                                            <td>
                                                <?php
                                                switch(strtolower($p['status'])) {
                                                    case 'proses': $status_class = 'badge-warning'; break;
                                                    case 'selesai': $status_class = 'badge-success'; break;
                                                    case 'gagal': $status_class = 'badge-danger'; break;
                                                    case 'persiapan': $status_class = 'badge-info'; break;
                                                    default: $status_class = 'badge-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $status_class ?>"><?= ucfirst($p['status']) ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-info" 
                                                            onclick="showDetail(<?= $p['id_penetasan'] ?>)" 
                                                            title="Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('penetasan/edit/' . $p['id_penetasan']) ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete(<?= $p['id_penetasan'] ?>)" 
                                                            title="Hapus">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data penetasan</td>
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

<!-- Professional Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="detailModalLabel">
                    <i class="fa fa-info-circle"></i> Detail Penetasan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Delete Confirmation -->
<script>
function showDetail(id) {
    $('#detailModal').modal('show');
    
    // Load detail data via AJAX
    $.ajax({
        url: '<?= base_url('penetasan/get_detail/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displayDetailContent(response.data);
            } else {
                $('#detailContent').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> 
                        Error: ${response.message || 'Gagal memuat data'}
                    </div>
                `);
            }
        },
        error: function() {
            $('#detailContent').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> 
                    Terjadi kesalahan saat memuat data
                </div>
            `);
        }
    });
}

function displayDetailContent(data) {
    // Calculate estimated completion date
    const startDate = new Date(data.tanggal_mulai);
    const estimatedEndDate = new Date(startDate);
    estimatedEndDate.setDate(startDate.getDate() + parseInt(data.lama_penetasan));
    
    // Calculate progress percentage
    const today = new Date();
    const daysPassed = Math.floor((today - startDate) / (1000 * 60 * 60 * 24));
    const progressPercentage = Math.min(Math.max((daysPassed / data.lama_penetasan) * 100, 0), 100);
    
    // Determine if overdue
    const isOverdue = today > estimatedEndDate && data.status !== 'selesai';
    const remainingDays = Math.ceil((estimatedEndDate - today) / (1000 * 60 * 60 * 24));
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fa fa-info-circle text-primary"></i> Informasi Umum</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr><td><strong>Batch:</strong></td><td>${data.batch}</td></tr>
                            <tr><td><strong>Mesin:</strong></td><td>${data.nama_mesin || 'N/A'}</td></tr>
                            <tr><td><strong>Tanggal Mulai:</strong></td><td>${formatDate(data.tanggal_mulai)}</td></tr>
                            <tr><td><strong>Estimasi Selesai:</strong></td><td>
                                <span class="${isOverdue ? 'text-danger font-weight-bold' : 'text-success'}">${formatDate(estimatedEndDate.toISOString().split('T')[0])}</span>
                                ${isOverdue ? '<small class="text-danger d-block">⚠️ Terlambat</small>' : 
                                 remainingDays > 0 ? `<small class="text-muted d-block">${remainingDays} hari lagi</small>` : 
                                 '<small class="text-success d-block">✅ Selesai tepat waktu</small>'}
                            </td></tr>
                            <tr><td><strong>Lama Penetasan:</strong></td><td>${data.lama_penetasan} hari</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-${getStatusClass(data.status)}">${data.status}</span></td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- Progress Timeline -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fa fa-clock text-warning"></i> Progress Timeline</h6>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar ${progressPercentage >= 100 ? 'bg-success' : 'bg-warning'}" 
                                 role="progressbar" style="width: ${progressPercentage}%" 
                                 aria-valuenow="${progressPercentage}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">
                            Hari ke-${Math.max(daysPassed, 0)} dari ${data.lama_penetasan} hari (${Math.round(progressPercentage)}%)
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fa fa-chart-bar text-success"></i> Statistik</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr><td><strong>Jumlah Telur:</strong></td><td>${formatNumber(data.jumlah_telur)}</td></tr>
                            <tr><td><strong>Hasil Menetas:</strong></td><td>${formatNumber(data.hasil_menetas)}</td></tr>
                            <tr><td><strong>Hasil Gagal:</strong></td><td>${formatNumber(data.hasil_gagal)}</td></tr>
                            <tr><td><strong>Persentase:</strong></td><td>
                                <span class="badge ${data.persentase_menetas >= 85 ? 'badge-success' : data.persentase_menetas >= 70 ? 'badge-warning' : 'badge-danger'}">
                                    ${data.persentase_menetas}%
                                </span>
                            </td></tr>
                            <tr><td><strong>Suhu Rata-rata:</strong></td><td>${data.suhu_rata}°C</td></tr>
                            <tr><td><strong>Kelembaban:</strong></td><td>${data.kelembaban_rata || 'N/A'}%</td></tr>
                        </table>
                    </div>
                </div>
                
                <!-- Environmental Conditions -->
                <div class="card border-info mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fa fa-thermometer-half text-info"></i> Kondisi Lingkungan</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-info">
                                    <i class="fa fa-thermometer-half fa-2x"></i>
                                    <h6 class="mt-2 mb-0">${data.suhu_rata}°C</h6>
                                    <small class="text-muted">Suhu</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-primary">
                                    <i class="fa fa-tint fa-2x"></i>
                                    <h6 class="mt-2 mb-0">${data.kelembaban_rata || 'N/A'}%</h6>
                                    <small class="text-muted">Kelembaban</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ${data.catatan ? `
        <div class="card border-info mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fa fa-sticky-note text-info"></i> Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">${data.catatan}</p>
            </div>
        </div>
        ` : ''}
    `;
    
    $('#detailContent').html(content);
}

function confirmDelete(id) {
    // First get the data to show batch name in confirmation
    const row = $(`button[onclick="showDetail(${id})"]`).closest('tr');
    const batchName = row.find('td').eq(1).text();
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus data penetasan ini?<br><strong>Batch: ${batchName}</strong><br><br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Data yang dihapus tidak dapat dikembalikan!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times"></i> Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '<?= base_url('penetasan/delete/') ?>' + id,
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                if (response.status !== 'success') {
                    throw new Error(response.message || 'Unknown error');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(`Error: ${error.message || error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data penetasan berhasil dihapus.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Refresh halaman untuk memperbarui data
                window.location.reload();
            });
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

function formatNumber(number) {
    return parseInt(number).toLocaleString('id-ID');
}

function getStatusClass(status) {
    switch(status.toLowerCase()) {
        case 'proses': return 'warning';
        case 'selesai': return 'success';
        case 'gagal': return 'danger';
        case 'persiapan': return 'info';
        default: return 'secondary';
    }
}
</script>

<!-- Custom CSS for better appearance -->
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #0fa9b4, #17a2b8) !important;
}

.table_custom {
    background: #fff;
}

.table_custom thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.btn-sm {
    margin: 0 1px;
}

.spacing5 td {
    padding: 12px 8px;
}

.border-style {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}
</style>
