<?php $this->load->view('mimin/template/header');?>
<?php $this->load->view('mimin/template/sidebar');?>
	
<div class="main-content">
	<section class="section">
		<div class="section-header">
			<h1><?php echo $title; ?></h1>
		</div>
		<div class="section-body">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<a href="#" class="btn btn-primary">Tambah Data Penetasan</a>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped" id="table-1">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th>Tgl Simpan Telur</th>
											<th>Jumlah Telur</th>
											<th>Tgl Menetas</th>
											<th>Jumlah Menetas</th>
											<th>Jumlah DOC</th>
											<th>Aksi</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$no = 1;
										foreach ($penetasan as $p) : ?>
											<tr>
												<td><?php echo $no++; ?></td>
												<td><?php echo $p->tanggal_simpan_telur; ?></td>
												<td><?php echo $p->jumlah_telur; ?></td>
												<td><?php echo $p->tanggal_menetas; ?></td>
												<td><?php echo $p->jumlah_menetas; ?></td>
												<td><?php echo $p->jumlah_doc; ?></td>
												<td>
													<a href="#" class="btn btn-info btn-sm">Edit</a>
													<a href="#" class="btn btn-danger btn-sm">Hapus</a>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('mimin/template/footer');?>