@extends('admin.layouts.app')

@section('title', 'Set Pakan & Vitamin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
	@font-face {
		font-family: 'AlanSans';
		src: url('{{ asset("bolopa/font/AlanSans-VariableFont_wght.ttf") }}') format('truetype');
		font-weight: 100 900;
		font-display: swap;
	}

	.pakvit-wrapper {
		padding: 20px;
		font-family: 'Poppins', sans-serif;
	}

	.pakvit-card {
		background: #fff;
		border-radius: 12px;
		border: 1px solid #e9ecef;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
		overflow: hidden;
	}

	.pakvit-card__header {
		padding: 25px 30px;
		background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
		border-bottom: 2px solid #e9ecef;
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		gap: 20px;
		flex-wrap: wrap;
	}

	.pakvit-card__title {
		font-size: 1.75rem;
		font-weight: 600;
		color: #1f2937;
		font-family: 'AlanSans', sans-serif;
		display: flex;
		align-items: center;
		gap: 10px;
		margin: 0;
	}

	.pakvit-card__title i {
		color: #4f46e5;
	}

	.pakvit-card__subtitle {
		color: #6b7280;
		font-size: 0.95rem;
		margin: 0;
	}

	.pakvit-card__body {
		padding: 30px;
		background: #fff;
		display: grid;
		gap: 28px;
	}

	.btn {
		padding: 8px 16px;
		border-radius: 4px;
		border: none;
		font-weight: 600;
		display: inline-flex;
		align-items: center;
		gap: 8px;
		cursor: pointer;
		text-decoration: none;
		transition: background 0.2s ease, color 0.2s ease, border 0.2s ease, box-shadow 0.2s ease;
	}

	.btn-primary {
		background: #2563eb;
		color: #fff;
	}

	.btn-primary:hover {
		background: #1d4ed8;
	}

	.btn-secondary {
		background: #5c636a;
		color: #fff;
	}

	.btn-secondary:hover {
		background: #4a5568;
	}

	.btn-outline {
		background: transparent;
		border: 2px solid #d1d5db;
		color: #374151;
	}

	.btn-outline:hover {
		border-color: #2563eb;
		color: #2563eb;
	}

	.btn-danger {
		background: #dc2626;
		color: #fff;
	}

	.btn-danger:hover {
		background: #b91c1c;
	}

	.btn-sm {
		padding: 6px 10px;
		font-size: 0.85rem;
	}

	.pakvit-form {
		background: #f8f9fa;
		border-radius: 8px;
		border: 1px solid #e9ecef;
		padding: 25px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.pakvit-form h4 {
		margin-bottom: 20px;
		font-size: 1.1rem;
		font-weight: 600;
		color: #495057;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.pakvit-form h4 i {
		color: #4299e1;
	}

	.pakvit-form__grid {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 20px;
	}

	.form-group {
		margin-bottom: 20px;
	}

	.form-group label {
		display: flex;
		align-items: center;
		gap: 6px;
		margin-bottom: 6px;
		font-weight: 600;
		color: #495057;
	}

	.form-control,
	.form-select {
		width: 100%;
		border-radius: 6px;
		border: 2px solid #e9ecef;
		padding: 10px 12px;
		font-size: 0.95rem;
		transition: border-color 0.2s ease, box-shadow 0.2s ease;
		background: #fff;
	}

	.form-control:focus,
	.form-select:focus {
		outline: none;
		border-color: #2563eb;
		box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
	}

	.form-help {
		display: block;
		margin-top: 5px;
		font-size: 12px;
		color: #6c757d;
		font-style: italic;
	}

	.edit-form__grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
	}

	.pakvit-list {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 20px;
	}

	.pakvit-section {
		border: 1px solid #e9ecef;
		border-radius: 12px;
		background: #fff;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
		display: flex;
		flex-direction: column;
	}

	.pakvit-section header {
		padding: 18px 24px;
		border-bottom: 1px solid #edf2f7;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.pakvit-section header h5 {
		margin: 0;
		font-size: 1rem;
		font-weight: 600;
		color: #111827;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.pakvit-items {
		padding: 20px;
		display: grid;
		gap: 16px;
	}

	.pakvit-item {
		border: 1px solid #e5e7eb;
		border-radius: 10px;
		padding: 16px;
		background: #fdfefe;
		display: flex;
		flex-direction: column;
		gap: 12px;
		box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
	}

	.pakvit-item__top {
		display: flex;
		justify-content: space-between;
		gap: 12px;
		flex-wrap: wrap;
	}

	.pakvit-item__name {
		font-weight: 600;
		color: #111827;
	}

	.badge {
		padding: 5px 12px;
		border-radius: 999px;
		font-size: 0.8rem;
		font-weight: 600;
	}

	.badge-active {
		background: #ecfdf5;
		color: #047857;
	}

	.badge-inactive {
		background: #fef2f2;
		color: #b91c1c;
	}

	.pakvit-item__meta {
		display: flex;
		flex-wrap: wrap;
		gap: 12px;
		font-size: 0.9rem;
		color: #4b5563;
	}

	.item-actions {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
		align-items: center;
		justify-content: flex-end;
	}

	details summary {
		cursor: pointer;
		color: #2563eb;
		font-weight: 600;
		display: inline-flex;
		align-items: center;
		gap: 6px;
	}

	details[open] summary {
		margin-bottom: 12px;
	}

	.edit-form {
		background: #f8f9fa;
		border-radius: 8px;
		border: 1px solid #e5e7eb;
		padding: 16px;
	}

	.toggle-field {
		display: flex;
		align-items: center;
		gap: 8px;
		margin-top: 6px;
	}

	.alert-soft {
		padding: 12px 16px;
		border-radius: 10px;
		margin-bottom: 16px;
		font-weight: 500;
		border: 1px solid transparent;
	}

	.alert-success {
		background: #ecfdf5;
		color: #065f46;
		border-color: #a7f3d0;
	}

	.alert-error {
		background: #fef2f2;
		color: #991b1b;
		border-color: #fecaca;
	}

	.empty-state-small {
		padding: 24px;
		text-align: center;
		color: #6b7280;
		border: 1px dashed #d1d5db;
		border-radius: 10px;
		background: #f9fafb;
	}

	@media (max-width: 640px) {
		.pakvit-card__header {
			flex-direction: column;
			align-items: flex-start;
		}

		.pakvit-card__body {
			padding: 20px;
		}

		.pakvit-form {
			padding: 20px;
		}

		.pakvit-section header {
			padding: 16px;
		}
	}

	.pakvit-form__actions {
		display: flex;
		justify-content: flex-end;
		gap: 10px;
	}
</style>
@endpush

@section('content')
@php
	$formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp
<div class="pakvit-wrapper">
	<div class="pakvit-card">
		<div class="pakvit-card__header">
			<div class="header-left">
				<h1 class="pakvit-card__title">
					<i class="fas fa-seedling"></i>
					Set Pakan & Vitamin
				</h1>
				<p class="pakvit-card__subtitle">Kelola daftar pakan dan vitamin agar mudah dipilih pada formulir pencatatan.</p>
			</div>
			<div class="header-right">
				<button onclick="history.back()" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i> Kembali
				</button>
			</div>
		</div>

		<div class="pakvit-card__body">
			@if (session('success'))
				<div class="alert-soft alert-success">
					<i class="fas fa-circle-check me-2"></i>{{ session('success') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert-soft alert-error">
					<strong>Terjadi kesalahan:</strong>
					<ul class="mb-0 mt-2">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<div class="pakvit-form">
				<h4><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Item Baru</h4>
				<form method="POST" action="{{ route('admin.sistem.pakanvitamin.store') }}">
					@csrf
					<div class="pakvit-form__grid">
						<div class="form-group">
							<label for="category">Kategori</label>
							<select name="category" id="category" class="form-select" required>
								<option value="">Pilih kategori</option>
								<option value="pakan">Pakan</option>
								<option value="vitamin">Vitamin</option>
							</select>
							<small class="form-help">Pilih kategori pakan atau vitamin untuk item ini</small>
						</div>
						<div class="form-group">
							<label for="name">Nama Item</label>
							<input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Pakan Starter 1" required>
							<small class="form-help">Beri nama yang jelas dan deskriptif untuk item pakan/vitamin</small>
						</div>
						<div class="form-group">
							<label for="price">Harga</label>
							<input type="number" min="0" step="100" name="price" id="price" class="form-control" placeholder="Masukkan harga" required>
							<small class="form-help">Masukkan harga per satuan dalam rupiah</small>
						</div>
						<div class="form-group">
							<label for="unit">Satuan</label>
							<input type="text" name="unit" id="unit" class="form-control" placeholder="contoh: kg, liter" required>
							<small class="form-help">Contoh: kg, liter, gram, botol, dll.</small>
						</div>
						<div class="form-group">
							<label>Status</label>
							<div class="toggle-field">
								<input type="checkbox" name="is_active" id="is_active" value="1" checked>
								<label for="is_active" class="mb-0">Aktifkan</label>
							</div>
						</div>
					</div>
					<div class="pakvit-form__actions">
						<button type="reset" class="btn btn-outline"><i class="fas fa-eraser"></i>Reset</button>
						<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Simpan Item</button>
					</div>
				</form>
			</div>

			<div class="pakvit-list">
				<section class="pakvit-section">
					<header>
						<h5><i class="fas fa-wheat-awn"></i> Daftar Pakan</h5>
						<span class="badge badge-active">{{ $feedItems->count() }} item</span>
					</header>
					<div class="pakvit-items">
						@forelse ($feedItems as $item)
							<article class="pakvit-item">
								<div class="pakvit-item__top">
									<span class="pakvit-item__name">{{ $item->name }}</span>
									<span class="badge {{ $item->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
								</div>
								<div class="pakvit-item__meta">
									<span><i class="fas fa-tag me-1"></i>{{ $formatCurrency($item->price) }}</span>
									<span><i class="fas fa-balance-scale me-1"></i>Satuan: {{ $item->unit }}</span>
								</div>
								<div class="item-actions">
									<details>
										<summary><i class="fas fa-edit"></i> Edit</summary>
										<div class="edit-form mt-2">
											<form method="POST" action="{{ route('admin.sistem.pakanvitamin.update', $item) }}">
												@csrf
												@method('PUT')
												<div class="edit-form__grid">
													<div class="form-group">
														<label>Nama</label>
														<input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
														<small class="form-help">Beri nama yang jelas dan deskriptif</small>
													</div>
													<div class="form-group">
														<label>Harga</label>
														<input type="number" name="price" class="form-control" value="{{ $item->price }}" min="0" step="100" required>
														<small class="form-help">Masukkan harga per satuan dalam rupiah</small>
													</div>
													<div class="form-group">
														<label>Satuan</label>
														<input type="text" name="unit" class="form-control" value="{{ $item->unit }}" required>
														<small class="form-help">Contoh: kg, liter, gram, botol, dll.</small>
													</div>
													<div class="form-group">
														<label>Status</label>
														<div class="toggle-field">
															<input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
															<span>Aktif</span>
														</div>
													</div>
												</div>
												<input type="hidden" name="category" value="pakan">
												<div class="pakvit-form__actions">
													<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i>Update</button>
												</div>
											</form>
										</div>
									</details>
									<form method="POST" action="{{ route('admin.sistem.pakanvitamin.destroy', $item) }}" class="pakvit-delete-form" data-item-name="{{ $item->name }}">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>Hapus</button>
									</form>
								</div>
							</article>
						@empty
							<div class="empty-state-small">
								<i class="fas fa-info-circle me-1"></i>Belum ada data pakan.
							</div>
						@endforelse
					</div>
				</section>

				<section class="pakvit-section">
					<header>
						<h5><i class="fas fa-prescription-bottle"></i> Daftar Vitamin</h5>
						<span class="badge badge-active">{{ $vitaminItems->count() }} item</span>
					</header>
					<div class="pakvit-items">
						@forelse ($vitaminItems as $item)
							<article class="pakvit-item">
								<div class="pakvit-item__top">
									<span class="pakvit-item__name">{{ $item->name }}</span>
									<span class="badge {{ $item->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
								</div>
								<div class="pakvit-item__meta">
									<span><i class="fas fa-tag me-1"></i>{{ $formatCurrency($item->price) }}</span>
									<span><i class="fas fa-vial me-1"></i>Satuan: {{ $item->unit }}</span>
								</div>
								<div class="item-actions">
									<details>
										<summary><i class="fas fa-edit"></i> Edit</summary>
										<div class="edit-form mt-2">
											<form method="POST" action="{{ route('admin.sistem.pakanvitamin.update', $item) }}">
												@csrf
												@method('PUT')
												<div class="edit-form__grid">
													<div class="form-group">
														<label>Nama</label>
														<input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
														<small class="form-help">Beri nama yang jelas dan deskriptif</small>
													</div>
													<div class="form-group">
														<label>Harga</label>
														<input type="number" name="price" class="form-control" value="{{ $item->price }}" min="0" step="100" required>
														<small class="form-help">Masukkan harga per satuan dalam rupiah</small>
													</div>
													<div class="form-group">
														<label>Satuan</label>
														<input type="text" name="unit" class="form-control" value="{{ $item->unit }}" required>
														<small class="form-help">Contoh: kg, liter, gram, botol, dll.</small>
													</div>
													<div class="form-group">
														<label>Status</label>
														<div class="toggle-field">
															<input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}>
															<span>Aktif</span>
														</div>
													</div>
												</div>
												<input type="hidden" name="category" value="vitamin">
												<div class="pakvit-form__actions">
													<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i>Update</button>
												</div>
											</form>
										</div>
									</details>
									<form method="POST" action="{{ route('admin.sistem.pakanvitamin.destroy', $item) }}" class="pakvit-delete-form" data-item-name="{{ $item->name }}">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>Hapus</button>
									</form>
								</div>
							</article>
						@empty
							<div class="empty-state-small">
								<i class="fas fa-info-circle me-1"></i>Belum ada data vitamin.
							</div>
						@endforelse
					</div>
				</section>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
	const successMessage = @json(session('success'));
	const errorMessages = @json($errors->any() ? $errors->all() : []);
	const escapeHTML = (text = '') => text.replace(/[&<>"']/g, (char) => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;'
	})[char]);

	if (successMessage) {
		Swal.fire({
			icon: 'success',
			title: 'Berhasil',
			text: successMessage,
			confirmButtonColor: '#4f46e5'
		});
	}

	if (errorMessages.length) {
		const errorList = `<ul class="mb-0" style="text-align:left;">${errorMessages.map((message) => `<li>${escapeHTML(message)}</li>`).join('')}</ul>`;
		Swal.fire({
			icon: 'error',
			title: 'Terjadi kesalahan',
			html: errorList,
			confirmButtonColor: '#b91c1c'
		});
	}

	// Intercept delete submissions so SweetAlert can confirm the action first.
	document.querySelectorAll('.pakvit-delete-form').forEach((form) => {
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			const itemName = form.dataset.itemName || 'item ini';
			Swal.fire({
				title: 'Hapus item?',
				text: `Item "${itemName}" akan dihapus permanen.`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#dc2626',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Ya, hapus',
				cancelButtonText: 'Batal',
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					form.submit();
				}
			});
		});
	});
});
</script>
@endpush