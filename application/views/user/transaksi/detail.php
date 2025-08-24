<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mt-0 header-title">Invoice</h4>
                    <p class="text-muted m-b-30">Daftar tagihan
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
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Nominal</th>
                                <th>#</th>
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
                                        <td><?= month($row->bulan); ?></td>
                                        <td><?= $row->tahun; ?></td>
                                        <td><?= rupiah($row->nominal); ?></td>
                                        <td>
                                            <?php if ($row->status == 'sudah') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-bukti"><button type="button" class="btn btn-success btn-sm" style="font-weight: bold;">Sudah Bayar</button></a>
                                            <?php } else  if ($row->status == 'proses') { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-info"><button type="button" class="btn btn-warning btn-sm" style="font-weight: bold;">Sedang Di Proses...</button></a>
                                            <?php } else { ?>
                                                <a href="javascript:void();" data-toggle="modal" data-target="#modal-kirim"><button type="button" class="btn btn-danger btn-sm" style="font-weight: bold;">Belum Bayar</button></a>
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
            <form enctype='multipart/form-data' method="post" action="<?= base_url('backuser/up_bukti_tf'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <!-- <input type="file" class="form-control" name="bukti"> -->
                        <input type="hidden" name="filelama" value="<?= $images['bukti']; ?>">
                        <input type="hidden" name="id" value="<?= $this->uri->segment(3); ?>">
                        <input class="form-control" type="file" name="fotopost" required>
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