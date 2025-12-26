@extends('admin.layouts.app')

@section('title', '400')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
<style>
	html,body{height:100%;margin:0}
	.wrap{width:100%;max-width:900px;padding:2rem;display:flex;align-items:center;justify-content:center}
	.wrap.split-wrap{max-width:none;width:100%;padding:2.5rem 1rem}
	.card{width:min(92%,720px);height:420px;border-radius:14px;position:relative;overflow:hidden;box-shadow:none;border:1px solid rgba(255,255,255,0.03);background:#000;font-family:Montserrat,system-ui,sans-serif}

	.split-card{width:100%;display:flex;gap:0;border-radius:14px;overflow:hidden;border:1px solid rgba(0,0,0,0.06);background:#fff;position:relative}
	.split-card::before{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;border-radius:14px;background:linear-gradient(90deg,#f6efe0 0%,#f0e6c8 25%,#eedbb3 45%,#ece8dd 70%,#f6efe0 100%);opacity:1;mix-blend-mode:normal;transform:translateZ(0);background-size:220% 120%;animation:splitBgMove 10s ease-in-out infinite}
	.split-card::after{content:"";position:absolute;inset:0;z-index:0;pointer-events:none;border-radius:14px;background-image:radial-gradient(circle at 18% 22%, rgba(0,0,0,0.05) 0 2px, transparent 3px), radial-gradient(circle at 72% 38%, rgba(0,0,0,0.05) 0 3px, transparent 4px), radial-gradient(circle at 52% 74%, rgba(0,0,0,0.04) 0 2px, transparent 3px);opacity:0.78;mix-blend-mode:multiply}
	@keyframes splitBgMove{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}

	.split-left,.split-right{position:relative;z-index:1}
	.split-left{flex:1 1 60%;padding:1.25rem;background:transparent;display:flex;align-items:center;justify-content:center}
	.split-right{flex:1 1 40%;background:#fff;color:#111;padding:32px;display:flex;align-items:center}
	.right-content{max-width:520px}
	.right-content .meta{display:inline-flex;align-items:center;font-size:.85rem;color:#6b7280;margin-bottom:0;font-weight:700;letter-spacing:.08em;text-transform:uppercase;line-height:1}
	.right-content .meta-wrap{display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.5rem}
	.right-content .meta-icon{width:28px;height:28px;display:block;object-fit:contain;border-radius:0;background:transparent;margin:0;padding:0}
	.right-content h2{margin:0 0 .5rem 0;font-size:1.75rem;line-height:1.12;font-weight:800;font-family:Poppins,system-ui,sans-serif;color:#0f172a}
	.right-content p{margin:0;color:#475569;font-size:1rem;line-height:1.6}
	.right-content .actions{margin-top:1rem;display:flex;gap:.5rem;flex-wrap:wrap}
	.right-content .btn{display:inline-flex;align-items:center;gap:.6rem;padding:.6rem .85rem;border-radius:.6rem;font-weight:700;text-decoration:none;font-size:.95rem}
	.right-content .btn-primary{background:#0f172a;color:#fff;border:1px solid rgba(0,0,0,0.2)}
	.right-content .btn-secondary{background:transparent;color:#0f172a;border:1px solid rgba(15,23,42,0.08)}
	.right-content .btn:focus{outline:none;box-shadow:0 6px 18px rgba(15,23,42,0.06)}

	@media(max-width:900px){.split-card{flex-direction:column}.split-right{padding:18px}.split-left{padding:12px}}

	.noise_bg{position:absolute;inset:0;background-color:#000;background-image:repeating-radial-gradient(#000 0 0.0001%,#ffffff 0 0.0002%),repeating-conic-gradient(#000 0 0.0001%,#ffffff 0 0.0002%);background-size:2500px 2500px,2500px 2500px;background-position:50% 0,60% 50%;background-blend-mode:difference;filter:contrast(120%) brightness(85%);opacity:1;z-index:0}
	@keyframes noiseMove{from{background-position:50% 0,60% 50%}to{background-position:52% 2%,58% 48%}}
	.noise_bg{animation:noiseMove 8s linear infinite alternate}
	.big400{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:900;color:#ffffff;opacity:0.12;font-size:clamp(12rem,18vw,32rem);letter-spacing:0.02em;z-index:1;pointer-events:none;text-shadow:0 2px 8px rgba(0,0,0,0.45);font-family:Anton,sans-serif;text-transform:uppercase}
	.overlay{position:relative;z-index:2;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#ffffff;text-align:center;padding:1.25rem;gap:.6rem}
	.overlay h1{font-size:2.5rem;letter-spacing:0.02em;margin:0;font-weight:800}
	.overlay p{font-size:1.5rem;margin:0;opacity:0.95;font-weight:700;text-shadow:0 3px 10px rgba(0,0,0,0.55)}
	.badge{display:inline-block;background:#000;color:#fff;padding:.5rem .9rem;border-radius:.6rem;font-weight:800;box-shadow:0 6px 20px rgba(0,0,0,0.45);border:1px solid rgba(255,255,255,0.02);font-family:Montserrat,system-ui,sans-serif;text-transform:uppercase;letter-spacing:.08em}
	.overlay h1 .badge{font-size:1.6rem}
	.card:before{content:"";position:absolute;inset:0;padding:1px;border-radius:14px;pointer-events:none;box-shadow:none}
	@media(max-width:480px){.card{height:300px}.overlay h1{font-size:1.25rem}.overlay p{font-size:1rem}.big400{font-size:8rem;opacity:0.12}}
	@media(max-width:480px){
		.noise_bg{background:linear-gradient(to right,#002fc6 0%,#002bb2 14.2857%,#3a3a3a 14.2857%,#303030 28.5714%,#ff0afe 28.5714%,#f500f4 42.8571%,#6c6c6c 42.8571%,#626262 57.1429%,#0affd9 57.1429%,#00f5ce 71.4286%,#3a3a3a 71.4286%,#303030 85.7143%,white 85.7143%,#fafafa 100%);background-size:100% 100%;animation:none;filter:none;opacity:1;mix-blend-mode:normal;border-radius:10px;border:2px solid black;display:flex;align-items:center;justify-content:center;overflow:hidden;z-index:0;color:#252525;font-weight:bold;letter-spacing:.15em}
		.noise_bg::before{content:"";position:absolute;left:0;top:0;width:100%;height:68.47826%;background:linear-gradient(to right,white 0%,#fafafa 14.2857%,#ffe60a 14.2857%,#f5dc00 28.5714%,#0affd9 28.5714%,#00f5ce 42.8571%,#10ea00 42.8571%,#0ed600 57.1429%,#ff0afe 57.1429%,#f500f4 71.4286%,#ed0014 71.4286%,#d90012 85.7143%,#002fc6 85.7143%,#002bb2 100%);z-index:1;pointer-events:none;mix-blend-mode:overlay;opacity:0.95}
		.noise_bg::after{content:"";position:absolute;left:0;bottom:0;width:100%;height:21.73913%;background:linear-gradient(to right,#006c6b 0%,#005857 16.6667%,white 16.6667%,#fafafa 33.3333%,#001b75 33.3333%,#001761 50%,#6c6c6c 50%,#626262 66.6667%,#929292 66.6667%,#888888 83.3333%,#3a3a3a 83.3333%,#303030 100%);z-index:1;pointer-events:none;mix-blend-mode:overlay;opacity:0.9}
		.big400{opacity:0.08}
		.badge{background:rgba(0,0,0,0.75)}
		.overlay .badge{background:#000;padding-left:.3em;padding-right:.3em;font-size:.75rem;color:#fff;border-radius:5px;letter-spacing:0}
	}
</style>
<style>
	.noise_bg.no-signal{background:linear-gradient(to right,#002fc6 0%,#002bb2 14.2857%,#3a3a3a 14.2857%,#303030 28.5714%,#ff0afe 28.5714%,#f500f4 42.8571%,#6c6c6c 42.8571%,#626262 57.1429%,#0affd9 57.1429%,#00f5ce 71.4286%,#3a3a3a 71.4286%,#303030 85.7143%,white 85.7143%,#fafafa 100%);background-size:100% 100%;animation:none !important;filter:none !important;mix-blend-mode:normal !important;opacity:1 !important;transition:background .6s ease,opacity .6s ease,filter .6s ease}
	.noise_bg.no-signal::before{content:"";position:absolute;left:0;top:0;width:100%;height:68.47826%;background:linear-gradient(to right,white 0%,#fafafa 14.2857%,#ffe60a 14.2857%,#f5dc00 28.5714%,#0affd9 28.5714%,#00f5ce 42.8571%,#10ea00 42.8571%,#0ed600 57.1429%,#ff0afe 57.1429%,#f500f4 71.4286%,#ed0014 71.4286%,#d90012 85.7143%,#002fc6 85.7143%,#002bb2 100%);z-index:1;pointer-events:none;mix-blend-mode:overlay;opacity:0.95}
	.noise_bg.no-signal::after{content:"";position:absolute;left:0;bottom:0;width:100%;height:21.73913%;background:linear-gradient(to right,#006c6b 0%,#005857 16.6667%,white 16.6667%,#fafafa 33.3333%,#001b75 33.3333%,#001761 50%,#6c6c6c 50%,#626262 66.6667%,#929292 66.6667%,#888888 83.3333%,#3a3a3a 83.3333%,#303030 100%);z-index:1;pointer-events:none;mix-blend-mode:overlay;opacity:0.9}
</style>
@endpush

@section('content')
  <div class="wrap split-wrap">
    <div class="split-card" role="group" aria-label="400 split card">
      <div class="split-left">
        <div class="card" role="region" aria-label="Bad Request 400">
          <div class="noise_bg" aria-hidden="true"></div>
          <div class="big400" aria-hidden="true">400</div>
					<div class="overlay">
						<h1><span class="badge">BAD REQUEST</span></h1>
						<p>Alamatnya miring, telur datanya belum utuh.</p>
					</div>
        </div>
      </div>

      <div class="split-right" role="region" aria-label="400 info panel">
        <div class="right-content">
          <div class="meta-wrap">
            <img src="{{ asset('bolopa/img/icon/vigazafarm logo.svg') }}" alt="Vigaza Farm" class="meta-icon" />
            <span class="meta">Vigaza Farm — Puyuh &amp; Telur</span>
          </div>
		  <h2>Permintaan Kurang Pas</h2>
		  <p>URL atau data yang dikirim belum lengkap—ibarat telur belum menetas. Cek lagi, lengkapi, lalu kirim ulang. Kalau masih gagal, kabari admin.</p>
          <div class="actions">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" title="Kembali ke Dashboard"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="javascript:location.reload()" class="btn btn-secondary" title="Muat ulang halaman"><i class="fa-solid fa-rotate"></i> Muat Ulang</a>
          </div>
        </div>
      </div>
    </div>
  </div>

	<script>
		(function(){
			const DELAY_MS = 3500;
			document.addEventListener('DOMContentLoaded', function(){
				const noise = document.querySelector('.noise_bg');
				if(!noise) return;
				noise.style.transition = 'filter 400ms ease, opacity 500ms ease, background-position 400ms linear';
				setTimeout(() => {
					noise.classList.add('no-signal');
					noise.style.animation = 'none';
				}, DELAY_MS);
			});
		})();
	</script>
@endsection
