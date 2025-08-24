<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backadmin/in_lokasi'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Lokasi</h4>
                <p class="text-muted m-b-30">
                    Tambah data lokasi
                </p>
                <div class="form-group">
                    <label>Nama Lokasi</label>
                    <input class="form-control" name="nama" type="text">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->