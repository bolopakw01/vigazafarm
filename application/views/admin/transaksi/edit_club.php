<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backadmin/up_club'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Club</h4>
                <p class="text-muted m-b-30">
                    Ubah data club
                </p>
                <div class="form-group">
                    <label>Nama Club</label>
                    <input type="hidden" name="id" value="<?= $data['id_mnl_club']; ?>">
                    <input class="form-control" name="nama" type="text" value="<?= $data['club']; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->