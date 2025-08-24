<div class="row">
	<div class="col-lg-6">
		<div class="card m-b-30">
			<?php echo form_open_multipart('backuser/update_profil'); ?>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="mt-0 header-title">Profil</h4>
						<p class="text-muted m-b-30">Ubah data profil</p>
					</div>
					<div class="col-md-12">
						<input type="hidden" class="form-control" name="minid" value="<?= $profil['id_mnl_ortu']; ?>" />
						<input type="hidden" class="form-control" name="pass_db" value="<?= $profil['password']; ?>" />
						<div class="form-group">
							<label>Nama</label>
							<div>
								<input type="text" name="nm" class="form-control" value="<?= $profil['nama']; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label>Username</label>
							<div>
								<input type="text" name="uname" class="form-control" value="<?= $profil['no_hp']; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label>Password</label>
							<div>
								<input type="password" name="pass" class="form-control" value="<?= $profil['password']; ?>" />
							</div>
						</div>
						<div class="form-group">
							<div>
								<button type="submit" class="btn btn-primary waves-effect waves-light">
									Submit
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div> <!-- end col -->
</div> <!-- end row -->