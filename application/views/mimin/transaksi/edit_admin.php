<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <?php echo form_open_multipart('backoffice/up_admin'); ?>
            <div class="card-body">
                <h4 class="mt-0 header-title">Admin</h4>
                <p class="text-muted m-b-30">
                    Ubah data admin
                </p>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="hidden" name="id" value="<?= $data['minid']; ?>">
                            <input type="hidden" name="pass_db" value="<?= $data['password']; ?>" />
                            <input class="form-control" name="nama" type="text" value="<?= $data['nama']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Usrename</label>
                            <input class="form-control" name="uname" type="text" value="<?= $data['username']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input class="form-control" name="pass" type="password" value="<?= $data['password']; ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP</label>
                            <input class="form-control" name="no_hp" type="text" value="<?= $data['hp']; ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div> <!-- end col -->

</div> <!-- end row -->