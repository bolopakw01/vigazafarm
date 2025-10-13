@extends('admin.layouts.app')

@section('title', 'Produksi - Daftar Produksi')

@push('styles')
<link rel="stylesheet" href="{{ asset('bolopa/css/admin-produksi.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">
	<div class="bolopa-page-header mb-3">
		<div>
			<h5 class="bolopa-page-title">Produksi</h5>
			<div class="bolopa-page-subtitle">Rekapitulasi produksi dan pengiriman telur</div>
		</div>
		<div class="bolopa-header-action">
			<a href="{{ route('admin.produksi.create') }}" class="btn btn-primary btn-sm">
				<i class="fa-solid fa-plus"></i> Tambah Produksi
			</a>
		</div>
	</div>

	{{-- KAI Summary --}}
	<div class="row g-3 mb-4">
		<div class="col-lg-3 col-sm-6">
			<div class="prod-kai-card prod-kai-blue">
				<div class="value">{{ number_format($totalTelur ?? 0) }}</div>
				<div class="label">Total Telur (pcs)</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="prod-kai-card prod-kai-green">
				<div class="value">{{ number_format($rataTelurPerHari ?? 0, 2) }}</div>
				<div class="label">Rata-rata / Hari</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="prod-kai-card prod-kai-indigo">
				<div class="value">Rp {{ number_format($pendapatan ?? 0, 0, ',', '.') }}</div>
				<div class="label">Perkiraan Pendapatan</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="prod-kai-card prod-kai-yellow">
				<div class="value">{{ number_format($lostRate ?? 0, 2) }}%</div>
				<div class="label">Loss / Reject</div>
			</div>
		</div>
	</div>

	{{-- Controls & Filters --}}
	<div class="card mb-3 p-3">
		<div class="d-flex gap-2 flex-wrap align-items-center">
			<div>
				<label class="small">Periode</label>
				<input type="date" id="fromDate" class="form-control form-control-sm" />
			</div>
			<div>
				<label class="small">s/d</label>
				<input type="date" id="toDate" class="form-control form-control-sm" />
			</div>
			<div>
				<label class="small">Kandang</label>
				<select id="kandangFilter" class="form-select form-select-sm">
					<option value="">Semua</option>
					@foreach($kandangList ?? [] as $k)
						<option value="{{ $k->id }}">{{ $k->nama_kandang }}</option>
					@endforeach
				</select>
			</div>

			<div class="ms-auto d-flex gap-2">
				<button class="btn btn-outline-secondary btn-sm" id="btnExport">Export CSV</button>
				<button class="btn btn-outline-primary btn-sm" id="btnPrint" onclick="window.print()">Print</button>
			</div>
		</div>
	</div>

	{{-- Table Produksi --}}
	<div class="card mb-4">
		<div class="card-body p-2">
			<div class="table-responsive">
				<table class="table table-sm table-hover mb-0" id="tableProduksi">
					<thead>
						<tr>
							<th style="width:60px">No</th>
							<th>Tanggal</th>
							<th>Kandang</th>
							<th>Batch</th>
							<th class="text-end">Telur (pcs)</th>
							<th class="text-end">Berat Rata-rata (g)</th>
							<th class="text-end">Harga / pcs</th>
							<th class="text-end">Total (Rp)</th>
							<th style="width:120px">Aksi</th>
						</tr>
					</thead>
					<tbody>
						@forelse($produksi as $i => $row)
						<tr>
							<td>{{ $produksi->firstItem() + $i }}</td>
							<td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
							<td>{{ $row->kandang->nama_kandang ?? '-' }}</td>
							<td>{{ $row->batch_produksi_id ?? '-' }}</td>
							<td class="text-end">{{ number_format($row->jumlah_telur) }}</td>
							<td class="text-end">{{ $row->berat_rata_rata ? number_format($row->berat_rata_rata, 2) : '-' }}</td>
							<td class="text-end">{{ number_format($row->harga_per_pcs ?? 0, 0, ',', '.') }}</td>
							<td class="text-end">{{ number_format(($row->jumlah_telur * ($row->harga_per_pcs ?? 0)), 0, ',', '.') }}</td>
							<td>
								<a href="{{ route('admin.produksi.show', $row->id) }}" class="btn btn-sm btn-info">Detail</a>
								<a href="{{ route('admin.produksi.edit', $row->id) }}" class="btn btn-sm btn-warning">Edit</a>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="9" class="text-center py-5">Tidak ada data produksi</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<div class="card-footer d-flex justify-content-between align-items-center">
			<div>Menampilkan {{ $produksi->firstItem() ?? 0 }} - {{ $produksi->lastItem() ?? 0 }} dari {{ $produksi->total() ?? 0 }} entri</div>
			<div>{{ $produksi->links() }}</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('btnExport')?.addEventListener('click', function() { alert('Export CSV belum diimplementasikan'); });
});
</script>
@endpush