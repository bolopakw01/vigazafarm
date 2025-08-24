<div class="row">
    <div class="col-xl-4 col-md-4">
        <div class="card bg-primary mini-stat text-white">
            <div class="p-3 mini-stat-desc">
                <div class="clearfix">
                    <h6 class="text-uppercase mt-0 float-left text-white-50">Jumlah Siswa</h6>
                    <h4 class="mb-3 mt-0 float-right"><?= $jml_siswa; ?></h4>
                </div>
                <div>
                    <span>Data Keseluruhan</span>
                </div>

            </div>
        </div>
    </div>

    <?php
    if ($jml_bb != null) {
        $pres_bb = ($jml_bb / $jml_siswa) * 100;
    } else {
        $pres_bb = 0;
    }

    if ($jml_br != null) {
        $pres_br = ($jml_br / $jml_siswa) * 100;
    } else {
        $pres_br = 0;
    }
    ?>

    <div class="col-xl-4 col-md-4">
        <div class="card bg-info mini-stat text-white">
            <div class="p-3 mini-stat-desc">
                <div class="clearfix">
                    <h6 class="text-uppercase mt-0 float-left text-white-50">Belum Bayar SPP</h6>
                    <h4 class="mb-3 mt-0 float-right"><?= $jml_bb; ?></h4>
                </div>
                <div>
                    <?php if ($pres_bb < 0) { ?>
                        <span class="badge badge-light text-danger"> <?= round($pres_bb, 0); ?> %</span>
                    <?php } else if ($pres_bb > 0) { ?>
                        <span class="badge badge-light text-success"> <?= round($pres_bb, 0); ?> %</span>
                    <?php } else { ?>
                        <span class="badge badge-light text-primary"> <?= round($pres_bb, 0); ?> %</span>
                    <?php } ?>
                    <span class="ml-2">Dari Data Keseluruhan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4">
        <div class="card bg-pink mini-stat text-white">
            <div class="p-3 mini-stat-desc">
                <div class="clearfix">
                    <h6 class="text-uppercase mt-0 float-left text-white-50">Belum Registrasi</h6>
                    <h4 class="mb-3 mt-0 float-right"><?= $jml_br; ?></h4>
                </div>
                <div>
                    <?php if ($pres_br < 0) { ?>
                        <span class="badge badge-light text-danger"> <?= round($pres_br, 0); ?> %</span>
                    <?php } else if ($pres_br > 0) { ?>
                        <span class="badge badge-light text-success"> <?= round($pres_br, 0); ?> %</span>
                    <?php } else { ?>
                        <span class="badge badge-light text-primary"> <?= round($pres_br, 0); ?> %</span>
                    <?php } ?>
                    <span class="ml-2">Dari Data Keseluruhan</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Butuh Approval</h4>
                    <p class="text-muted m-b-30">Daftar data pembayaran SPP
                        <!-- <span class="float-right d-md-block"><a href="<?php echo base_url('backadmin/tambah_campaign'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span> -->
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatabless" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Nominal</th>
                                <th>Tanggal Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $row) {
                                    $stat = $row->status;
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= $row->nama; ?></td>
                                        <td><?= month($row->bulan); ?></td>
                                        <td><?= $row->tahun; ?></td>
                                        <td><?= rupiah($row->nominal); ?></td>
                                        <td><?= $row->tanggal; ?> || <?= $row->waktu; ?></td>
                                        <td>
                                            <?php if ($stat == 'belum') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-kirim"><button data-id="<?= $row->id_mnl_siswa; ?>" data-spp="<?= $row->id_mnl_spp; ?>" data-bukti="<?= $row->bukti; ?>" class="btn btn-sm btn-danger bb" style="font-weight:bold;">Belum Bayar</button></a>
                                            <?php } else if ($stat == 'proses') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-approve"><button data-id="<?= $row->id_mnl_siswa; ?>" data-bukti="<?= $row->bukti; ?>" class="btn btn-sm btn-warning ba" style="font-weight:bold;">Butuh Approval</button></a>
                                            <?php } else { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-bukti"><button data-bukti="<?= $row->bukti; ?>" class="btn btn-sm btn-success sb" style="font-weight:bold;">Sudah Bayar</button></a>
                                            <?php } ?>
                                        </td>
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
    </div> <!-- end col -->
</div> <!-- end row -->