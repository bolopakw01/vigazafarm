// ===== Admin Show Part Pembesaran JS =====

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Show Part Pembesaran JS initialized');

    // Accessibility helper: update aria-selected on tab change
    document.querySelectorAll('#batchTabs [data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', (e) => {
            document.querySelectorAll('#batchTabs [data-bs-toggle="tab"]').forEach(b => b.setAttribute('aria-selected', 'false'));
            e.target.setAttribute('aria-selected', 'true');
        });
    });

    // Helper: safe parse JSON
    function readLocal(key) {
        try {
            return JSON.parse(localStorage.getItem(key) || 'null');
        } catch (e) {
            return null;
        }
    }

    // Demo fallback data (used when localStorage is empty or no data from backend)
    const demoFeed = [
        { tanggal: '01/10/2025', kg: 0.4 },
        { tanggal: '02/10/2025', kg: 0.5 },
        { tanggal: '03/10/2025', kg: 0.45 },
        { tanggal: '04/10/2025', kg: 0.6 },
        { tanggal: '05/10/2025', kg: 0.55 }
    ];

    const demoMortality = [
        { tanggal: '01/10/2025', cumPct: 0 },
        { tanggal: '02/10/2025', cumPct: 0 },
        { tanggal: '03/10/2025', cumPct: 0 },
        { tanggal: '04/10/2025', cumPct: 0 },
        { tanggal: '05/10/2025', cumPct: 0 }
    ];

    const demoEnv = [
        { tanggal: '01/10/2025', temp: 28.5, hum: 65 },
        { tanggal: '02/10/2025', temp: 29.0, hum: 64 },
        { tanggal: '03/10/2025', temp: 28.0, hum: 66 },
        { tanggal: '04/10/2025', temp: 27.8, hum: 67 },
        { tanggal: '05/10/2025', temp: 28.7, hum: 65 }
    ];

    const demoWeight = [
        { umur: 1, berat: 0.4 },
        { umur: 2, berat: 0.6 },
        { umur: 3, berat: 0.9 },
        { umur: 4, berat: 1.3 },
        { umur: 5, berat: 1.8 }
    ];

    // Read from localStorage or use demo
    const feedRaw = readLocal('feedRecords');
    const feedData = Array.isArray(feedRaw) && feedRaw.length ? 
        feedRaw.map(r => ({ tanggal: r.tanggal, kg: Number(r.jumlah || r.kg || 0) })) : 
        demoFeed;

    const mortalityRaw = readLocal('mortalityRecords');
    const mortalityData = Array.isArray(mortalityRaw) && mortalityRaw.length ? 
        mortalityRaw.map(r => ({ tanggal: r.tanggal, cumPct: Number(r.cumPct || 0) })) : 
        demoMortality;

    const envRaw = readLocal('envRecords');
    const envData = Array.isArray(envRaw) && envRaw.length ? 
        envRaw.map(r => ({ tanggal: r.tanggal, temp: Number(r.temp || 0), hum: Number(r.hum || 0) })) : 
        demoEnv;

    const weightRaw = readLocal('weightRecords');
    const weightData = Array.isArray(weightRaw) && weightRaw.length ? 
        weightRaw.map(r => ({ umur: Number(r.umur || 0), berat: Number(r.berat || 0) })) : 
        demoWeight;

    // Utility: show/hide error
    function handleError(id, hasData) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = hasData ? 'none' : 'block';
    }

    // Check if ApexCharts is available
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts library not loaded');
        return;
    }

    // Create Feed chart
    const feedCategories = feedData.map(d => d.tanggal);
    const feedSeries = [{ name: 'Pakan (kg)', data: feedData.map(d => Number(d.kg || 0)) }];
    handleError('feedError', feedSeries[0].data.some(v => v > 0));

    const feedChart = new ApexCharts(document.querySelector('#chartFeed'), {
        chart: { type: 'area', height: 240, toolbar: { show: false }, zoom: { enabled: false } },
        series: feedSeries,
        xaxis: { categories: feedCategories, labels: { rotate: -45 } },
        colors: ['#0077b6'],
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { y: { formatter: v => v + ' kg' } }
    });

    // Feed analysis
    (function() {
        const el = document.getElementById('feedAnalysis');
        if (!el) return;
        const nums = feedSeries[0].data;
        if (!nums.length) {
            el.textContent = 'Tidak ada data pakan.';
            return;
        }
        const total = nums.reduce((a, b) => a + b, 0);
        const avg = (total / nums.length) || 0;
        el.textContent = `Total (periode): ${total.toFixed(2)} kg — Rata-rata/hari: ${avg.toFixed(2)} kg`;
    })();

    // Mortality chart
    const mortCategories = mortalityData.map(d => d.tanggal);
    const mortSeries = [{ name: 'Mortalitas Kumulatif (%)', data: mortalityData.map(d => Number(d.cumPct || 0)) }];
    handleError('mortalityError', mortSeries[0].data.length > 0);

    const mortalityChart = new ApexCharts(document.querySelector('#chartMortality'), {
        chart: { type: 'line', height: 240, toolbar: { show: false }, zoom: { enabled: false } },
        series: mortSeries,
        xaxis: { categories: mortCategories, labels: { rotate: -45 } },
        colors: ['#ef4444'],
        stroke: { curve: 'smooth', width: 2 },
        markers: { size: 4 },
        tooltip: { y: { formatter: v => v + '%' } }
    });

    (function() {
        const el = document.getElementById('mortalityAnalysis');
        if (!el) return;
        const nums = mortSeries[0].data;
        if (!nums.length) {
            el.textContent = 'Tidak ada data mortalitas.';
            return;
        }
        const latest = nums[nums.length - 1] || 0;
        el.textContent = `Mortalitas saat ini: ${latest.toFixed(2)}% — ${latest > 5 ? 'Perhatian: mortalitas tinggi' : 'Normal'}`;
    })();

    // Environment chart (dual axis)
    const envCategories = envData.map(d => d.tanggal);
    const envSeries = [
        { name: 'Suhu (°C)', type: 'line', data: envData.map(d => Number(d.temp || 0)) },
        { name: 'Kelembaban (%)', type: 'column', data: envData.map(d => Number(d.hum || 0)) }
    ];
    handleError('envError', envSeries.some(s => s.data.some(v => v !== 0)));

    const envChart = new ApexCharts(document.querySelector('#chartEnv'), {
        chart: { height: 240, toolbar: { show: false } },
        series: envSeries,
        stroke: { width: [3, 0] },
        xaxis: { categories: envCategories, labels: { rotate: -45 } },
        yaxis: [
            { title: { text: 'Suhu (°C)' } },
            { opposite: true, title: { text: 'Kelembaban (%)' } }
        ],
        colors: ['#f97316', '#06b6d4'],
        tooltip: { shared: true }
    });

    (function() {
        const el = document.getElementById('envAnalysis');
        if (!el) return;
        if (!envData.length) {
            el.textContent = 'Tidak ada data monitoring lingkungan.';
            return;
        }
        const temps = envSeries[0].data;
        const hums = envSeries[1].data;
        const avgT = (temps.reduce((a, b) => a + b, 0) / temps.length) || 0;
        const avgH = (hums.reduce((a, b) => a + b, 0) / hums.length) || 0;
        el.textContent = `Rata-rata suhu: ${avgT.toFixed(1)}°C — Rata-rata kelembaban: ${avgH.toFixed(0)}%`;
    })();

    // Weight chart
    const weightCategories = weightData.map(d => `umur ${d.umur}d`);
    const weightSeries = [{ name: 'Berat rata-rata (g)', data: weightData.map(d => Number(d.berat || 0)) }];
    handleError('weightError', weightSeries[0].data.some(v => v > 0));

    const weightChart = new ApexCharts(document.querySelector('#chartWeight'), {
        chart: { type: 'line', height: 240, toolbar: { show: false }, zoom: { enabled: false } },
        series: weightSeries,
        xaxis: { categories: weightCategories, labels: { rotate: -45 } },
        colors: ['#10b981'],
        stroke: { curve: 'smooth', width: 2 },
        tooltip: { y: { formatter: v => v + ' g' } }
    });

    (function() {
        const el = document.getElementById('weightAnalysis');
        if (!el) return;
        const nums = weightSeries[0].data;
        if (nums.length < 2) {
            el.textContent = 'Tidak cukup data berat untuk analisis.';
            return;
        }
        const first = nums[0], last = nums[nums.length - 1];
        const delta = last - first;
        const trend = delta > 0 ? 'naik' : (delta < 0 ? 'turun' : 'stabil');
        el.textContent = `Perubahan berat: ${delta.toFixed(2)} g (${trend}) — dari ${first.toFixed(2)} g ke ${last.toFixed(2)} g`;
    })();

    // Render all charts (catch errors individually)
    [
        { c: feedChart, id: 'feedError' },
        { c: mortalityChart, id: 'mortalityError' },
        { c: envChart, id: 'envError' },
        { c: weightChart, id: 'weightError' }
    ].forEach(item => {
        try {
            item.c.render();
        } catch (e) {
            console.error('Chart render error', e);
            handleError(item.id, false);
        }
    });

    // Reflow charts on tab open to fix sizing
    document.querySelectorAll('#batchTabs [data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', () => {
            setTimeout(() => {
                try {
                    feedChart.updateOptions({});
                    mortalityChart.updateOptions({});
                    envChart.updateOptions({});
                    weightChart.updateOptions({});
                } catch (e) {
                    console.error('Chart reflow error', e);
                }
            }, 120);
        });
    });

    // Auto-calculate total biaya for feed form
    const pakanForm = document.querySelector('#recordHarian .lopa-form-card');
    if (pakanForm) {
        const jumlahKg = pakanForm.querySelector('input[name="jumlah_kg"]');
        const hargaPerKg = pakanForm.querySelector('input[name="harga_per_kg"]');
        const totalBiaya = pakanForm.querySelector('input[name="total_biaya"]');

        function calculateTotal() {
            if (jumlahKg && hargaPerKg && totalBiaya) {
                const kg = parseFloat(jumlahKg.value) || 0;
                const harga = parseFloat(hargaPerKg.value) || 0;
                const total = kg * harga;
                totalBiaya.value = 'Rp ' + total.toLocaleString('id-ID');
            }
        }

        if (jumlahKg) jumlahKg.addEventListener('input', calculateTotal);
        if (hargaPerKg) hargaPerKg.addEventListener('input', calculateTotal);
    }

    console.log('All charts initialized successfully');
});
