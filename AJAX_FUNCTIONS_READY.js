// ===== AJAX FUNCTIONS SIAP PAKAI untuk admin-show-part-pembesaran.js =====
// Copy dan tambahkan ke file JavaScript existing

// ========== CONFIGURATION ==========
const pembesaranId = window.location.pathname.match(/\/pembesaran\/(\d+)/)?.[1];
const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || 
                           document.querySelector('input[name="_token"]')?.value;

// ========== HELPER FUNCTIONS ==========

function showToast(message, type = 'success') {
    const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b' };
    const icons = { success: '✅', error: '❌', warning: '⚠️' };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 80px; right: 20px; z-index: 9999;
        background: ${colors[type]}; color: white; padding: 16px 24px;
        border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        font-weight: 600; min-width: 250px; max-width: 400px;
    `;
    toast.textContent = `${icons[type]} ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

async function submitAjax(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        return response.ok ? result : { success: false, message: result.message || 'Terjadi kesalahan' };
    } catch (error) {
        console.error('AJAX Error:', error);
        return { success: false, message: 'Gagal menghubungi server' };
    }
}

// ========== FORM PAKAN ==========

const pakanForm = document.querySelector('form[aria-label="Form pencatatan pakan harian"]');
if (pakanForm) {
    // Auto-calculate total biaya
    const jumlahKg = pakanForm.querySelector('input[name="jumlah_kg"]');
    const hargaPerKg = pakanForm.querySelector('input[name="harga_per_kg"]');
    const totalBiaya = pakanForm.querySelector('input[name="total_biaya"]');
    
    function updateTotal() {
        const kg = parseFloat(jumlahKg?.value || 0);
        const harga = parseFloat(hargaPerKg?.value || 0);
        if (totalBiaya) totalBiaya.value = 'Rp ' + (kg * harga).toLocaleString('id-ID');
    }
    jumlahKg?.addEventListener('input', updateTotal);
    hargaPerKg?.addEventListener('input', updateTotal);
    
    // Submit handler
    pakanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/pakan`, {
            tanggal: formData.get('tanggal'),
            stok_pakan_id: formData.get('jenis_pakan'),
            jumlah_kg: formData.get('jumlah_kg'),
            jumlah_karung: formData.get('jumlah_karung') || 0
        });
        
        if (result.success) {
            showToast(result.message || 'Data pakan berhasil disimpan');
            this.reset();
            loadPakanData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM KEMATIAN ==========

const kematianForm = document.querySelector('form[aria-label="Form pencatatan kematian harian"]');
if (kematianForm) {
    kematianForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/kematian`, {
            tanggal: formData.get('tanggal'),
            jumlah: parseInt(formData.get('jumlah_ekor')),
            penyebab: formData.get('penyebab'),
            keterangan: formData.get('catatan') || ''
        });
        
        if (result.success) {
            showToast(result.message || 'Data kematian berhasil disimpan');
            this.reset();
            
            // DSS Alert untuk mortalitas tinggi
            if (result.is_high_mortality && result.alert) {
                setTimeout(() => showToast(result.alert, 'warning'), 500);
            }
            
            // Update metrics di halaman
            updateMetric('.bolopa-kai-red .bolopa-kai-value', 
                `${result.mortalitas.toFixed(2)}<small style="font-size:0.45em;">%</small>`);
            updateMetric('.bolopa-kai-red .bolopa-kai-label', 
                `Mortalitas (${result.total_mati} ekor)`);
            
            loadKematianData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM MONITORING LINGKUNGAN ==========

const monitoringForm = document.querySelector('form[aria-label="Form monitoring lingkungan"]');
if (monitoringForm) {
    monitoringForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/monitoring`, {
            tanggal: formData.get('tanggal'),
            waktu: formData.get('waktu'),
            suhu: parseFloat(formData.get('suhu')),
            kelembaban: parseFloat(formData.get('kelembaban')),
            kondisi_ventilasi: formData.get('kualitas_udara') || '',
            catatan: formData.get('catatan') || ''
        });
        
        if (result.success) {
            showToast(result.message || 'Data monitoring berhasil disimpan');
            this.reset();
            
            // DSS Alert untuk lingkungan tidak ideal
            if (result.dss_alert) {
                setTimeout(() => showToast(result.dss_alert, 'warning'), 500);
            }
            
            loadMonitoringData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM KESEHATAN & VAKSINASI ==========

const kesehatanForm = document.querySelector('form[aria-label="Form kesehatan & vaksinasi"]');
if (kesehatanForm) {
    kesehatanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/kesehatan`, {
            tanggal: formData.get('tanggal'),
            jenis_tindakan: formData.get('jenis_tindakan'),
            nama_vaksin_obat: formData.get('nama_vaksin'),
            dosis: formData.get('dosis') || '',
            petugas: formData.get('petugas') || '',
            hasil_observasi: formData.get('hasil') || '',
            catatan: formData.get('catatan') || ''
        });
        
        if (result.success) {
            showToast(result.message || 'Data kesehatan berhasil disimpan');
            this.reset();
            loadKesehatanData();
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM BERAT RATA-RATA ==========

const beratForm = document.querySelector('form[aria-label="Form pencatatan mingguan - sampling berat"]');
if (beratForm) {
    beratForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/berat`, {
            tanggal_sampling: formData.get('tanggal_sampling'),
            berat_rata: parseFloat(formData.get('berat_rata'))
        });
        
        if (result.success) {
            showToast(result.message || 'Data berat berhasil disimpan');
            this.reset();
            
            // DSS Alert untuk berat dibawah standar
            if (result.dss_alert) {
                setTimeout(() => showToast(result.dss_alert, 'warning'), 500);
            }
            
            // Update metric
            updateMetric('.bolopa-kai-green .bolopa-kai-value', 
                `${Math.round(result.berat_rata)}g`);
            
            loadBeratData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM LAPORAN HARIAN ==========

const laporanForm = document.querySelector('form[aria-label="Form laporan harian"]');
if (laporanForm) {
    laporanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const result = await submitAjax(`/admin/pembesaran/${pembesaranId}/laporan-harian`, {
            tanggal: formData.get('tanggal_laporan'),
            catatan_kejadian: formData.get('catatan') || ''
        });
        
        if (result.success) {
            showToast('Laporan harian berhasil digenerate');
            this.reset();
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== DATA LOADERS ==========

async function loadPakanData() {
    try {
        const response = await fetch(`/admin/pembesaran/${pembesaranId}/pakan/list`);
        const result = await response.json();
        
        if (result.success && result.data) {
            renderPakanChart(result.data);
            renderPakanHistory(result.data);
        }
    } catch (error) {
        console.error('Error loading pakan data:', error);
    }
}

async function loadKematianData() {
    try {
        const response = await fetch(`/admin/pembesaran/${pembesaranId}/kematian/list`);
        const result = await response.json();
        
        if (result.success && result.data) {
            renderKematianChart(result.data, result.mortalitas);
            renderKematianHistory(result.data);
        }
    } catch (error) {
        console.error('Error loading kematian data:', error);
    }
}

async function loadMonitoringData() {
    try {
        const response = await fetch(`/admin/pembesaran/${pembesaranId}/monitoring/list`);
        const result = await response.json();
        
        if (result.success && result.data) {
            renderMonitoringChart(result.data);
            renderMonitoringHistory(result.data);
        }
    } catch (error) {
        console.error('Error loading monitoring data:', error);
    }
}

async function loadBeratData() {
    // Implementasi tergantung endpoint yang tersedia
    console.log('Loading berat data...');
}

async function loadKesehatanData() {
    try {
        const response = await fetch(`/admin/pembesaran/${pembesaranId}/kesehatan/list`);
        const result = await response.json();
        
        if (result.success && result.data) {
            renderKesehatanHistory(result.data);
        }
    } catch (error) {
        console.error('Error loading kesehatan data:', error);
    }
}

// ========== CHART RENDERERS ==========

function renderPakanChart(data) {
    if (!data || !data.length) return;
    
    const categories = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const series = [{ name: 'Pakan (kg)', data: data.map(d => parseFloat(d.jumlah_kg)) }];
    
    if (window.feedChart) {
        window.feedChart.updateSeries(series);
        window.feedChart.updateOptions({ xaxis: { categories } });
    } else {
        window.feedChart = new ApexCharts(document.querySelector('#chartFeed'), {
            chart: { type: 'area', height: 240, toolbar: { show: false } },
            series: series,
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#0077b6'],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: val => val.toFixed(2) + ' kg' } }
        });
        window.feedChart.render();
    }
    
    const total = series[0].data.reduce((a, b) => a + b, 0);
    const avg = total / series[0].data.length;
    document.getElementById('feedAnalysis').textContent = 
        `Total: ${total.toFixed(2)} kg | Rata-rata: ${avg.toFixed(2)} kg/hari`;
}

function renderKematianChart(data, mortalitasPct) {
    if (!data || !data.length) return;
    
    const categories = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const series = [{ name: 'Mortalitas (%)', data: data.map((d, i) => mortalitasPct) }]; // Simplified
    
    if (window.mortalityChart) {
        window.mortalityChart.updateSeries(series);
        window.mortalityChart.updateOptions({ xaxis: { categories } });
    } else {
        window.mortalityChart = new ApexCharts(document.querySelector('#chartMortality'), {
            chart: { type: 'line', height: 240, toolbar: { show: false } },
            series: series,
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#ef4444'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: val => val.toFixed(2) + '%' } },
            yaxis: { min: 0, max: 10, title: { text: 'Mortalitas (%)' } }
        });
        window.mortalityChart.render();
    }
    
    const totalDead = data.reduce((sum, d) => sum + parseInt(d.jumlah), 0);
    document.getElementById('mortalityAnalysis').textContent = 
        `Total kematian: ${totalDead} ekor | Mortalitas: ${mortalitasPct.toFixed(2)}%`;
}

function renderMonitoringChart(data) {
    if (!data || !data.length) return;
    
    const categories = data.map(d => new Date(d.waktu_pencatatan).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }));
    const tempData = data.map(d => parseFloat(d.suhu));
    const humData = data.map(d => parseFloat(d.kelembaban));
    
    if (window.envChart) {
        window.envChart.updateSeries([
            { name: 'Suhu (°C)', data: tempData },
            { name: 'Kelembaban (%)', data: humData }
        ]);
        window.envChart.updateOptions({ xaxis: { categories } });
    } else {
        window.envChart = new ApexCharts(document.querySelector('#chartEnv'), {
            chart: { type: 'line', height: 240, toolbar: { show: false } },
            series: [
                { name: 'Suhu (°C)', data: tempData },
                { name: 'Kelembaban (%)', data: humData }
            ],
            xaxis: { categories: categories, labels: { rotate: -45 } },
            colors: ['#f59e0b', '#06b6d4'],
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            yaxis: [
                { title: { text: 'Suhu (°C)' }, min: 20, max: 35 },
                { opposite: true, title: { text: 'Kelembaban (%)' }, min: 40, max: 80 }
            ]
        });
        window.envChart.render();
    }
    
    const avgTemp = tempData.reduce((a, b) => a + b, 0) / tempData.length;
    const avgHum = humData.reduce((a, b) => a + b, 0) / humData.length;
    document.getElementById('envAnalysis').textContent = 
        `Rata-rata Suhu: ${avgTemp.toFixed(1)}°C | Kelembaban: ${avgHum.toFixed(1)}%`;
}

// ========== HISTORY RENDERERS ==========

function renderPakanHistory(data) {
    const container = document.querySelector('.note-panel.alt.lopa-note-panel.lopa-alt');
    if (!container || !data.length) return;
    
    container.innerHTML = `
        <h6>History Pakan (${data.length} terakhir)</h6>
        <table class="table table-sm">
            <thead>
                <tr><th>Tanggal</th><th>Jenis</th><th>Jumlah</th><th>Biaya</th></tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td>${new Date(d.tanggal).toLocaleDateString('id-ID')}</td>
                        <td>${d.stok_pakan?.nama_pakan || '-'}</td>
                        <td>${d.jumlah_kg} kg</td>
                        <td>Rp ${parseInt(d.total_biaya).toLocaleString('id-ID')}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderKematianHistory(data) {
    const containers = document.querySelectorAll('.note-panel.alt.lopa-note-panel.lopa-alt');
    const container = containers[1]; // Second occurrence
    if (!container || !data.length) return;
    
    container.innerHTML = `
        <h6>History Kematian (${data.length} terakhir)</h6>
        <table class="table table-sm">
            <thead>
                <tr><th>Tanggal</th><th>Jumlah</th><th>Penyebab</th></tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td>${new Date(d.tanggal).toLocaleDateString('id-ID')}</td>
                        <td>${d.jumlah} ekor</td>
                        <td>${d.penyebab}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderMonitoringHistory(data) {
    const container = document.querySelector('.note-panel.alt.mt-3.lopa-note-panel.lopa-alt');
    if (!container || !data.length) return;
    
    container.innerHTML = `
        <h6>History Monitoring (${data.length} terakhir)</h6>
        <table class="table table-sm">
            <thead>
                <tr><th>Waktu</th><th>Suhu</th><th>Kelembaban</th><th>Ventilasi</th></tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td>${new Date(d.waktu_pencatatan).toLocaleString('id-ID')}</td>
                        <td>${d.suhu}°C</td>
                        <td>${d.kelembaban}%</td>
                        <td>${d.kondisi_ventilasi || '-'}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderKesehatanHistory(data) {
    // Implement if needed
}

// ========== UPDATE METRICS ==========

function updateMetric(selector, html) {
    const el = document.querySelector(selector);
    if (el) el.innerHTML = html;
}

// ========== INITIAL LOAD ==========

// Load all data when page loads
if (pembesaranId) {
    Promise.all([
        loadPakanData(),
        loadKematianData(),
        loadMonitoringData(),
        loadBeratData(),
        loadKesehatanData()
    ]).then(() => {
        console.log('✅ All data loaded');
    }).catch(err => {
        console.error('Error loading data:', err);
    });
}

console.log('✅ AJAX functions initialized');
