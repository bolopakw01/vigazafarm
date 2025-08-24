<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/up_jkamar'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Jenis Kamar</h4>
                <p class="text-muted m-b-30">
                    Ubah data jenis kamar
                </p>
                <div class="form-group">
                    <label>Jenis Kamar</label>
                    <input type="hidden" name="id" value="<?= $data['id_jenis_kamar']; ?>">
                    <input class="form-control" name="nama" type="text" value="<?= $data['jenis']; ?>">
                </div>
                <div class="form-group">
                    <label>Harga Kamar</label>
                    <input class="form-control" name="harga" type="text" onkeyup="convertToRupiah(this);" value="<?= $data['harga']; ?>">
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <input class="form-control" name="tahun" type="text" value="<?= $data['tahun']; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->