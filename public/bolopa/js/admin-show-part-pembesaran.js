// ===== AJAX FUNCTIONS SIAP PAKAI untuk admin-show-part-pembesaran.js =====
// Copy dan tambahkan ke file JavaScript existing

// ========== CONFIGURATION ==========
// Get config from blade template (with fallback to auto-detect)
const config = window.vigazaConfig || {
    baseUrl: window.location.origin + window.location.pathname.split('/admin')[0],
    pembesaranId: window.location.pathname.match(/\/pembesaran\/(\d+)/)?.[1],
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
};

const pembesaranId = config.pembesaranId;
const baseUrl = config.baseUrl;
const getCsrfToken = () => config.csrfToken || 
                           document.querySelector('meta[name="csrf-token"]')?.content || 
                           document.querySelector('input[name="_token"]')?.value;

// Normalize date-like values to YYYY-MM-DD for reliable comparisons
function toDateKey(input) {
    if (!input) return '';
    // If already ISO-like yyyy-mm-dd
    const isoMatch = String(input).match(/^(\d{4}-\d{2}-\d{2})/);
    if (isoMatch) return isoMatch[1];

    // If in dd/mm/yyyy or dd-mm-yyyy
    const dmMatch = String(input).match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})/);
    if (dmMatch) return `${dmMatch[3]}-${dmMatch[2]}-${dmMatch[1]}`;

    // Fallback: try Date parse
    const d = new Date(input);
    if (!isNaN(d)) return d.toISOString().slice(0,10);

    // As last resort, return original string
    return String(input);
}

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
            credentials: 'same-origin', // CRITICAL: Send cookies with request
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
    // Auto-calculate total biaya & auto-fill harga from dropdown
    const stokPakanSelect = pakanForm.querySelector('select[name="stok_pakan_id"]');
    const jumlahKg = pakanForm.querySelector('input[name="jumlah_kg"]');
    const hargaPerKg = pakanForm.querySelector('input[name="harga_per_kg"]');
    const totalBiaya = pakanForm.querySelector('input[name="total_biaya"]');
    
    // Auto-fill harga when pakan selected
    stokPakanSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga') || 0;
        if (hargaPerKg) hargaPerKg.value = harga;
        updateTotal();
    });
    
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
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/pakan`, {
            tanggal: formData.get('tanggal'),
            stok_pakan_id: parseInt(formData.get('stok_pakan_id')),
            jumlah_kg: parseFloat(formData.get('jumlah_kg')),
            jumlah_karung: parseInt(formData.get('jumlah_karung')) || 0
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
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/kematian`, {
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
        const tanggal = formData.get('tanggal');
        const waktu = formData.get('waktu');
        
        // Gabungkan tanggal dan waktu menjadi datetime
        const waktu_pencatatan = `${tanggal} ${waktu}:00`;
        
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/monitoring`, {
            waktu_pencatatan: waktu_pencatatan,
            suhu: parseFloat(formData.get('suhu')),
            kelembaban: parseFloat(formData.get('kelembaban')),
            intensitas_cahaya: formData.get('intensitas_cahaya') ? parseFloat(formData.get('intensitas_cahaya')) : null,
            kondisi_ventilasi: formData.get('kondisi_ventilasi') || null,
            catatan: formData.get('catatan') || null
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
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/kesehatan`, {
            tanggal: formData.get('tanggal'),
            tipe_kegiatan: formData.get('tipe_kegiatan'),
            nama_vaksin_obat: formData.get('nama_vaksin_obat'),
            jumlah_burung: parseInt(formData.get('jumlah_burung')),
            gejala: formData.get('gejala') || null,
            diagnosa: formData.get('diagnosa') || null,
            tindakan: formData.get('tindakan') || null,
            biaya: formData.get('biaya') ? parseFloat(formData.get('biaya')) : null,
            petugas: formData.get('petugas') || null
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
        const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/berat`, {
            umur_hari: parseInt(formData.get('umur_hari')),
            berat_rata_rata: parseFloat(formData.get('berat_rata_rata'))
        });
        
        if (result.success) {
            showToast(result.message || 'Data berat berhasil disimpan');
            this.reset();
            
            // Show status berat
            if (result.status && result.status.message) {
                setTimeout(() => {
                    const badgeClass = result.status.badge === 'warning' ? 'warning' : 'success';
                    showToast(`${result.status.message}`, badgeClass);
                }, 500);
            }
            
            // Update metric if available
            if (result.data && result.data.berat_rata_rata) {
                updateMetric('.bolopa-kai-green .bolopa-kai-value', 
                    `${Math.round(result.data.berat_rata_rata)}g`);
            }
            
            loadBeratData(); // Reload chart
        } else {
            showToast(result.message, 'error');
        }
    });
}

// ========== FORM LAPORAN HARIAN ==========

// Auto-generate catatan button handler
const btnGenerateCatatan = document.getElementById('btn-generate-catatan');
if (btnGenerateCatatan) {
    btnGenerateCatatan.addEventListener('click', async function() {
        const tanggalLaporan = document.getElementById('tanggal_laporan').value;
        if (!tanggalLaporan) {
            showToast('Pilih tanggal laporan terlebih dahulu', 'warning');
            return;
        }
        
        // Show loading state
        const btnText = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
        
        try {
            // Check whether a laporan for this date already exists (to warn user)
            const laporanListResp = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, { credentials: 'same-origin' });
            const laporanList = await laporanListResp.json();
                const keyTanggal = toDateKey(tanggalLaporan);
                const existsForDate = (laporanList.data || []).some(l => toDateKey(l.tanggal) === keyTanggal);
            if (existsForDate) {
                const confirmAdd = await Swal.fire({
                    title: 'Laporan sudah ada untuk tanggal ini',
                    html: 'Terdapat laporan untuk tanggal yang Anda pilih. Apakah Anda yakin ingin menambahkan laporan baru untuk tanggal yang sama?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambahkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d'
                });

                if (!confirmAdd.isConfirmed) {
                    this.disabled = false;
                    this.innerHTML = btnText;
                    return;
                }
            }
            // Fetch data pakan dan kematian untuk tanggal tersebut
            const [pakanResponse, kematianResponse] = await Promise.all([
                fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/pakan/list`, {
                    credentials: 'same-origin'
                }),
                fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/kematian/list`, {
                    credentials: 'same-origin'
                })
            ]);
            
            const pakanResult = await pakanResponse.json();
            const kematianResult = await kematianResponse.json();
            
            // Filter data by tanggal
                const pakanHariIni = (pakanResult.data || []).filter(p => toDateKey(p.tanggal) === keyTanggal);
                const kematianHariIni = (kematianResult.data || []).filter(k => toDateKey(k.tanggal) === keyTanggal);
            
            // Generate catatan otomatis
            let catatan = `LAPORAN HARIAN - ${new Date(tanggalLaporan).toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            })}\n\n`;
            
            // Section Pakan
            catatan += '🌾 PEMBERIAN PAKAN:\n';
            if (pakanHariIni.length > 0) {
                const totalPakan = pakanHariIni.reduce((sum, p) => sum + parseFloat(p.jumlah_kg), 0);
                const totalKarung = pakanHariIni.reduce((sum, p) => sum + parseInt(p.jumlah_karung || 0), 0);
                
                catatan += `- Total pakan diberikan: ${totalPakan.toFixed(2)} kg`;
                if (totalKarung > 0) catatan += ` (${totalKarung} karung)`;
                catatan += '\n';
                
                // Detail per jenis pakan
                pakanHariIni.forEach(p => {
                    catatan += `  • ${p.nama_pakan || 'Pakan'}: ${parseFloat(p.jumlah_kg).toFixed(2)} kg\n`;
                });
            } else {
                catatan += '- Belum ada data pemberian pakan hari ini\n';
            }
            
            catatan += '\n';
            
            // Section Kematian
            catatan += '💀 MORTALITAS:\n';
            if (kematianHariIni.length > 0) {
                const totalMati = kematianHariIni.reduce((sum, k) => sum + parseInt(k.jumlah), 0);
                catatan += `- Total kematian: ${totalMati} ekor\n`;
                
                // Group by penyebab
                const groupedByPenyebab = kematianHariIni.reduce((acc, k) => {
                    const penyebab = k.penyebab || 'Tidak diketahui';
                    acc[penyebab] = (acc[penyebab] || 0) + parseInt(k.jumlah);
                    return acc;
                }, {});
                
                catatan += '- Penyebab:\n';
                Object.entries(groupedByPenyebab).forEach(([penyebab, jumlah]) => {
                    catatan += `  • ${penyebab}: ${jumlah} ekor\n`;
                });
                
                // Tingkat mortalitas (jika ada data populasi)
                if (kematianResult.mortalitas) {
                    catatan += `- Tingkat mortalitas: ${parseFloat(kematianResult.mortalitas).toFixed(2)}%\n`;
                }
            } else {
                catatan += '- Tidak ada kematian hari ini ✅\n';
            }
            
            catatan += '\n';
            
            // Section Kesimpulan
            catatan += '📋 KESIMPULAN:\n';
            if (pakanHariIni.length === 0 && kematianHariIni.length === 0) {
                catatan += '- Belum ada aktivitas tercatat untuk hari ini\n';
            } else {
                if (kematianHariIni.length === 0) {
                    catatan += '- Kondisi populasi stabil, tidak ada mortalitas\n';
                } else {
                    const totalMati = kematianHariIni.reduce((sum, k) => sum + parseInt(k.jumlah), 0);
                    if (totalMati > 10) {
                        catatan += '- PERHATIAN: Tingkat mortalitas tinggi, perlu investigasi lebih lanjut\n';
                    } else {
                        catatan += '- Mortalitas dalam batas normal\n';
                    }
                }
                
                if (pakanHariIni.length > 0) {
                    catatan += '- Pemberian pakan berjalan sesuai jadwal\n';
                }
            }
            
            catatan += '\n---\n';
            catatan += 'Catatan tambahan: (Silakan edit jika perlu)';
            
            // Set ke textarea
            document.getElementById('catatan_laporan').value = catatan;
            showToast('Catatan berhasil di-generate! Silakan sesuaikan jika perlu.', 'success');
            
        } catch (error) {
            console.error('Error generating catatan:', error);
            showToast('Gagal generate catatan: ' + error.message, 'error');
        } finally {
            // Restore button
            this.disabled = false;
            this.innerHTML = btnText;
        }
    });
}

const laporanForm = document.querySelector('form[aria-label="Form laporan harian"]');
if (laporanForm) {
    laporanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const catatan = formData.get('catatan');
        
        // Validasi catatan tidak boleh kosong
        if (!catatan || catatan.trim().length === 0) {
            showToast('Catatan laporan tidak boleh kosong. Klik "Generate Catatan" atau isi manual.', 'warning');
            return;
        }
        
        // Check if laporan already exists for this date
        try {
            const tanggalLaporan = formData.get('tanggal_laporan');
                const keyTanggal = toDateKey(tanggalLaporan);
                let laporanListData = window._laporanCache;
                if (!laporanListData) {
                    const resp = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, { credentials: 'same-origin' });
                    const listResult = await resp.json();
                    laporanListData = listResult.data || [];
                    window._laporanCache = laporanListData;
                }
                const exists = (laporanListData || []).some(l => toDateKey(l.tanggal) === keyTanggal);

            if (exists) {
                // Inform user that laporan for this date already exists
                await Swal.fire({
                    icon: 'info',
                    title: 'Sudah tercatat',
                    html: 'Anda sudah melakukan pencatatan laporan harian untuk tanggal ini. Jika ingin mengubah catatan, buka laporan tersebut melalui tombol <strong>Detail</strong>.',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Proceed to submit if not exists
            const result = await submitAjax(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian`, {
                tanggal: tanggalLaporan,
                catatan_kejadian: catatan
            });

            if (result.success) {
                // Server may respond with already_exists:true to indicate a pre-existing record
                if (result.already_exists) {
                    // Inform user it's already recorded and show message from server
                    await Swal.fire({
                        icon: 'info',
                        title: 'Sudah tercatat',
                        html: result.message || 'Anda sudah melakukan pencatatan laporan harian untuk tanggal ini.',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#0d6efd'
                    });
                    // Refresh client-side cache and history to reflect server state
                    window._laporanCache = window._laporanCache || [];
                    loadLaporanData();
                } else {
                    showToast('Laporan harian berhasil disimpan');
                    this.reset();
                    loadLaporanData(); // Reload history
                }
            } else {
                showToast(result.message, 'error');
            }
        } catch (err) {
            console.error('Error checking existing laporan:', err);
            showToast('Gagal memeriksa laporan tersedia. Silakan coba lagi.', 'error');
        }
    });
}

// ========== DATA LOADERS ==========

async function loadPakanData() {
    try {
        console.log('📊 Loading pakan data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/pakan/list`, {
            credentials: 'same-origin' // Send cookies
        });
        console.log('📊 Pakan response status:', response.status);
        const result = await response.json();
        console.log('📊 Pakan result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering pakan data, count:', result.data.length);
            renderPakanChart(result.data);
            renderPakanHistory(result.data);
        } else {
            console.warn('📊 Pakan data not successful or empty');
            // Still try to render with empty state
            renderPakanHistory([]);
        }
    } catch (error) {
        console.error('❌ Error loading pakan data:', error);
        renderPakanHistory([]);
    }
}

async function loadKematianData() {
    try {
        console.log('📊 Loading kematian data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/kematian/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Kematian response status:', response.status);
        const result = await response.json();
        console.log('📊 Kematian result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering kematian data, count:', result.data.length);
            renderKematianChart(result.data, result.mortalitas);
            renderKematianHistory(result.data);
        } else {
            console.warn('📊 Kematian data not successful or empty');
            renderKematianHistory([]);
        }
    } catch (error) {
        console.error('❌ Error loading kematian data:', error);
        renderKematianHistory([]);
    }
}

async function loadLaporanData() {
    try {
        console.log('📊 Loading laporan data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Laporan response status:', response.status);
        const result = await response.json();
        console.log('📊 Laporan result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering laporan data, count:', result.data.length);
            // Cache laporan list for quick client-side checks (avoid duplicate submissions)
            window._laporanCache = result.data || [];
            renderLaporanHistory(result.data);
        } else {
            console.warn('📊 Laporan data not successful or empty');
            window._laporanCache = [];
            renderLaporanHistory([]);
        }
    } catch (error) {
        console.error('❌ Error loading laporan data:', error);
        renderLaporanHistory([]);
    }
}

async function loadMonitoringData() {
    try {
        console.log('📊 Loading monitoring data...');
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/monitoring/list`, {
            credentials: 'same-origin'
        });
        console.log('📊 Monitoring response status:', response.status);
        const result = await response.json();
        console.log('📊 Monitoring result:', result);
        
        if (result.success && result.data) {
            console.log('📊 Rendering monitoring data, count:', result.data.length);
            renderMonitoringChart(result.data);
            renderMonitoringHistory(result.data);
        } else {
            console.warn('📊 Monitoring data not successful or empty');
            renderMonitoringHistory([]);
        }
    } catch (error) {
        console.error('❌ Error loading monitoring data:', error);
        renderMonitoringHistory([]);
    }
}

async function loadBeratData() {
    // Implementasi tergantung endpoint yang tersedia
    console.log('Loading berat data...');
}

async function loadKesehatanData() {
    try {
        const response = await fetch(`${baseUrl}/admin/pembesaran/${pembesaranId}/kesehatan/list`, {
            credentials: 'same-origin' // Send cookies
        });
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
    const container = document.getElementById('pakan-history-content');
    console.log('🔍 renderPakanHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #pakan-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No pakan data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data pakan</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'pakan records');
    console.log('📝 Sample record:', data[0]);
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:25%" class="text-start">Tanggal</th>
                    <th style="width:35%" class="text-start">Jenis Pakan</th>
                    <th style="width:20%" class="text-end">Jumlah</th>
                    <th style="width:20%" class="text-end">Biaya</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td class="text-start">${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})}</td>
                        <td class="text-start"><small>${d.stok_pakan?.nama_pakan || '-'}</small></td>
                        <td class="text-end">${parseFloat(d.jumlah_kg).toFixed(2)} kg</td>
                        <td class="text-end"><small>Rp ${parseInt(d.total_biaya).toLocaleString('id-ID')}</small></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderKematianHistory(data) {
    const container = document.getElementById('kematian-history-content');
    console.log('🔍 renderKematianHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #kematian-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No kematian data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data kematian</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'kematian records');
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:30%" class="text-start">Tanggal</th>
                    <th style="width:25%" class="text-end">Jumlah</th>
                    <th style="width:45%" class="text-start">Penyebab</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td class="text-start">${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})}</td>
                        <td class="text-end">${d.jumlah} ekor</td>
                        <td class="text-start"><small>${d.penyebab || '-'}</small></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderMonitoringHistory(data) {
    const container = document.getElementById('monitoring-history-content');
    console.log('🔍 renderMonitoringHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #monitoring-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No monitoring data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada data monitoring</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'monitoring records');
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:30%">Waktu</th>
                    <th style="width:20%" class="text-end">Suhu</th>
                    <th style="width:25%" class="text-end">Kelembaban</th>
                    <th style="width:25%">Ventilasi</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map(d => `
                    <tr>
                        <td><small>${new Date(d.waktu_pencatatan || d.tanggal).toLocaleString('id-ID', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'})}</small></td>
                        <td class="text-end">${parseFloat(d.suhu).toFixed(1)}°C</td>
                        <td class="text-end">${parseFloat(d.kelembaban).toFixed(1)}%</td>
                        <td><small>${d.kondisi_ventilasi || '-'}</small></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
    `;
}

function renderLaporanHistory(data) {
    const container = document.getElementById('laporan-history-content');
    console.log('🔍 renderLaporanHistory called, container found:', !!container, 'data count:', data?.length || 0);
    
    if (!container) {
        console.error('❌ Container #laporan-history-content not found!');
        return;
    }
    
    if (!data || data.length === 0) {
        console.log('ℹ️ No laporan data to display');
        container.innerHTML = '<p class="text-muted small mb-0">Belum ada laporan harian</p>';
        return;
    }
    
    console.log('✅ Rendering', data.length, 'laporan records');
    console.log('📝 Sample laporan record:', data[0]);
    
    // Get base URL for detail page
    const baseUrl = window.vigazaConfig?.baseUrl || '';
    const pembesaranId = window.vigazaConfig?.pembesaranId || '';
    
    container.innerHTML = `
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:18%" class="text-start">Tanggal</th>
                    <th style="width:13%" class="text-end">Populasi</th>
                    <th style="width:13%" class="text-end">Pakan (kg)</th>
                    <th style="width:10%" class="text-end">Mati</th>
                    <th style="width:33%" class="text-start">Catatan</th>
                    <th style="width:13%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                ${data.slice(0, 10).map((d) => `
                    <tr>
                        <td class="text-start"><small>${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</small></td>
                        <td class="text-end">${parseInt(d.jumlah_burung || 0).toLocaleString('id-ID')}</td>
                        <td class="text-end">${parseFloat(d.konsumsi_pakan_kg || 0).toFixed(2)}</td>
                        <td class="text-end">${parseInt(d.jumlah_kematian || 0)}</td>
                        <td class="text-start"><small class="text-muted">${(d.catatan_kejadian || '-').substring(0, 35)}${(d.catatan_kejadian || '').length > 35 ? '...' : ''}</small></td>
                        <td class="text-center">
                            <a href="${baseUrl}/admin/pembesaran/${pembesaranId}/laporan-harian/${d.id}"
                               class="btn btn-sm btn-outline-primary text-decoration-none"
                               aria-label="Detail Laporan"
                               style="text-decoration:none">
                                Detail
                            </a>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        ${data.length > 10 ? `<p class="text-muted small mt-2 mb-0 text-center">Menampilkan 10 dari ${data.length} data</p>` : ''}
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
        loadLaporanData(),
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
