<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backadmin/in_admin'); ?>
            <div class="card-body">

                <h4 class="mt-0 header-title">Admin</h4>
                <p class="text-muted m-b-30">
                    Tambah data admin
                </p>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Nama</label>
                            <input class="form-control" name="nama" type="text">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input class="form-control" name="uname" type="text">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" name="pass" type="text">
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input class="form-control" name="no_hp" type="text">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->