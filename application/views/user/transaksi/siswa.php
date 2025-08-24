<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Invoice</h4>
                    <p class="text-muted m-b-30">Daftar siswa yang terdaftar
                        <!-- <span class="float-right d-md-block"><a href="<?php echo base_url('backoffice/tambah_siswa'); ?>"><button class="btn btn-sm btn-info pull-right"><i class="fa fa-plus"></i></button></a></span> -->
                    </p>
                </div>
                <div class="table-responsive">
                    <?= $this->session->flashdata('pesan') ?>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <!-- <th>No. HP</th> -->
                                <th>Status Siswa</th>
                                <th>SPP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($data)) {
                                $no = 1;
                                foreach ($data as $row) {
                            ?>
                                    <tr>
                                        <th scope="row"><?= $no++; ?></th>
                                        <td><?= ucwords($row->nama); ?></td>
                                        <td><?= $row->kelas; ?></td>
                                        <!-- <td><?= $row->no_hp; ?></td> -->
                                        <!-- <td>
                                            <?php if ($row->status == 'belum') { ?>
                                                <button type="button" class="btn btn-warning btn-sm" style="font-weight: bold;">BELUM REGISTRASI</button>
                                            <?php } else if ($row->status == 'aktif') { ?>
                                                <button type="button" class="btn btn-success btn-sm" style="font-weight: bold;">AKTIF</button>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-danger btn-sm" style="font-weight: bold;">TIDAK AKTIF</button>
                                            <?php } ?>

                                        </td> -->

                                        <!-- <td>
                                            <?php if ($row->status == 'belum') { ?>
                                                <a href="javascript:void();" data-toggle="modal" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" data-id="<?= $row->id_mnl_siswa; ?>" data-target="#modal-kirim"><button type="button" class="btn btn-warning btn-sm" style="font-weight: bold;">BELUM REGISTRASI</button></a>
                                            <?php } else  if ($row->status == 'proses') { ?>
                                                <a href="javascript:void();" data-toggle="modal" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" data-id="<?= $row->id_mnl_siswa; ?>" data-target="#modal-info"><button type="button" class="btn btn-warning btn-sm" style="font-weight: bold;">Sedang Di Proses...</button></a>
                                            <?php } else  if ($row->status == 'aktif') { ?>
                                                <a href="javascript:void();" data-toggle="modal" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" data-id="<?= $row->id_mnl_siswa; ?>" data-target="#modal-bukti"><button type="button" class="btn btn-success btn-sm" style="font-weight: bold;">AKTIF</button></a>
                                            <?php } else { ?>
                                                <a href="javascript:void();" data-toggle="modal" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" data-id="<?= $row->id_mnl_siswa; ?>" data-target="#modal-kirim"><button type="button" class="btn btn-danger btn-sm" style="font-weight: bold;">TIDAK AKTIF</button></a>
                                            <?php } ?>
                                        </td> -->
                                        <td>
                                            <?php if ($row->status == 'belum') { ?>
                                                <button type="button" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" class="edit btn btn-warning btn-sm" style="font-weight: bold;">BELUM REGISTRASI</button>
                                            <?php } else  if ($row->status == 'proses') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-info"><button type="button" class="btn btn-warning btn-sm" style="font-weight: bold;">Sedang Di Proses...</button></a>
                                            <?php } else  if ($row->status == 'aktif') { ?>
                                                <button type="button" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" class="lihat btn btn-success btn-sm" style="font-weight: bold;">AKTIF</button>
                                            <?php } else { ?>
                                                <button type="button" id_mnl_siswa="<?= $row->id_mnl_siswa; ?>" class="edits btn btn-danger btn-sm" style="font-weight: bold;">TIDAK AKTIF</button>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('backuser/detail/' . $row->id_mnl_siswa . ''); ?>"><button class="btn btn-sm btn-primary"><i class="fas fa-file-export"></i></button></a>
                                            <!-- <a href="<?php echo base_url('backuser/detail/' . $row->id_mnl_siswa . ''); ?>"><button class="btn btn-sm btn-danger" onClick="return doConfirm();"><i class="fa fa-trash"></i></button></a> -->
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

<div id="modal-bukti" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Bukti Transfer
                </h6>
            </div>
            <div class="modal-body">
                <img src="<?php echo base_url('assets/back/images/bukti/' . $images['bukti'] . ''); ?>" class="img-responsive" width="100%" height="auto" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-info" class="modal fade" style="width: 100%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    Info
                </h6>
            </div>
            <div class="modal-body">
                Pembayaran Anda sedang diproses oleh Admin max 1x24 jam. Jika status belum berubah setelah 1x24 jam harap hubungi Admin via Whatsapp. <br /><br />Terimakasih
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal">
    <form enctype='multipart/form-data' method="post" action="<?= base_url('backuser/up_bukti_regist'); ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title" id="judul"></h6>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div id="tampil_modal">
                        <!-- Data akan di tampilkan disini-->
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" id="tombol_kirim" class="btn btn-sm btn-success">Kirim</button>
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {

        $('.edit').click(function() {

            var id_mnl_siswa = $(this).attr("id_mnl_siswa");
            $.ajax({
                url: '<?php echo base_url(); ?>backuser/edit',
                method: 'post',
                data: {
                    id_mnl_siswa: id_mnl_siswa
                },
                success: function(data) {
                    $('#myModal').modal("show");
                    $('#tampil_modal').html(data);
                    document.getElementById("judul").innerHTML = 'Masukkan Bukti Transfer';
                    var element = document.getElementById("tombol_kirim");
                    element.style.display = "block";
                }
            });
        });

        $('.lihat').click(function() {

            var id_mnl_siswa = $(this).attr("id_mnl_siswa");
            $.ajax({
                url: '<?php echo base_url(); ?>backuser/lihat',
                method: 'post',
                data: {
                    id_mnl_siswa: id_mnl_siswa
                },
                success: function(data) {
                    $('#myModal').modal("show");
                    $('#tampil_modal').html(data);
                    document.getElementById("judul").innerHTML = 'Bukti Transfer';
                    var element = document.getElementById("tombol_kirim");
                    element.style.display = "none";
                }
            });
        });

    });
</script>