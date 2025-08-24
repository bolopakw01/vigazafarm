<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/in_jkamar'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Jenis Kamar</h4>
                <p class="text-muted m-b-30">
                    Tambah data jenis kamar
                </p>
                <div class="form-group">
                    <label>Jenis Kamar</label>
                    <input class="form-control" name="nama" type="text">
                </div>
                <div class="form-group">
                    <label>Harga Kamar</label>
                    <input class="form-control" name="harga" onkeyup="convertToRupiah(this);" type="text">
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <input class="form-control" name="tahun" type="text">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->