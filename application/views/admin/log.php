<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="mt-0 header-title">Log Aktifitas Aplikasi</h4>
                        <p class="text-muted m-b-30">Data log aktifikas</p>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($log)) {
                                    foreach ($log as $r) {
                                ?>
                                        <tr>
                                            <td><?= $r->tanggal; ?></td>
                                            <td><?= $r->waktu; ?></td>
                                            <td><?= $r->nama; ?> - <?= $r->jabatan; ?></td>
                                            <td><?= $r->aksi; ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->