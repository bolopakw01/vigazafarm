@extends('admin.layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@push('styles')
<style>
	.vf-404-wrapper {
		display: flex;
		align-items: center;
		justify-content: center;
		min-height: 62vh;
		padding: 48px 16px;
	}

	.vf-404-card {
		max-width: 920px;
		width: 100%;
		background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
		border: 1px solid #e6eef8;
		border-radius: 18px;
		padding: 36px;
		box-shadow: 0 10px 30px rgba(15,23,42,0.06);
		display: flex;
		gap: 24px;
		align-items: center;
	}

	.vf-404-illustration {
		flex: 0 0 220px;
		height: 160px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 12px;
		background: linear-gradient(135deg, #eef2ff 0%, #e9d5ff 100%);
		box-shadow: inset 0 -6px 18px rgba(99,102,241,0.06);
		font-weight: 800;
		color: #111827;
		font-size: 44px;
	}

	.vf-404-body {
		flex: 1 1 auto;
	}

	.vf-404-title {
		font-size: 28px;
		font-weight: 800;
		margin: 0 0 6px 0;
		color: #0f172a;
	}

	.vf-404-sub {
		margin: 0 0 16px 0;
		color: #475569;
		font-size: 15px;
	}

	.vf-404-actions { display:flex; gap:12px; flex-wrap:wrap; }

	.vf-btn-primary {
		background: #0f172a;
		color: #fff;
		padding: 10px 16px;
		border-radius: 10px;
		text-decoration: none;
		font-weight: 700;
		display: inline-flex;
		align-items: center;
		gap: 8px;
	}

	.vf-btn-secondary {
		background: transparent;
		color: #0f172a;
		padding: 10px 14px;
		border-radius: 10px;
		text-decoration: none;
		border: 1px solid #e6eef8;
		font-weight: 600;
	}

	@media (max-width: 760px) {
		.vf-404-card { flex-direction: column; align-items: stretch; }
		.vf-404-illustration { width: 100%; height: 120px; font-size: 36px; }
	}
</style>
@endpush

@section('content')
	<div class="vf-404-wrapper">
		<div class="vf-404-static" aria-hidden="true"></div>
		<div class="vf-404-card">
			<div class="vf-404-illustration">404</div>
			<div class="vf-404-body">
				<h1 class="vf-404-title">Halaman tidak ditemukan</h1>
				<p class="vf-404-sub">Maaf, halaman yang Anda cari tidak tersedia atau mungkin telah dipindahkan. Periksa kembali URL atau kembali ke dashboard.</p>

				<div class="vf-404-actions">
					<a href="{{ route('admin.dashboard') }}" class="vf-btn-primary"><i class="fa-solid fa-house"></i> Kembali ke Dashboard</a>
					<a href="{{ url('/') }}" class="vf-btn-secondary"><i class="fa-solid fa-arrow-left"></i> Beranda</a>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('styles')
<style>
/* TV no-signal static background animation */
.vf-404-wrapper { position: relative; overflow: hidden; }
.vf-404-static { position: absolute; inset: 0; z-index: 0; pointer-events: none; opacity: 0.18; mix-blend-mode: screen; }
.vf-404-static::before,
.vf-404-static::after {
	content: '';
	position: absolute; inset: 0;
	background-repeat: repeat;
	will-change: background-position, opacity;
}
.vf-404-static::before {
	background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 2px), radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 2px);
	background-size: 3px 3px, 7px 7px;
	animation: staticNoise 0.9s steps(6) infinite;
	opacity: 0.9;
	filter: grayscale(100%) contrast(140%) brightness(120%);
}
.vf-404-static::after {
	background-image: linear-gradient(90deg, rgba(255,255,255,0.02) 0%, rgba(0,0,0,0.02) 50%, rgba(255,255,255,0.02) 100%);
	background-size: 100% 4px;
	animation: scanline 3s linear infinite;
	opacity: 0.6;
	mix-blend-mode: overlay;
}

@keyframes staticNoise {
	0% { background-position: 0 0, 0 0; }
	100% { background-position: 200% 200%, -200% -200%; }
}

@keyframes scanline {
	0% { transform: translateY(-10%); opacity: 0.25; }
	50% { transform: translateY(10%); opacity: 0.6; }
	100% { transform: translateY(-10%); opacity: 0.25; }
}

/* Slight flicker on the card to mimic old TV */
.vf-404-card { z-index: 1; animation: cardFlicker 6s infinite; }
@keyframes cardFlicker {
	0% { filter: none; }
	2% { filter: brightness(0.96); }
	3% { filter: brightness(1.02); }
	7% { filter: brightness(0.98); }
	100% { filter: none; }
}
</style>
@endpush

