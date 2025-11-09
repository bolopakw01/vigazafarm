@extends('admin.layouts.app')

@section('title', 'Backoffice')

@push('styles')
	<link rel="stylesheet" href="{{ asset('bolopa/css/admin-dashboard.css') }}">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endpush

@section('content')

	<div class="container-app container">
		<!-- Header Card -->
		{{-- <div class="app-card section-gap header-with-rt">
			<div class="header-rt" role="button" tabindex="0" title="Refresh page" aria-label="Refresh page">
				<img src="{{ asset('bolopa/img/icon/line-md--speed-loop.svg') }}" class="header-rt-img" alt="Refresh Icon">
			</div>
			<div class="card-head header-left">
				<img src="{{ asset('bolopa/img/icon/ic--outline-dashboard.svg') }}" class="header-icon" width="64" height="64" alt="Dashboard Icon">
				<div class="meta">
					<div class="title-row">
						<h1 class="card-title mb-0">Dashboard</h1>
						<span id="statusDot" class="status-dot status-green" title="Click to change status"></span>
					</div>
					<div style="font-size:.95rem;color:#6b7280;margin-top:2px;">
						<div>vigazafarm — peternakan burung buyuh</div>
					</div>
				</div>
			</div>
		</div> --}}

		<!-- MINI KPI / MENU CARDS -->
		<div class="app-card section-gap">
			<div class="box-body">
				<div class="row g-3">
					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-yellow" style="cursor:pointer;">
							<div class="mini-value" data-target="{{ \App\Models\Penetasan::count() }}">0</div>
							<div class="mini-label">Penetasan</div>
							<i class="fa-solid fa-egg mini-icon" aria-hidden="true"></i>
						</div>
					</div>

					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-green" style="cursor:pointer;">
							<div class="mini-value" data-target="{{ \App\Models\Pembesaran::count() }}">0</div>
							<div class="mini-label">Pembesaran</div>
							<i class="fa-solid fa-seedling mini-icon" aria-hidden="true"></i>
						</div>
					</div>

					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-blue" style="cursor:pointer;">
							<div class="mini-value" data-target="{{ \App\Models\Produksi::count() }}">0</div>
							<div class="mini-label">Produksi</div>
							<i class="fa-solid fa-industry mini-icon" aria-hidden="true"></i>
						</div>
					</div>

					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-indigo" style="cursor:pointer;">
							<div class="mini-value" data-target="{{ \App\Models\Kandang::count() }}">0</div>
							<div class="mini-label">Kandang</div>
							<i class="fa-solid fa-warehouse mini-icon" aria-hidden="true"></i>
						</div>
					</div>

					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-red" style="cursor:pointer;">
							<div class="mini-value" data-target="{{ \App\Models\User::count() }}">0</div>
							<div class="mini-label">User</div>
							<i class="fa-solid fa-user mini-icon" aria-hidden="true"></i>
						</div>
					</div>
					<div class="col-6 col-md-4 col-lg-2">
						<div class="mini-kpi-card theme-red" style="cursor:pointer;">
							<div class="mini-value" data-target="12500">0</div>
							<div class="mini-label">Cost</div>
							<i class="fa-solid fa-coins mini-icon" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="app-card section-gap">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center">
					<h2 class="card-title mb-0">Activity</h2>
					<small class="text-muted">Main chart & goals</small>
				</div>
			</div>

			<div class="box-body">
				<div class="row g-4 align-items-start">
					<div class="col-12 col-lg-8">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<div class="segmented" id="chartFilter">
								<button class="seg-btn is-active" data-filter="bulan">Bulan</button>
								<button class="seg-btn" data-filter="tahun">Tahun</button>
								<button class="seg-btn" data-filter="hari">Hari</button>
							</div>
						</div>
						<div id="mainChart"></div>
					</div>

					<div class="col-12 col-lg-4">
						<div class="subtitle">Goals</div>

						<div id="goalsList" class="goals-panel">
							<div class="goal-block">
								<div class="d-flex justify-content-between align-items-center mb-2">
										<span class="goal-item">Produksi</span>
										<div class="d-flex align-items-center">
											<small class="text-muted me-2 goal-text" data-current="160" data-target="200"><span class="current">0</span> / <span class="target">200</span></small>
										</div>
								</div>
									<div class="progress">
										<div class="progress-bar bg-primary" style="width:0%"></div>
									</div>
							</div>

							<div class="goal-block">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="goal-item">Penetasan</span>
									<div class="d-flex align-items-center">
										<small class="text-muted me-2 goal-text" data-current="310" data-target="400"><span class="current">0</span> / <span class="target">400</span></small>
									</div>
								</div>
								<div class="progress">
									<div class="progress-bar bg-danger" style="width:0%"></div>
								</div>
							</div>

							<div class="goal-block">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="goal-item">Pembesaran</span>
									<div class="d-flex align-items-center">
										<small class="text-muted me-2 goal-text" data-current="480" data-target="800"><span class="current">0</span> / <span class="target">800</span></small>
									</div>
								</div>
								<div class="progress">
									<div class="progress-bar bg-success" style="width:0%"></div>
								</div>
							</div>

							<div class="goal-block mb-0">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="goal-item">User</span>
									<div class="d-flex align-items-center">
										<small class="text-muted me-2 goal-text" data-current="250" data-target="500"><span class="current">0</span> / <span class="target">500</span></small>
									</div>
								</div>
								<div class="progress">
									<div class="progress-bar bg-warning" style="width:0%"></div>
								</div>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>

		<!-- NEW KPI CARD (separate) -->
		<div class="app-card section-gap kpi-card">
			<div class="box-body" style="padding: 12px 18px;">
				<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
					<div class="col">
						<div class="kpi up">
							<div class="delta"><img src="{{ asset('bolopa/img/icon/line-md--hazard-lights-filled-loop.svg') }}" class="kpi-icon" alt="KPI Icon"><span class="kpi-delta" data-target="17">0</span>%</div>
							<div class="value"><span class="kpi-value" data-currency="$" data-target="35210">0</span></div>
							<div class="label">PRODUKSI</div>
						</div>
					</div>
					<div class="col">
						<div class="kpi left">
							<div class="delta"><img src="{{ asset('bolopa/img/icon/line-md--hazard-lights-filled-loop.svg') }}" class="kpi-icon" alt="KPI Icon"><span class="kpi-delta" data-target="0">0</span>%</div>
							<div class="value"><span class="kpi-value" data-currency="$" data-target="10390">0</span></div>
							<div class="label">PENETASAN</div>
						</div>
					</div>
					<div class="col">
						<div class="kpi up">
							<div class="delta"><img src="{{ asset('bolopa/img/icon/line-md--hazard-lights-filled-loop.svg') }}" class="kpi-icon" alt="KPI Icon"><span class="kpi-delta" data-target="18">0</span>%</div>
							<div class="value"><span class="kpi-value" data-currency="$" data-target="24813">0</span></div>
							<div class="label">PEMBESARAN</div>
						</div>
					</div>
					<div class="col">
						<div class="kpi down">
							<div class="delta"><img src="{{ asset('bolopa/img/icon/line-md--hazard-lights-filled-loop.svg') }}" class="kpi-icon" alt="KPI Icon"><span class="kpi-delta" data-target="20">0</span>%</div>
							<div class="value"><span class="kpi-value" data-target="1200">0</span></div>
							<div class="label">OTHER</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- SECTION 2: Table Produksi + Radar Chart -->
		<div class="row g-4">
			<div class="col-12 col-lg-6">
				<div class="app-card h-100 section-gap">
					<div class="card-head">
						<div class="w-100 d-flex justify-content-between align-items-center">
							<h2 class="card-title mb-0">Produksi</h2>
							<div class="d-flex gap-2">
								<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-filter me-2"></i>Filter</button>
								<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
							</div>
						</div>
					</div>
					<div class="table-wrap">
						<div class="table-responsive">
							<table class="table align-middle">
								<thead>
									<tr>
										<th class="text-start">Tanggal</th>
										<th class="text-start">Kandang</th>
										<th class="text-start">Jenis</th>
										<th class="text-start">Jumlah</th>
										<th class="text-start">Status</th>
									</tr>
								</thead>
								<tbody>
									@foreach(\App\Models\Produksi::latest()->take(6)->get() as $row)
									<tr>
										<td>{{ optional($row->created_at)->format('d/m/Y') }}</td>
										<td>{{ $row->kandang?->nama ?? '—' }}</td>
										<td>{{ $row->jenis ?? 'Telur' }}</td>
										<td>{{ number_format($row->jumlah ?? 0,0,',','.') }}</td>
										<td><span class="badge badge-pill badge-selesai">Selesai</span></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<p class="hint ms-1">
							Data Produksi Terbaru.
							<a href="#" class="text-decoration-none" style="color:#0b74da;">Lihat Semua</a>
						</p>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6">
				<div class="app-card h-100 section-gap">
					<div class="card-head">
						<div class="w-100 d-flex justify-content-between align-items-center">
							<h2 class="chart-title mb-0">Performance</h2>
							<div class="chart-menu"><i class="fa-solid fa-list"></i></div>
						</div>
					</div>
					<div class="box-body d-flex align-items-center" style="height: calc(100% - 80px);">
						<div id="radarChart" style="width: 100%;"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- Penetasan Table Card -->
		<div class="row g-4">
			<div class="col-12">
				<div class="app-card section-gap mt-penetasan">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center">
					<h2 class="card-title mb-0">Penetasan</h2>
					<div class="d-flex gap-2">
						<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-filter me-2"></i>Filter</button>
						<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="table-wrap penetasan-table">
					<div class="table-responsive">
						<table class="table align-middle">
							<thead>
								<tr>
									<th class="text-start">Tanggal</th>
									<th class="text-start">Kandang</th>
									<th class="text-start">Batch</th>
									<th class="text-start">Jumlah Telur</th>
									<th class="text-start">Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach(\App\Models\Penetasan::latest()->take(6)->get() as $r)
								<tr>
									<td>{{ optional($r->created_at)->format('d/m/Y') }}</td>
									<td>{{ $r->kandang?->nama ?? '—' }}</td>
									<td>{{ $r->batch ?? '—' }}</td>
									<td>{{ number_format($r->jumlah_telur ?? 0,0,',','.') }}</td>
									<td><span class="badge badge-pill badge-aktif">Aktif</span></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<p class="hint ms-1">Data penetasan terbaru. <a href="#" class="text-decoration-none" style="color:#0b74da;">Lihat Semua</a></p>
				</div>
			</div>
				</div>
			</div>
		</div>

		<!-- Pembesaran Table Card -->
		<div class="row g-4">
			<div class="col-12">
				<div class="app-card section-gap mt-penetasan">
			<div class="card-head">
				<div class="w-100 d-flex justify-content-between align-items-center">
					<h2 class="card-title mb-0">Pembesaran</h2>
					<div class="d-flex gap-2">
						<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-filter me-2"></i>Filter</button>
						<button class="btn btn-sm rounded-pill px-3 border"><i class="fa-solid fa-up-right-from-square me-2"></i>Export</button>
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="table-wrap penetasan-table">
					<div class="table-responsive">
						<table class="table align-middle">
							<thead>
								<tr>
									<th class="text-start">Tanggal</th>
									<th class="text-start">Kandang</th>
									<th class="text-start">Jenis</th>
									<th class="text-start">Jumlah</th>
									<th class="text-start">Keterangan</th>
								</tr>
							</thead>
							<tbody>
								@foreach(\App\Models\Pembesaran::latest()->take(6)->get() as $pr)
								<tr>
									<td>{{ optional($pr->created_at)->format('d/m/Y') }}</td>
									<td>{{ $pr->kandang?->nama ?? '—' }}</td>
									<td>{{ $pr->jenis ?? '—' }}</td>
									<td>{{ number_format($pr->jumlah ?? 0,0,',','.') }}</td>
									<td><span class="badge badge-pill badge-aktif">Aktif</span></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<p class="hint ms-1">Data pembesaran terbaru. <a href="#" class="text-decoration-none" style="color:#0b74da;">Lihat Semua</a></p>
				</div>
			</div>
				</div>
			</div>
		</div>

	</div>

@endsection

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script>
		// replicate mainChart + filters and radar chart + export menu and animations from lopadashboard.html
		(function(){
			// main chart sample data
			const mainData = {
				labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
				series: [
					{ name: 'Produksi', type: 'column', data: [230,180,240,200,260,300,320,290,310,330,360,380] },
					{ name: 'Penetasan', type: 'area', data: [150,160,140,180,170,200,210,220,230,240,250,260] },
					{ name: 'Pembesaran', type: 'line', data: [120,140,160,150,170,180,200,210,220,230,240,260] }
				]
			};

			const mainOpts = {
				series: mainData.series,
				chart: { type: 'line', height: 340, stacked: false, toolbar: { show:false }, foreColor: '#6c757d' },
				colors: ['#0d6efd','#20c997','#ffc107'],
				labels: mainData.labels,
				stroke: { width: [0,2,4], curve: 'smooth' },
				fill: { opacity: [1,0.25,1], gradient: { shadeIntensity: 1, opacityFrom: .7, opacityTo: .2, stops: [0,90,100] } },
				plotOptions: { bar: { columnWidth: '48%' } },
				dataLabels: { enabled: false },
				markers: { size: 0 },
				grid: { borderColor: '#dee2e6' },
				xaxis: { labels: { style: { colors: '#495057' } }, axisBorder: { color: '#dee2e6' }, axisTicks: { color: '#dee2e6' } },
				yaxis: { title: { text: 'Jumlah' }, labels: { style: { colors: '#495057' } } },
				tooltip: { shared: true, intersect: false },
				legend: { position: 'bottom', horizontalAlign: 'center', offsetY: 8, markers: { radius: 12 }, itemMargin: { horizontal: 14, vertical: 6 } },
				responsive: [ { breakpoint: 992, options: { chart: { height: 320 }, legend: { itemMargin: { vertical: 4 } } } }, { breakpoint: 576, options: { chart: { height: 300 }, plotOptions: { bar: { columnWidth: '55%' } } } } ]
			};

			const mainChart = new ApexCharts(document.querySelector('#mainChart'), mainOpts);
			mainChart.render();

			const chartFilter = document.getElementById('chartFilter');
			const filterData = {
				bulan: mainData,
				tahun: { labels: ['2020','2021','2022','2023','2024','2025'], series: [ { name:'Produksi', type:'column', data:[1200,1400,1600,1800,2000,2200] }, { name:'Penetasan', type:'area', data:[800,900,1000,1100,1200,1300] }, { name:'Pembesaran', type:'line', data:[600,700,800,900,1000,1100] } ] },
				hari: { labels: Array.from({length:31},(_,i)=>String(i+1)), series: [ { name:'Produksi', type:'column', data:Array.from({length:31},()=>Math.floor(Math.random()*100)+50) }, { name:'Penetasan', type:'area', data:Array.from({length:31},()=>Math.floor(Math.random()*80)+30) }, { name:'Pembesaran', type:'line', data:Array.from({length:31},()=>Math.floor(Math.random()*60)+20) } ] }
			};

			chartFilter?.addEventListener('click', (e)=>{
				if(e.target.classList.contains('seg-btn')){
					chartFilter.querySelectorAll('.seg-btn').forEach(b=>b.classList.remove('is-active'));
					e.target.classList.add('is-active');
					const f = e.target.getAttribute('data-filter');
					const newOpts = { ...mainOpts, series: filterData[f].series, labels: filterData[f].labels };
					mainChart.updateOptions(newOpts);
				}
			});

			// radar chart
			const radarOptions = {
				chart: { type: 'radar', height: '100%', toolbar: { show:false }, foreColor: '#6c757d', parentHeightOffset: 0 },
				series: [ { name: 'Sales', data: [80,50,30,40,100,20] }, { name: 'Income', data: [20,30,40,80,20,80] }, { name: 'Expense', data: [44,76,78,13,43,10] } ],
				labels: ['Jan','Feb','Mar','Apr','May','Jun'],
				colors: ['#198754','#0d6efd','#ffc107'],
				stroke: { width: 2 }, fill: { opacity: 0.3 }, markers: { size: 4 }, dataLabels: { enabled: false }, yaxis: { show: false }, grid: { show: false },
				legend: { position: 'bottom', horizontalAlign: 'center', fontWeight: 700, fontSize: '14px', markers: { width: 14, height: 14, radius: 12 }, itemMargin: { horizontal: 16, vertical: 8 }, offsetY: 8 },
				xaxis: { labels: { style: { fontSize: '13px', fontWeight: 600, colors: '#495057' } } },
				plotOptions: { radar: { size: 180, polygons: { strokeColors: '#e9ecef', connectorColors: '#e9ecef', strokeWidth: 1 } } },
				responsive: [ { breakpoint: 992, options: { plotOptions: { radar: { size: 150 } } } }, { breakpoint: 576, options: { plotOptions: { radar: { size: 120 } }, legend: { itemMargin: { horizontal: 12, vertical: 6 } } } } ]
			};
			const radarChart = new ApexCharts(document.querySelector('#radarChart'), radarOptions);
			radarChart.render();

			// chart export menu
			(function(){
				const menu = document.querySelector('.chart-menu'); if(!menu) return; menu.setAttribute('role','button'); menu.setAttribute('tabindex','0'); menu.setAttribute('title','Export chart');
				const parent = menu.parentElement || document.body; if(getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
				const box = document.createElement('div'); box.className = 'chart-export-box'; Object.assign(box.style, { position:'absolute', right:'0', top:'36px', background:'#fff', border:'1px solid #e6e9ee', borderRadius:'6px', boxShadow:'0 8px 20px rgba(15,23,42,0.06)', padding:'6px', zIndex:9999, display:'none', minWidth:'160px' });
				box.innerHTML = `
					<button class="chart-export-btn" data-type="png" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Download PNG</button>
					<button class="chart-export-btn" data-type="svg" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Download SVG</button>
					<button class="chart-export-btn" data-type="csv" style="display:block;width:100%;text-align:left;padding:8px;border:0;background:transparent;cursor:pointer">Export CSV</button>
				`;
				parent.appendChild(box);
				function toggle(){ box.style.display = box.style.display === 'none' ? 'block' : 'none'; }
				function closeBox(){ box.style.display = 'none'; }
				document.addEventListener('click', (e)=>{ if(!box.contains(e.target) && e.target !== menu) closeBox(); });
				menu.addEventListener('click', function(e){ e.stopPropagation(); toggle(); });
				menu.addEventListener('keydown', function(e){ if(e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar'){ e.preventDefault(); toggle(); } });
				function downloadHref(href, filename){ const a = document.createElement('a'); a.href = href; a.download = filename; document.body.appendChild(a); a.click(); a.remove(); }
				function downloadBlob(blob, filename){ const url = URL.createObjectURL(blob); downloadHref(url, filename); setTimeout(()=> URL.revokeObjectURL(url), 15000); }
				box.addEventListener('click', function(e){ const btn = e.target.closest('.chart-export-btn'); if(!btn) return; const type = btn.getAttribute('data-type'); closeBox(); if(type === 'png'){ if(typeof radarChart?.dataURI === 'function'){ radarChart.dataURI().then(({imgURI})=> downloadHref(imgURI, 'performance-chart.png')).catch(()=> alert('Unable to export PNG')); } else alert('Chart export not available'); } else if(type === 'svg'){ const svgEl = document.querySelector('#radarChart svg'); if(!svgEl){ alert('SVG not found'); return; } const svgText = svgEl.outerHTML; const blob = new Blob([svgText], {type: 'image/svg+xml;charset=utf-8'}); downloadBlob(blob, 'performance-chart.svg'); } else if(type === 'csv'){ try{ const labels = (typeof radarOptions !== 'undefined' && radarOptions.labels) || []; const series = (typeof radarOptions !== 'undefined' && radarOptions.series) || []; let csv = ['label'].concat(series.map(s=> '"'+s.name.replace(/"/g,'""')+'"')).join(',') + '\n'; for(let i=0;i<labels.length;i++){ const row = [ '"'+String(labels[i]).replace(/"/g,'""')+'"' ]; series.forEach(s=> row.push(s.data[i] != null ? s.data[i] : '')); csv += row.join(',') + '\n'; } const blob = new Blob([csv], {type: 'text/csv;charset=utf-8'}); downloadBlob(blob, 'performance-chart.csv'); }catch(err){ console.error(err); alert('Unable to export CSV'); } } });
			})();

			// header-rt refresh
			(function(){ const btn = document.querySelector('.header-rt'); if(!btn) return; btn.addEventListener('click', ()=> location.reload()); btn.addEventListener('keydown', (e)=>{ if(e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') { e.preventDefault(); location.reload(); } }); })();

			// status dot cycling pulse
			(function(){ const dot = document.getElementById('statusDot'); if(!dot) return; const classes = ['status-green','status-yellow','status-red']; let idx = classes.findIndex(c=> dot.classList.contains(c)); if(idx < 0) idx = 0; setTimeout(()=>{ idx = (idx + 1) % classes.length; classes.forEach(c=> dot.classList.remove(c)); dot.classList.add(classes[idx]); dot.classList.add('status-pulse-short'); setTimeout(()=> dot.classList.remove('status-pulse-short'), 380); }, 300); setInterval(()=>{ idx = (idx + 1) % classes.length; classes.forEach(c=> dot.classList.remove(c)); dot.classList.add(classes[idx]); dot.classList.add('status-pulse-short'); setTimeout(()=> dot.classList.remove('status-pulse-short'), 380); }, 2000); })();

			// animate goals and mini-kpi and kpi numbers (same as original)
			(function(){
				function animateNumeric(el, start, end, duration){ const range = end - start; let startTime = null; function step(timestamp){ if(!startTime) startTime = timestamp; const progress = Math.min((timestamp - startTime) / duration, 1); const value = Math.floor(start + range * progress); el.textContent = value; if(progress < 1) requestAnimationFrame(step); else el.textContent = end; } requestAnimationFrame(step); }
				function formatNumber(n){ return n.toLocaleString('id-ID'); }
				function animateValue(el, start, end, duration, isCurrency){ const range = end - start; let startTime = null; function step(timestamp){ if(!startTime) startTime = timestamp; const progress = Math.min((timestamp - startTime) / duration, 1); const value = Math.floor(start + range * progress); el.textContent = isCurrency ? ('Rp ' + formatNumber(value)) : formatNumber(value); if(progress < 1) window.requestAnimationFrame(step); else el.textContent = isCurrency ? ('Rp ' + formatNumber(end)) : formatNumber(end); } window.requestAnimationFrame(step); }
				function animate(el, start, end, dur, isCurrency, currency){ const range = end - start; let s=null; function step(t){ if(!s) s=t; const p=Math.min((t-s)/dur,1); const v=Math.floor(start + range*p); el.textContent = isCurrency ? (currency + v.toLocaleString('en-US')) : v.toLocaleString('en-US'); if(p<1) requestAnimationFrame(step); else el.textContent = isCurrency ? (currency + end.toLocaleString('en-US')) : end.toLocaleString('en-US'); } requestAnimationFrame(step); }
				document.addEventListener('DOMContentLoaded', function(){
					const goals = document.querySelectorAll('.goal-text'); goals.forEach((textEl, i) => { const current = parseFloat(textEl.getAttribute('data-current')) || 0; const target = parseFloat(textEl.getAttribute('data-target')) || 0; const pct = target > 0 ? Math.max(0, Math.min(100, (current / target) * 100)) : 0; const currentSpan = textEl.querySelector('.current'); const bar = textEl.closest('.goal-block').querySelector('.progress-bar'); const delay = i * 160; setTimeout(()=>{ animateNumeric(currentSpan, 0, current, 900); bar.style.width = pct + '%'; }, delay); });
					const items = document.querySelectorAll('.mini-kpi-card .mini-value[data-target]'); items.forEach((el, idx) => { const target = parseInt(el.getAttribute('data-target')) || 0; const isCurrency = el.closest('.mini-kpi-card').querySelector('.mini-label').textContent.trim().toLowerCase() === 'cost'; const delay = idx * 120; setTimeout(()=> animateValue(el, 0, target, 900, isCurrency), delay); });
					const deltas = document.querySelectorAll('.kpi-delta'); deltas.forEach((d, i)=>{ const target = parseInt(d.getAttribute('data-target')) || 0; setTimeout(()=> animate(d, 0, target, 700, false), i*120); });
					const values = document.querySelectorAll('.kpi-value'); values.forEach((v, i)=>{ const target = parseInt(v.getAttribute('data-target')) || 0; const currency = v.getAttribute('data-currency') || ''; setTimeout(()=> animate(v, 0, target, 900, currency !== '', currency), i*140); });
				});
			})();

		})();
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

