@extends('admin.layouts.app')

@section('title', 'Test Error')

@push('styles')
<style>
	.test-wrap{max-width:920px;margin:0 auto;padding:32px;display:flex;flex-direction:column;gap:16px}
	.test-card{background:#fff;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(15,23,42,0.06);padding:24px}
	.test-card h1{margin:0 0 6px;font-size:1.5rem;font-weight:800;color:#0f172a}
	.test-card p{margin:0 0 12px;color:#475569}
	.btn-row{display:flex;gap:10px;flex-wrap:wrap}
	.btn-test{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-weight:700;text-decoration:none;font-size:1rem;border:1px solid transparent;transition:transform .15s ease, box-shadow .15s ease}
	.btn-test i{font-size:1.05rem}
	.btn-404{background:#111827;color:#fff;border-color:rgba(0,0,0,0.2)}
	.btn-500{background:#f59e0b;color:#111827;border-color:rgba(0,0,0,0.05)}
	.btn-test:focus-visible{outline:2px solid #2563eb;outline-offset:2px}
	.btn-test:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(15,23,42,0.08)}

	@media (max-width:720px){
		.test-wrap{padding:20px}
		.test-card{padding:18px}
	}
</style>
@endpush

@section('content')
	<div class="test-wrap">
		<div class="test-card">
			<h1>Test Error Pages</h1>
			<p>Klik tombol di bawah untuk menguji tampilan 404 atau 500 menggunakan layout yang sama.</p>
			<div class="btn-row">
				<a class="btn-test btn-404" href="{{ route('errors.test.400') }}" title="Trigger 400"><i class="fa-solid fa-circle-exclamation"></i> Trigger 400</a>
				<a class="btn-test btn-404" href="{{ route('errors.test.401') }}" title="Trigger 401"><i class="fa-solid fa-lock"></i> Trigger 401</a>
				<a class="btn-test btn-404" href="{{ route('errors.test.404') }}" title="Trigger 404"><i class="fa-solid fa-magnifying-glass-minus"></i> Trigger 404</a>
				<a class="btn-test btn-404" href="{{ route('errors.test.429') }}" title="Trigger 429"><i class="fa-solid fa-hourglass-half"></i> Trigger 429</a>
				<a class="btn-test btn-500" href="{{ route('errors.test.500') }}" title="Trigger 500"><i class="fa-solid fa-bolt"></i> Trigger 500</a>
				<a class="btn-test btn-500" href="{{ route('errors.test.502') }}" title="Trigger 502"><i class="fa-solid fa-server"></i> Trigger 502</a>
				<a class="btn-test btn-500" href="{{ route('errors.test.503') }}" title="Trigger 503"><i class="fa-solid fa-triangle-exclamation"></i> Trigger 503</a>
			</div>
		</div>
	</div>
@endsection
