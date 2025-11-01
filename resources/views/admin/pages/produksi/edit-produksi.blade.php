@extends('admin.layouts.app')

@section('title', 'Produksi - Edit Data Produksi')

@push('styles')
<style>
		body {
			background: linear-gradient(135deg, #f0f5ff, #ffffff);
			font-family: 'Inter', sans-serif;
			min-height: 100vh;
		}

		.card {
			border: none;
			border-radius: 1rem;
			background: #fff;
			box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
		}

		.card-header {
			background: white;
			color: #333;
			border-radius: 0;
			padding: 1rem 1.5rem 16px;
			border-bottom: 1px solid #e9ecef;
			margin-bottom: 24px;
		}

		.card-header h1 {
			font-size: 1.5rem;
			font-weight: 700;
			color: #333;
			line-height: 1.2;
		}

		.card-header p {
			margin-bottom: 0;
			font-size: 0.9rem;
			opacity: 0.9;
		}

		.form-label {
			font-weight: 600;
			color: #333;
		}

		.form-control, .form-select {
			border-radius: 0.5rem;
			border: 1px solid #ced4da;
			transition: 0.2s;
		}

		.form-control:focus, .form-select:focus {
			border-color: #007bff;
			box-shadow: 0 0 0 0.15rem rgba(0,123,255,0.2);
		}

		.section-box {
			background: #f8fbff;
			border: 1px solid #e0e6f0;
			border-radius: 0.75rem;
			padding: 1.5rem;
			margin-bottom: 1.5rem;
		}

		.section-title {
			font-weight: 600;
			color: #0077b6;
			border-left: 4px solid #00b4d8;
			padding-left: 0.75rem;
			margin-bottom: 1rem;
		}

		.section-title.manual {
			color: #007bff;
			border-left-color: #007bff;
		}

		.section-title.pembesaran {
			color: #28a745;
			border-left-color: #28a745;
		}

		.section-title.penetasan {
			color: #fd7e14;
			border-left-color: #fd7e14;
		}

		.btn-primary {
			background: linear-gradient(90deg, #007bff, #0096c7);
			border: none;
			border-radius: 0.5rem;
			transition: 0.3s;
		}

		.btn-primary:hover {
			transform: translateY(-2px);
			background: linear-gradient(90deg, #0069d9, #0084b4);
		}

		.btn-secondary {
			border-radius: 0.5rem;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
		}

		.btn-secondary:hover {
			box-shadow: 0 4px 8px rgba(0,0,0,0.15);
		}

		.badge-info {
			background-color: #0d6efd;
			color: #fff;
			font-weight: 500;
		}

		.readonly-input {
			background-color: #f1f5f9;
			font-weight: 600;
			color: #1e293b;
		}

		@media (max-width: 768px) {
			.card-header h1 {
				font-size: 1.2rem;
			}
			.btn-secondary {
				padding: 0.375rem 0.75rem;
				font-size: 0.875rem;
				white-space: nowrap;
			}
			.btn-secondary i {
				font-size: 0.8rem;
			}
		}

		.required {
			color: #dc3545;
			font-weight: bold;
		}
</style>
@endpush

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-12">
			<div class="card">
				<div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
					<div>
						<h1>Edit Data Produksi</h1>
						<p class="mb-0">Perbarui data batch produksi sesuai kebutuhan aktual.</p>
					</div>
					<div class="mt-3 mt-lg-0">
						<a href="{{ route('admin.produksi') }}" class="btn btn-secondary">
							<i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Produksi
						</a>
					</div>
				</div>

				<div class="card-body p-4">
				  @php
					  $tanggalMulaiValue = old('tanggal_mulai', $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('Y-m-d') : '');
					  $tanggalAkhirValue = old('tanggal_akhir', $produksi->tanggal_akhir ? \Carbon\Carbon::parse($produksi->tanggal_akhir)->format('Y-m-d') : '');
				  @endphp
				  <form action="{{ route('admin.produksi.update', $produksi) }}" method="POST">
						@csrf
						@method('PATCH')

						@if(session('error'))
							<div class="alert alert-danger">
								<i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
							</div>
						@endif

						@if($errors->any())
							<div class="alert alert-danger">
								<strong>Terjadi Kesalahan Validasi</strong>
								<ul class="mb-0 mt-2">
									@foreach($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<div class="section-box">
							<h6 class="section-title">Informasi Dasar</h6>
							<div class="row g-3">
								<div class="col-md-6">
									<label for="batch_produksi_id" class="form-label">Batch Produksi ID <span class="required">*</span></label>
									<input type="text" id="batch_produksi_id" name="batch_produksi_id" class="form-control @error('batch_produksi_id') is-invalid @enderror" value="{{ old('batch_produksi_id', $produksi->batch_produksi_id) }}" required>
									@error('batch_produksi_id')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-3">
									<label class="form-label">Tipe Produksi</label>
									<div class="form-control readonly-input">
										<span class="badge badge-info text-uppercase">{{ $produksi->tipe_produksi }}</span>
									</div>
								</div>
								<div class="col-md-3">
									<label class="form-label">Sumber Data</label>
									<div class="form-control readonly-input">
										<span class="badge bg-secondary text-uppercase">{{ $produksi->jenis_input }}</span>
									</div>
								</div>
							</div>

							<div class="row g-3 mt-1">
								<div class="col-md-6">
									<label for="kandang_id" class="form-label">Kandang <span class="required">*</span></label>
									<select id="kandang_id" name="kandang_id" class="form-select @error('kandang_id') is-invalid @enderror" required>
										<option value="">Pilih Kandang</option>
										@foreach($kandangList as $kandang)
											<option value="{{ $kandang->id }}" {{ old('kandang_id', $produksi->kandang_id) == $kandang->id ? 'selected' : '' }}>
												{{ $kandang->nama_dengan_detail }}
											</option>
										@endforeach
									</select>
									@error('kandang_id')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-3">
									<label for="status" class="form-label">Status <span class="required">*</span></label>
									@php
										$statusValue = old('status', $produksi->status);
										if (in_array($statusValue, ['selesai', 'dibatalkan'])) {
											$statusValue = 'tidak_aktif';
										}
									@endphp
									<select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
										<option value="">Pilih Status</option>
										<option value="aktif" {{ $statusValue === 'aktif' ? 'selected' : '' }}>Aktif</option>
										<option value="tidak_aktif" {{ $statusValue === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
									</select>
									@error('status')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-3">
									<label class="form-label">Dari Pembesaran</label>
									<div class="form-control readonly-input">
										{{ $produksi->pembesaran ? $produksi->pembesaran->batch_produksi_id : '-' }}
									</div>
								</div>
							</div>
						</div>

						<div class="section-box">
							<h6 class="section-title">Periode Produksi</h6>
							<div class="row g-3">
								<div class="col-md-6">
									<label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="required">*</span></label>
									<input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ $tanggalMulaiValue }}" required>
									@error('tanggal_mulai')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="col-md-6">
									<label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
									<input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" value="{{ $tanggalAkhirValue }}">
									@error('tanggal_akhir')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
									<div class="form-text">Opsional, isi jika batch dinonaktifkan.</div>
								</div>
							</div>
						</div>

						@if($produksi->tipe_produksi === 'puyuh')
							<div class="section-box">
								<h6 class="section-title manual">Detail Produksi Puyuh</h6>
								<div class="row g-3">
									<div class="col-md-4">
										<label for="jumlah_indukan" class="form-label">Jumlah Indukan <span class="required">*</span></label>
										<input type="number" min="1" id="jumlah_indukan" name="jumlah_indukan" class="form-control @error('jumlah_indukan') is-invalid @enderror" value="{{ old('jumlah_indukan', $produksi->jumlah_indukan) }}" required>
										@error('jumlah_indukan')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-4">
										<label for="jumlah_jantan" class="form-label">Jumlah Jantan</label>
										<input type="number" min="0" id="jumlah_jantan" name="jumlah_jantan" class="form-control" value="{{ old('jumlah_jantan', $produksi->jumlah_jantan) }}">
									</div>
									<div class="col-md-4">
										<label for="jumlah_betina" class="form-label">Jumlah Betina</label>
										<input type="number" min="0" id="jumlah_betina" name="jumlah_betina" class="form-control" value="{{ old('jumlah_betina', $produksi->jumlah_betina) }}">
									</div>
								</div>
								<div class="row g-3 mt-1">
									<div class="col-md-6">
										<label for="umur_mulai_produksi" class="form-label">Umur Mulai Produksi (hari)</label>
										<input type="number" min="1" id="umur_mulai_produksi" name="umur_mulai_produksi" class="form-control @error('umur_mulai_produksi') is-invalid @enderror" value="{{ old('umur_mulai_produksi', $produksi->umur_mulai_produksi) }}">
										@error('umur_mulai_produksi')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<label for="harga_per_kg" class="form-label">Harga per KG</label>
										<input type="number" step="0.01" min="0" id="harga_per_kg" name="harga_per_kg" class="form-control @error('harga_per_kg') is-invalid @enderror" value="{{ old('harga_per_kg', $produksi->harga_per_kg) }}">
										@error('harga_per_kg')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						@else
							<div class="section-box">
								<h6 class="section-title penetasan">Detail Produksi Telur</h6>
								<div class="row g-3">
									<div class="col-md-4">
										<label for="jumlah_telur" class="form-label">Jumlah Telur <span class="required">*</span></label>
										<input type="number" min="1" id="jumlah_telur" name="jumlah_telur" class="form-control @error('jumlah_telur') is-invalid @enderror" value="{{ old('jumlah_telur', $produksi->jumlah_telur) }}" required>
										@error('jumlah_telur')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-4">
										<label for="berat_rata_telur" class="form-label">Berat Rata-rata Telur (gram)</label>
										<input type="number" step="0.01" min="0" id="berat_rata_telur" name="berat_rata_telur" class="form-control @error('berat_rata_telur') is-invalid @enderror" value="{{ old('berat_rata_telur', $produksi->berat_rata_telur) }}">
										@error('berat_rata_telur')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-4">
										<label for="harga_per_kg" class="form-label">Harga per KG</label>
										<input type="number" step="0.01" min="0" id="harga_per_kg" name="harga_per_kg" class="form-control @error('harga_per_kg') is-invalid @enderror" value="{{ old('harga_per_kg', $produksi->harga_per_kg) }}">
										@error('harga_per_kg')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						@endif

						<div class="section-box">
							<h6 class="section-title">Catatan Tambahan</h6>
							<textarea id="catatan" name="catatan" rows="4" class="form-control">{{ old('catatan', $produksi->catatan) }}</textarea>
							<div class="form-text">Gunakan catatan untuk menuliskan informasi tambahan seperti performa atau rencana tindak lanjut.</div>
						</div>

						<div class="d-flex flex-column flex-md-row gap-3 justify-content-end">
							<a href="{{ route('admin.produksi') }}" class="btn btn-outline-secondary">
								<i class="fa-solid fa-xmark me-2"></i>Batal
							</a>
							<button type="submit" class="btn btn-primary">
								<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
