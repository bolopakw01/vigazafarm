<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Pembayaran Registrasi</h4>
                    <p class="text-muted m-b-30">Daftar data pembayaran registrasi
                        <!-- <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_campaign'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span> -->
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
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
                                        <td><?= rupiah($row->nominal); ?></td>
                                        <?php if ($stat == 'belum') { ?>
                                            <td>0000-00-00 || 00:00:00</td>
                                        <?php } else { ?>
                                            <td><?= $row->tanggal; ?> || <?= $row->waktu; ?></td>
                                        <?php } ?>
                                        <td>
                                            <?php if ($stat == 'belum') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-kirim"><button data-id="<?= $row->id_mnl_siswa; ?>" data-regist="<?= $row->id_mnl_regist; ?>" class="btn btn-sm btn-danger bb" style="font-weight:bold;">Belum Bayar</button></a>
                                            <?php } else if ($stat == 'proses') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-approve"><button data-id="<?= $row->id_mnl_siswa; ?>" data-bukti="<?= $row->bukti; ?>" data-ket="<?= $row->ket; ?>" class="btn btn-sm btn-warning ba" style="font-weight:bold;">Butuh Approval</button></a>
                                            <?php } else { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-bukti"><button data-bukti="<?= $row->bukti; ?>" data-kets="<?= $row->ket; ?>" class="btn btn-sm btn-success sb" style="font-weight:bold;">Sudah Bayar</button></a>
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

<div id="modal-kirim" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Masukkan Bukti Transfer
                </h6>
            </div>
            <form method="post" enctype="multipart/form-data" action="<?= base_url('backoffice/up_bukti_regist'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id_mnl_siswa">
                        <input type="hidden" name="id_regist" id="id_mnl_regist">
                        <input type="file" class="form-control" name="fotopost">
                    </div>
                    <div class="form-group">
                        <textarea name="ket" class="form-control" placeholder="Harap isikan keterangan ini ketika Anda membayar secara kolektif ataupun jika ada catatan lainnya"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Kirim</button>
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-approve" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <?php echo form_open_multipart('backoffice/approve_regist'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Bukti Transfer & Approval
                </h6>
            </div>
            <div class="modal-body">
                <img id="buktinya">
                <input type="hidden" name="id" id="ids">
                <br /><br />
                <textarea id="ketnya" class="form-control" readonly="readonly"></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-success" onClick="return proses();">Approve</button>
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div id="modal-bukti" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Bukti Transfer
                </h6>
            </div>
            <div class="modal-body">
                <img id="buktitf">
                <br /><br />
                <textarea id="ketsnya" class="form-control" readonly="readonly"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(".bb").click(function() {
        var ids = $(this).attr('data-id');
        var id_regist = $(this).attr('data-regist');
        $("#id_mnl_siswa").val(ids);
        $("#id_mnl_regist").val(id_regist);
    });

    $(".ba").click(function() {
        var ids = $(this).attr('data-id');
        $("#ids").val(ids);

        var bukti = $(this).attr('data-bukti');
        document.getElementById('buktinya').src = "<?= base_url('assets/back/images/regist/'); ?>" + bukti;
        document.getElementById('buktinya').style.width = '100%';
        document.getElementById('buktinya').style.height = 'auto';

        let ket = $(this).attr('data-ket');
        document.getElementById("ketnya").innerHTML = ket;
    });

    $(".sb").click(function() {
        let kets = $(this).attr('data-kets');
        document.getElementById("ketsnya").innerHTML = kets;

        var buktitrf = $(this).attr('data-bukti');
        document.getElementById('buktitf').src = "<?= base_url('assets/back/images/regist/'); ?>" + buktitrf;
        document.getElementById('buktitf').style.width = '100%';
        document.getElementById('buktitf').style.height = 'auto';
    });
</script>